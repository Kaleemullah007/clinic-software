<?php

namespace App\Http\Controllers;

use App\Models\AppointmentReturn;
use App\Models\AppointmentProduct;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\DamagedProduct;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $this->authorize('returns.view');
        $returns = AppointmentReturn::with('appointment', 'product', 'processedBy')->latest()->get();
        return view('admin.returns.index', compact('returns'));
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
