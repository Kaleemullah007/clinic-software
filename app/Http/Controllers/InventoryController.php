<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $this->authorize('inventory.view');
        $products = Product::where('track_inventory', true)
            ->with('inventory', 'variations.inventory')
            ->get();
        return view('admin.inventory.index', compact('products'));
    }

    public function show(Product $inventory)
    {
        // $inventory param is actually a product here (route model binding on 'inventory' resource)
        $this->authorize('inventory.view');
        $product = $inventory;
        $product->load(['variations', 'movements' => fn($q) => $q->with('creator')->latest()->limit(50)]);
        return view('admin.inventory.show', compact('product'));
    }

    public function adjust(Request $request)
    {
        $this->authorize('inventory.create');
        $data = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:product_variations,id',
            'quantity'     => 'required|numeric|not_in:0',
            'unit_price'   => 'nullable|numeric|min:0',
            'type'         => 'required|in:purchase,adjustment,return,damaged',
            'notes'        => 'nullable|string',
        ]);

        // Record movement
        InventoryMovement::create([
            'product_id'   => $data['product_id'],
            'variation_id' => $data['variation_id'] ?? null,
            'type'         => $data['type'],
            'quantity'     => $data['quantity'],
            'unit_price'   => $data['unit_price'] ?? 0,
            'notes'        => $data['notes'] ?? null,
            'created_by'   => auth()->id(),
        ]);

        // Update inventory snapshot
        $inv = Inventory::firstOrNew([
            'product_id'   => $data['product_id'],
            'variation_id' => $data['variation_id'] ?? null,
        ]);
        $inv->quantity = ($inv->quantity ?? 0) + $data['quantity'];
        if (!empty($data['unit_price'])) $inv->cost_price = $data['unit_price'];
        $inv->save();

        return back()->with('success', 'Inventory adjusted successfully.');
    }

    public function movements()
    {
        $this->authorize('inventory.view');
        $movements = InventoryMovement::with('product', 'variation', 'creator')->latest()->paginate(50);
        return view('admin.inventory.movements', compact('movements'));
    }
}
