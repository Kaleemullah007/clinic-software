<?php

namespace App\Http\Controllers;

use App\Models\AppointmentReturn;
use App\Models\AppointmentProduct;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\DamagedProduct;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('returns.view');

        if ($request->ajax()) {
            $query = AppointmentReturn::with('appointment', 'product', 'processedBy')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_name', function (AppointmentReturn $r) {
                    return e($r->appointment->appointment_id ?? ('Appt #' . $r->appointment_id));
                })
                ->addColumn('product_name', function (AppointmentReturn $r) {
                    return e($r->product->name ?? '—');
                })
                ->addColumn('refund_amount_fmt', function (AppointmentReturn $r) {
                    return $r->refund_amount ? 'PKR ' . number_format($r->refund_amount, 2) : '—';
                })
                ->addColumn('date', function (AppointmentReturn $r) {
                    return optional($r->created_at)?->format('d M Y') ?? '—';
                })
                ->addColumn('action', function (AppointmentReturn $r) {
                    $view = '<a href="' . route('returns.show', $r->id) . '" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>';
                    $del  = auth()->user()->can('returns.delete')
                        ? '<form action="' . route('returns.destroy', $r->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete?\')">'
                          . csrf_field() . method_field('DELETE')
                          . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>'
                        : '';
                    return $view . $del;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.returns.index');
    }

    public function create()
    {
        $this->authorize('returns.create');
        return view('admin.returns.create');
    }

    public function store(Request $request)
    {
        $this->authorize('returns.create');
        $data = $request->validate([
            'appointment_product_id' => 'required|exists:appointment_products,id',
            'quantity'               => 'required|numeric|min:0.01',
            'refund_amount'          => 'nullable|numeric|min:0',
            'return_to'              => 'required|in:inventory,damaged',
            'reason'                 => 'nullable|string',
        ]);

        $apProd = AppointmentProduct::findOrFail($data['appointment_product_id']);

        $return = AppointmentReturn::create([
            ...$data,
            'appointment_id' => $apProd->appointment_id,
            'product_id'     => $apProd->product_id,
            'variation_id'   => $apProd->variation_id,
            'processed_by'   => auth()->id(),
        ]);

        // Update inventory
        if ($apProd->product_id) {
            $qty = $data['return_to'] === 'inventory' ? $data['quantity'] : -$data['quantity'];

            InventoryMovement::create([
                'product_id'     => $apProd->product_id,
                'variation_id'   => $apProd->variation_id,
                'type'           => 'return',
                'quantity'       => $data['return_to'] === 'inventory' ? $data['quantity'] : 0,
                'reference_type' => 'appointment_return',
                'reference_id'   => $return->id,
                'created_by'     => auth()->id(),
                'notes'          => $data['reason'] ?? null,
            ]);

            $inv = Inventory::firstOrNew(['product_id' => $apProd->product_id, 'variation_id' => $apProd->variation_id]);
            if ($data['return_to'] === 'inventory') {
                $inv->quantity = ($inv->quantity ?? 0) + $data['quantity'];
            }
            $inv->save();

            if ($data['return_to'] === 'damaged') {
                DamagedProduct::create([
                    'product_id'     => $apProd->product_id,
                    'variation_id'   => $apProd->variation_id,
                    'quantity'       => $data['quantity'],
                    'reason'         => $data['reason'] ?? 'Returned from appointment',
                    'reference_type' => 'appointment_return',
                    'reference_id'   => $return->id,
                    'reported_by'    => auth()->id(),
                ]);
            }
        }

        return redirect()->route('returns.index')->with('success', 'Return processed successfully.');
    }

    public function show(AppointmentReturn $return)
    {
        $this->authorize('returns.view');
        $return->load('appointment', 'appointmentProduct.product', 'product', 'processedBy');
        return view('admin.returns.show', compact('return'));
    }

    public function destroy(AppointmentReturn $return)
    {
        $this->authorize('returns.delete');
        $return->delete();
        return redirect()->route('returns.index')->with('success', 'Return record deleted.');
    }
}
