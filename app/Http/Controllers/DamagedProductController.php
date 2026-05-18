<?php

namespace App\Http\Controllers;

use App\Models\DamagedProduct;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class DamagedProductController extends Controller
{
    public function index()
    {
        $this->authorize('damaged-products.view');
        $records = DamagedProduct::with('product', 'variation', 'reportedBy')->latest()->get();
        return view('admin.damaged-products.index', compact('records'));
    }

    public function create()
    {
        $this->authorize('damaged-products.create');
        $products = Product::where('track_inventory', true)->where('status', 1)->with('variations')->get();
        return view('admin.damaged-products.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('damaged-products.create');
        $data = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:product_variations,id',
            'quantity'     => 'required|numeric|min:0.01',
            'cost_value'   => 'nullable|numeric|min:0',
            'reason'       => 'nullable|string',
        ]);

        DamagedProduct::create([...$data, 'reference_type' => 'manual', 'reported_by' => auth()->id()]);

        // Deduct from inventory
        InventoryMovement::create([
            'product_id'   => $data['product_id'],
            'variation_id' => $data['variation_id'] ?? null,
            'type'         => 'damaged',
            'quantity'     => -$data['quantity'],
            'unit_price'   => $data['cost_value'] ?? 0,
            'created_by'   => auth()->id(),
            'notes'        => $data['reason'] ?? null,
        ]);

        $inv = Inventory::firstOrNew(['product_id' => $data['product_id'], 'variation_id' => $data['variation_id'] ?? null]);
        $inv->quantity = max(0, ($inv->quantity ?? 0) - $data['quantity']);
        $inv->save();

        return redirect()->route('damaged-products.index')->with('success', 'Damaged product recorded.');
    }

    public function destroy(DamagedProduct $damagedProduct)
    {
        $this->authorize('damaged-products.delete');
        $damagedProduct->delete();
        return redirect()->route('damaged-products.index')->with('success', 'Record deleted.');
    }
}
