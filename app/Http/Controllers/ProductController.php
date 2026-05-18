<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('products.view');

        if ($request->ajax()) {
            $query = Product::withCount('variations')->with('inventory');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('price_col', fn (Product $p) =>
                    $p->has_variations
                        ? '<span class="text-muted">—</span>'
                        : 'PKR ' . number_format($p->price, 2)
                )
                ->addColumn('variations_col', fn (Product $p) =>
                    $p->has_variations ? $p->variations_count . ' var.' : '—'
                )
                ->addColumn('stock_col', function (Product $p) {
                    if (!$p->track_inventory) return '<span class="text-muted">—</span>';
                    $qty = $p->inventory?->quantity ?? 0;
                    return '<span class="badge ' . ($qty > 0 ? 'bg-success' : 'bg-danger') . '">' . $qty . '</span>';
                })
                ->addColumn('track_col', fn (Product $p) =>
                    $p->track_inventory
                        ? '<span class="badge bg-info">Yes</span>'
                        : '<span class="badge bg-secondary">No</span>'
                )
                ->addColumn('status_badge', fn (Product $p) =>
                    $p->status
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>'
                )
                ->addColumn('action', function (Product $p) {
                    $edit = $del = '';
                    if (auth()->user()->can('products.edit')) {
                        $edit = '<a href="' . route('products.edit', $p) . '" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-pencil"></i>
                                 </a>';
                    }
                    if (auth()->user()->can('products.delete')) {
                        $del = '<form action="' . route('products.destroy', $p) . '" method="POST" class="d-inline"
                                    onsubmit="return confirm(\'Delete this product?\')">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                               </form>';
                    }
                    return $edit . $del;
                })
                ->rawColumns(['price_col', 'variations_col', 'stock_col', 'track_col', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.products.index');
    }

    public function create()
    {
        $this->authorize('products.create');
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $this->authorize('products.create');
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required_without:has_variations|nullable|numeric|min:0',
            'has_variations'  => 'boolean',
            'track_inventory' => 'boolean',
            'category_id'     => 'nullable|exists:categories,id',
            'status'          => 'boolean',
            'variations'      => 'nullable|array',
            'variations.*.name'  => 'required_with:variations|string|max:255',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
        ]);
        $data['has_variations']  = $request->boolean('has_variations');
        $data['track_inventory'] = $request->boolean('track_inventory', true);
        $data['status']          = $request->boolean('status', true);

        $product = Product::create($data);

        if ($data['has_variations'] && !empty($data['variations'])) {
            foreach ($data['variations'] as $v) {
                $product->variations()->create(['name' => $v['name'], 'price' => $v['price'], 'status' => 1]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorize('products.edit');
        $product->load('variations');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('products.edit');
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'has_variations'  => 'boolean',
            'track_inventory' => 'boolean',
            'category_id'     => 'nullable|exists:categories,id',
            'status'          => 'boolean',
        ]);
        $data['has_variations']  = $request->boolean('has_variations');
        $data['track_inventory'] = $request->boolean('track_inventory', true);
        $data['status']          = $request->boolean('status', true);

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('products.delete');
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    /** AJAX: add a variation inline */
    public function storeVariation(Request $request, Product $product)
    {
        $this->authorize('products.edit');
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);
        $variation = $product->variations()->create($data + ['status' => 1]);
        return response()->json(['success' => true, 'variation' => $variation]);
    }

    /** AJAX: delete a variation */
    public function destroyVariation(ProductVariation $variation)
    {
        $this->authorize('products.edit');
        $variation->delete();
        return response()->json(['success' => true]);
    }

    /** AJAX: product search by name or code (for purchase/appointment forms) */
    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $products = Product::where('status', 1)
            ->where('name', 'like', "%{$q}%")
            ->with('variations')
            ->limit(20)
            ->get(['id', 'name', 'price', 'has_variations']);
        return response()->json($products);
    }
}
