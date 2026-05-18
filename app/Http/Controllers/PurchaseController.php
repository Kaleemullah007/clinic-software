<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\Vendor;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('purchases.view');

        if ($request->ajax()) {
            $query = Purchase::with(['vendor:id,name', 'creator:id,name'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('vendor_col', fn (Purchase $p) =>
                    $p->vendor ? e($p->vendor->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('total_col', fn (Purchase $p) =>
                    'PKR ' . number_format($p->total_amount ?? $p->net_amount ?? 0, 2)
                )
                ->addColumn('payment_col', function (Purchase $p) {
                    $map = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                    $status = $p->payment_status ?? 'unpaid';
                    $cls = $map[$status] ?? 'secondary';
                    return '<span class="badge bg-' . $cls . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('creator_col', fn (Purchase $p) =>
                    $p->creator ? e($p->creator->name) : '<span class="text-muted">—</span>'
                )
                ->addColumn('action', function (Purchase $p) {
                    $view = '<a href="' . route('purchases.show', $p->id) . '" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>';
                    $del = auth()->user()->can('purchases.delete')
                        ? '<form action="' . route('purchases.destroy', $p->id) . '" method="POST" class="d-inline"
                               onsubmit="return confirm(\'Delete?\')">
                               ' . csrf_field() . method_field('DELETE') . '
                               <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                           </form>'
                        : '';
                    return $view . $del;
                })
                ->rawColumns(['vendor_col', 'payment_col', 'creator_col', 'action'])
                ->make(true);
        }

        return view('admin.purchases.index');
    }

    public function create()
    {
        $this->authorize('purchases.create');
        $vendors  = Vendor::where('status', 1)->get(['id','name','company']);
        $products = Product::where('status', 1)->with('variations')->get(['id','name','price','has_variations']);
        $pendingPRs = PurchaseRequest::where('status', 'approved')->get(['id','pr_number']);
        return view('admin.purchases.create', compact('vendors', 'products', 'pendingPRs'));
    }

    public function store(Request $request)
    {
        $this->authorize('purchases.create');
        $data = $request->validate([
            'vendor_id'           => 'nullable|exists:vendors,id',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'purchase_date'       => 'required|date',
            'discount'            => 'nullable|numeric|min:0',
            'payment_status'      => 'required|in:unpaid,partial,paid',
            'paid_amount'         => 'nullable|numeric|min:0',
            'notes'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.variation_id'=> 'nullable|exists:product_variations,id',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'items.*.selling_price'=> 'nullable|numeric|min:0',
        ]);

        $totalAmount = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_cost']);
        $discount    = $data['discount'] ?? 0;

        $purchase = Purchase::create([
            'purchase_number'     => Purchase::generateNumber(),
            'vendor_id'           => $data['vendor_id'] ?? null,
            'purchase_request_id' => $data['purchase_request_id'] ?? null,
            'created_by'          => auth()->id(),
            'purchase_date'       => $data['purchase_date'],
            'total_amount'        => $totalAmount,
            'discount'            => $discount,
            'net_amount'          => $totalAmount - $discount,
            'payment_status'      => $data['payment_status'],
            'paid_amount'         => $data['paid_amount'] ?? 0,
            'notes'               => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $item['total_cost'] = $item['quantity'] * $item['unit_cost'];
            $purchase->items()->create($item);

            // Update inventory
            InventoryMovement::create([
                'product_id'     => $item['product_id'],
                'variation_id'   => $item['variation_id'] ?? null,
                'type'           => 'purchase',
                'quantity'       => $item['quantity'],
                'unit_price'     => $item['unit_cost'],
                'reference_type' => 'purchase',
                'reference_id'   => $purchase->id,
                'vendor_id'      => $data['vendor_id'] ?? null,
                'created_by'     => auth()->id(),
            ]);

            $inv = Inventory::firstOrNew(['product_id' => $item['product_id'], 'variation_id' => $item['variation_id'] ?? null]);
            $inv->quantity   = ($inv->quantity ?? 0) + $item['quantity'];
            $inv->cost_price = $item['unit_cost'];
            $inv->save();
        }

        // Mark PR as ordered
        if (!empty($data['purchase_request_id'])) {
            PurchaseRequest::find($data['purchase_request_id'])?->update(['status' => 'ordered']);
        }

        return redirect()->route('purchases.index')->with('success', "Purchase {$purchase->purchase_number} created.");
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('purchases.view');
        $purchase->load('vendor', 'creator', 'items.product', 'items.variation', 'purchaseRequest');
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        $this->authorize('purchases.delete');
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase deleted.');
    }

    public function edit(Purchase $purchase) { abort(403, 'Purchases cannot be edited. Delete and re-create.'); }
    public function update(Request $request, Purchase $purchase) { abort(403); }
}
