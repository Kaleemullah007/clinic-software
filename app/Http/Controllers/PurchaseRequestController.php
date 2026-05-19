<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('purchase-requests.view');

        if ($request->ajax()) {
            $query = PurchaseRequest::with('requestedBy', 'approvedBy', 'items')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('pr_number_link', function (PurchaseRequest $pr) {
                    return '<a href="' . route('purchase-requests.show', $pr->id) . '" class="fw-semibold text-theme-color">' . e($pr->pr_number) . '</a>';
                })
                ->addColumn('items_count', function (PurchaseRequest $pr) {
                    return $pr->items->count();
                })
                ->addColumn('requested_by_name', function (PurchaseRequest $pr) {
                    return e($pr->requestedBy->name ?? '—');
                })
                ->addColumn('approved_by_name', function (PurchaseRequest $pr) {
                    return e($pr->approvedBy->name ?? '—');
                })
                ->addColumn('status_badge', function (PurchaseRequest $pr) {
                    $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'ordered' => 'info'];
                    $color  = $colors[$pr->status] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($pr->status) . '</span>';
                })
                ->addColumn('date', function (PurchaseRequest $pr) {
                    return optional($pr->created_at)?->format('d M Y') ?? '—';
                })
                ->addColumn('action', function (PurchaseRequest $pr) {
                    $html = '<a href="' . route('purchase-requests.show', $pr->id) . '" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-eye"></i></a>';

                    if ($pr->status === 'pending') {
                        if (auth()->user()->can('purchase-requests.edit')) {
                            $html .= '<a href="' . route('purchase-requests.edit', $pr->id) . '" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>';
                        }
                        if (auth()->user()->can('purchase-requests.approve')) {
                            $html .= '<form action="' . route('purchase-request.approve', $pr->id) . '" method="POST" class="d-inline me-1">'
                                   . csrf_field()
                                   . '<button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button></form>';
                            $html .= '<button class="btn btn-sm btn-danger me-1" onclick="rejectPR(' . $pr->id . ')"><i class="bi bi-x-lg"></i></button>';
                        }
                    }

                    if (auth()->user()->can('purchase-requests.delete') && in_array($pr->status, ['pending', 'rejected'])) {
                        $html .= '<form action="' . route('purchase-requests.destroy', $pr->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this request?\')">'
                               . csrf_field() . method_field('DELETE')
                               . '<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
                    }

                    return $html;
                })
                ->rawColumns(['pr_number_link', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.purchase-requests.index');
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
