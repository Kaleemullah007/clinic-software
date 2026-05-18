<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $this->authorize('purchase-requests.view');
        $prs = PurchaseRequest::with('requestedBy', 'approvedBy')
            ->latest()->get();
        return view('admin.purchase-requests.index', compact('prs'));
    }

    public function create()
    {
        $this->authorize('purchase-requests.create');
        $products = Product::where('status', 1)->with('variations')->get(['id','name','has_variations']);
        return view('admin.purchase-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('purchase-requests.create');
        $data = $request->validate([
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.notes'      => 'nullable|string',
        ]);

        $pr = PurchaseRequest::create([
            'pr_number'    => PurchaseRequest::generateNumber(),
            'requested_by' => auth()->id(),
            'status'       => 'pending',
            'notes'        => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $pr->items()->create($item);
        }

        return redirect()->route('purchase-requests.index')
            ->with('success', "Purchase Request {$pr->pr_number} submitted.");
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.view');
        $purchaseRequest->load('requestedBy', 'approvedBy', 'items.product', 'items.variation');
        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.edit');
        abort_if($purchaseRequest->status !== 'pending', 403, 'Only pending requests can be edited.');
        $purchaseRequest->load('items.product', 'items.variation');
        $products = Product::where('status', 1)->with('variations')->get(['id','name','has_variations']);
        return view('admin.purchase-requests.edit', compact('purchaseRequest', 'products'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.edit');
        abort_if($purchaseRequest->status !== 'pending', 403);
        $data = $request->validate([
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.notes'      => 'nullable|string',
        ]);

        $purchaseRequest->update(['notes' => $data['notes'] ?? null]);
        $purchaseRequest->items()->delete();
        foreach ($data['items'] as $item) {
            $purchaseRequest->items()->create($item);
        }

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Purchase request updated.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.delete');
        abort_if(!in_array($purchaseRequest->status, ['pending','rejected']), 403, 'Only pending/rejected can be deleted.');
        $purchaseRequest->delete();
        return redirect()->route('purchase-requests.index')->with('success', 'Request deleted.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.approve');
        abort_if($purchaseRequest->status !== 'pending', 403);
        $purchaseRequest->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', "Request {$purchaseRequest->pr_number} approved.");
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $this->authorize('purchase-requests.approve');
        abort_if($purchaseRequest->status !== 'pending', 403);
        $purchaseRequest->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes'       => $purchaseRequest->notes . "\n[Rejected] " . $request->input('reject_reason',''),
        ]);
        return back()->with('success', "Request {$purchaseRequest->pr_number} rejected.");
    }
}
