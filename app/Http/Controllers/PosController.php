<?php

namespace App\Http\Controllers;

use App\Mail\PosOrderConfirmation;
use App\Models\Category;
use App\Models\Clinic;
use App\Models\Country;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'avoid-back-history']);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       INDEX — orders list
    ═══════════════════════════════════════════════════════════════════════ */
    public function index(Request $request)
    {
        $this->authorize('pos.view');

        if ($request->ajax()) {
            $user  = auth()->user();
            $query = PosOrder::with(['patient:id,name,phone', 'clinic:id,name', 'creator:id,name'])
                ->withCount('items');

            if (!$user->isSuperAdmin()) {
                $query->where('clinic_id', $user->clinic_id);
            } elseif ($request->filled('filter_clinic')) {
                $query->where('clinic_id', $request->filter_clinic);
            }

            if ($request->filled('filter_status')) {
                $query->where('payment_status', $request->filter_status);
            }
            if ($request->filled('filter_date')) {
                $query->whereDate('created_at', $request->filter_date);
            }

            $query->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('patient_col', fn(PosOrder $o) =>
                    '<div class="fw-semibold">' . e($o->patient?->name ?? '—') . '</div>'
                    . ($o->patient?->phone ? '<small class="text-muted">' . e($o->patient->phone) . '</small>' : '')
                )
                ->addColumn('clinic_col',  fn(PosOrder $o) => e($o->clinic?->name ?? '—'))
                ->addColumn('totals_col',  fn(PosOrder $o) =>
                    '<div><small class="text-muted">Sub:</small> ' . number_format($o->subtotal, 2) . '</div>'
                    . ($o->discount > 0 ? '<div><small class="text-danger">-Disc:</small> ' . number_format($o->discount, 2) . '</div>' : '')
                    . ($o->tax_amount > 0 ? '<div><small class="text-muted">' . e($o->tax_label ?: 'Tax') . ':</small> ' . number_format($o->tax_amount, 2) . '</div>' : '')
                    . '<div class="fw-semibold text-dark">Total: ' . number_format($o->grand_total, 2) . '</div>'
                )
                ->addColumn('status_col', function (PosOrder $o) {
                    $badge = $o->payment_status === 'paid'
                        ? '<span class="badge bg-success">Paid</span>'
                        : '<span class="badge bg-warning text-dark">Unpaid</span>';
                    return '<button class="btn btn-sm p-0 border-0 btn-toggle-payment" data-id="' . $o->id . '">' . $badge . '</button>';
                })
                ->addColumn('action', function (PosOrder $o) {
                    $edit = auth()->user()->can('pos.create')
                        ? '<a href="' . route('pos.edit', $o->id) . '" class="btn btn-sm btn-outline-warning me-1" title="Edit Order"><i class="bi bi-pencil-square"></i></a>'
                        : '';
                    $view = '<a href="' . route('pos.show', $o->id) . '" class="btn btn-sm btn-outline-info me-1" title="View Receipt"><i class="bi bi-receipt"></i></a>';
                    $del  = auth()->user()->can('pos.delete')
                        ? '<button class="btn btn-sm btn-outline-danger btn-del-order" data-id="' . $o->id . '" title="Delete"><i class="bi bi-trash3"></i></button>'
                        : '';
                    return $edit . $view . $del;
                })
                ->rawColumns(['patient_col', 'totals_col', 'status_col', 'action'])
                ->make(true);
        }

        $clinics = auth()->user()->isSuperAdmin() ? Clinic::orderBy('name')->get() : collect();
        return view('admin.pos.index', compact('clinics'));
    }

    /* ═══════════════════════════════════════════════════════════════════════
       CREATE — new POS cart
    ═══════════════════════════════════════════════════════════════════════ */
    public function create()
    {
        $this->authorize('pos.create');

        $user       = auth()->user();
        $clinics    = $user->isSuperAdmin() ? Clinic::orderBy('name')->get() : collect();
        $myClinic   = $user->clinic;
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $countries  = Country::active()->orderBy('name')->get(['id', 'name', 'phone_code']);

        return view('admin.pos.create', compact('clinics', 'myClinic', 'categories', 'countries'));
    }

    /* ═══════════════════════════════════════════════════════════════════════
       EDIT — load existing order into POS cart
    ═══════════════════════════════════════════════════════════════════════ */
    public function edit(PosOrder $pos)
    {
        $this->authorize('pos.create');

        $pos->load(['items.product', 'items.variation', 'patient', 'clinic']);

        $user       = auth()->user();
        $clinics    = $user->isSuperAdmin() ? Clinic::orderBy('name')->get() : collect();
        $myClinic   = $user->clinic;
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $countries  = Country::active()->orderBy('name')->get(['id', 'name', 'phone_code']);

        return view('admin.pos.create', compact('pos', 'clinics', 'myClinic', 'categories', 'countries'));
    }

    /* ═══════════════════════════════════════════════════════════════════════
       LOAD ORDER BY NUMBER — AJAX (for "load existing order" input)
    ═══════════════════════════════════════════════════════════════════════ */
    public function loadOrder(Request $request)
    {
        $this->authorize('pos.create');

        $order = PosOrder::with(['items.product', 'items.variation', 'patient'])
            ->where('order_number', $request->order_number)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'payment_status'   => $order->payment_status,
            'order_type'       => $order->order_type ?? 'takeaway',
            'delivery_address' => $order->delivery_address,
            'discount'         => $order->discount,
            'tax_label'        => $order->tax_label,
            'tax_rate'         => $order->tax_rate,
            'notes'            => $order->notes,
            'clinic_id'        => $order->clinic_id,
            'patient'          => $order->patient ? [
                'id'    => $order->patient->id,
                'name'  => $order->patient->name,
                'phone' => $order->patient->phone,
                'shipping_address' => $order->patient->shipping_address,
            ] : null,
            'items' => $order->items->map(fn($item) => [
                'product_id'     => $item->product_id,
                'variation_id'   => $item->variation_id,
                'product_name'   => $item->product_name,
                'variation_name' => $item->variation_name,
                'quantity'       => $item->quantity,
                'unit_price'     => $item->unit_price,
                'track_inventory'=> $item->product?->track_inventory ?? false,
            ]),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       STORE — save NEW POS order
    ═══════════════════════════════════════════════════════════════════════ */
    public function store(Request $request)
    {
        $this->authorize('pos.create');
        return $this->saveOrder($request, null);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       UPDATE — edit EXISTING POS order (inventory-safe)
    ═══════════════════════════════════════════════════════════════════════ */
    public function update(Request $request, PosOrder $pos)
    {
        $this->authorize('pos.create');
        return $this->saveOrder($request, $pos);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       INTERNAL — shared store/update logic
    ═══════════════════════════════════════════════════════════════════════ */
    private function saveOrder(Request $request, ?PosOrder $existing)
    {
        $data = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'clinic_id'        => 'nullable|exists:clinics,id',
            'discount'         => 'nullable|numeric|min:0',
            'tax_label'        => 'nullable|string|max:50',
            'tax_rate'         => 'nullable|numeric|min:0|max:100',
            'shipping_address' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'order_type'       => 'nullable|in:takeaway,delivery',
            'notes'            => 'nullable|string',
            'payment_status'   => 'nullable|in:paid,unpaid',
            'items'            => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:product_variations,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        $user      = auth()->user();
        $clinicId  = $user->isSuperAdmin() ? ($data['clinic_id'] ?? null) : $user->clinic_id;
        $discount  = (float) ($data['discount'] ?? 0);
        $taxRate   = (float) ($data['tax_rate'] ?? 0);
        $taxLabel  = $data['tax_label'] ?? null;
        $orderType = $data['order_type'] ?? 'takeaway';

        $subtotal   = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $afterDisc  = max(0, $subtotal - $discount);
        $taxAmount  = round($afterDisc * $taxRate / 100, 2);
        $grandTotal = round($afterDisc + $taxAmount, 2);

        $orderId = null;

        DB::transaction(function () use ($data, $existing, $clinicId, $discount, $taxRate, $taxLabel, $subtotal, $taxAmount, $grandTotal, $orderType, &$orderId) {

            // ── If editing an existing order, restore OLD inventory first ──
            if ($existing) {
                $this->restoreInventory($existing, 'POS Order updated: #' . $existing->order_number);
                $existing->items()->delete();

                $existing->update([
                    'clinic_id'        => $clinicId,
                    'user_id'          => $data['user_id'],
                    'subtotal'         => $subtotal,
                    'discount'         => $discount,
                    'tax_label'        => $taxLabel,
                    'tax_rate'         => $taxRate,
                    'tax_amount'       => $taxAmount,
                    'grand_total'      => $grandTotal,
                    'shipping_address' => $data['shipping_address'] ?? $existing->shipping_address,
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'order_type'       => $orderType,
                    'notes'            => $data['notes'] ?? null,
                    'payment_status'   => $data['payment_status'] ?? $existing->payment_status,
                ]);
                $order = $existing;
            } else {
                $order = PosOrder::create([
                    'order_number'     => PosOrder::generateOrderNumber(),
                    'clinic_id'        => $clinicId,
                    'user_id'          => $data['user_id'],
                    'subtotal'         => $subtotal,
                    'discount'         => $discount,
                    'tax_label'        => $taxLabel,
                    'tax_rate'         => $taxRate,
                    'tax_amount'       => $taxAmount,
                    'grand_total'      => $grandTotal,
                    'shipping_address' => $data['shipping_address'] ?? null,
                    'delivery_address' => $data['delivery_address'] ?? null,
                    'order_type'       => $orderType,
                    'notes'            => $data['notes'] ?? null,
                    'payment_status'   => $data['payment_status'] ?? 'unpaid',
                    'created_by'       => auth()->id(),
                ]);
            }

            // ── Save new items and deduct inventory ──
            foreach ($data['items'] as $item) {
                $product   = Product::find($item['product_id']);
                $variation = !empty($item['variation_id']) ? ProductVariation::find($item['variation_id']) : null;

                PosOrderItem::create([
                    'pos_order_id'   => $order->id,
                    'product_id'     => $item['product_id'],
                    'variation_id'   => $item['variation_id'] ?? null,
                    'product_name'   => $product?->name ?? 'Unknown',
                    'variation_name' => $variation?->name ?? null,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'line_total'     => $item['quantity'] * $item['unit_price'],
                ]);

                if ($product && $product->track_inventory) {
                    $inv = Inventory::firstOrNew([
                        'product_id'   => $item['product_id'],
                        'variation_id' => $item['variation_id'] ?? null,
                    ]);
                    $inv->quantity = ($inv->quantity ?? 0) - $item['quantity'];
                    $inv->save();

                    InventoryMovement::create([
                        'product_id'     => $item['product_id'],
                        'variation_id'   => $item['variation_id'] ?? null,
                        'type'           => 'pos_sale',
                        'quantity'       => -$item['quantity'],
                        'unit_price'     => $item['unit_price'],
                        'reference_type' => 'pos_order',
                        'reference_id'   => $order->id,
                        'notes'          => ($existing ? 'Updated' : 'New') . ' POS Sale #' . $order->order_number,
                        'created_by'     => auth()->id(),
                    ]);
                }
            }

            // ── Delivery: save address to patient profile ──
            if ($orderType === 'delivery' && !empty($data['delivery_address'])) {
                User::where('id', $data['user_id'])->update([
                    'shipping_address' => $data['delivery_address'],
                ]);
            }

            $orderId = $order->id;
            session(['last_pos_order_id' => $order->id]);
        });

        // ── Email notification (if clinic has it enabled) ──
        $order = PosOrder::with(['items', 'patient', 'clinic', 'creator'])->find($orderId);
        if ($order && $order->clinic && $order->clinic->email_notifications) {
            $patient = $order->patient;
            if ($patient && $patient->email) {
                try {
                    Mail::to($patient->email)->send(new PosOrderConfirmation($order));
                } catch (\Throwable $e) {
                    // Email failure should not break the order
                    \Log::warning('POS email failed: ' . $e->getMessage());
                }
            }
        }

        $finalOrderId = session()->pull('last_pos_order_id') ?? $orderId;

        if (request()->ajax()) {
            return response()->json([
                'status'   => 'success',
                'order_id' => $finalOrderId,
                'redirect' => route('pos.show', $finalOrderId),
            ]);
        }

        return redirect()->route('pos.show', $finalOrderId)->with('success', 'POS order saved.');
    }

    /* ── Helper: restore inventory for all items of an order ── */
    private function restoreInventory(PosOrder $order, string $notes): void
    {
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (!$product || !$product->track_inventory) continue;

            $inv = Inventory::where('product_id', $item->product_id)
                ->where('variation_id', $item->variation_id)
                ->first();
            if ($inv) {
                $inv->increment('quantity', $item->quantity);
            }

            InventoryMovement::create([
                'product_id'     => $item->product_id,
                'variation_id'   => $item->variation_id,
                'type'           => 'return',
                'quantity'       => $item->quantity,
                'unit_price'     => $item->unit_price,
                'reference_type' => 'pos_order',
                'reference_id'   => $order->id,
                'notes'          => $notes,
                'created_by'     => auth()->id(),
            ]);
        }
    }

    /* ═══════════════════════════════════════════════════════════════════════
       SHOW — receipt page
    ═══════════════════════════════════════════════════════════════════════ */
    public function show(PosOrder $pos)
    {
        $this->authorize('pos.view');
        $pos->load(['items', 'patient', 'clinic', 'creator']);
        return view('admin.pos.show', compact('pos'));
    }

    /* ═══════════════════════════════════════════════════════════════════════
       TOGGLE PAYMENT STATUS
    ═══════════════════════════════════════════════════════════════════════ */
    public function togglePayment(PosOrder $pos)
    {
        $this->authorize('pos.view');
        $new = $pos->payment_status === 'paid' ? 'unpaid' : 'paid';
        $pos->update(['payment_status' => $new]);
        return response()->json(['success' => true, 'status' => $new, 'label' => ucfirst($new)]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       DESTROY — delete order + restore inventory
    ═══════════════════════════════════════════════════════════════════════ */
    public function destroy(PosOrder $pos)
    {
        $this->authorize('pos.delete');

        DB::transaction(function () use ($pos) {
            $pos->load('items');
            $this->restoreInventory($pos, 'POS Order deleted: #' . $pos->order_number);
            $pos->items()->delete();
            $pos->delete();
        });

        return response()->json(['success' => true]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PRODUCTS SEARCH — AJAX for POS cart
    ═══════════════════════════════════════════════════════════════════════ */
    public function getProducts(Request $request)
    {
        $this->authorize('pos.create');

        $query = Product::where('status', 1)
            ->with(['variations' => fn($q) => $q->where('status', 1)->with('inventory'), 'inventory'])
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->get();

        return response()->json($query->map(fn(Product $p) => [
            'id'              => $p->id,
            'name'            => $p->name,
            'price'           => $p->price,
            'has_variations'  => $p->has_variations,
            'track_inventory' => $p->track_inventory,
            'stock'           => $p->has_variations ? null : ($p->inventory?->quantity ?? 0),
            'category_id'     => $p->category_id,
            'variations'      => $p->has_variations ? $p->variations->map(fn($v) => [
                'id'    => $v->id,
                'name'  => $v->name,
                'price' => $v->price,
                'stock' => $v->inventory?->quantity ?? 0,
            ]) : [],
        ]));
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PATIENT SEARCH — AJAX typeahead
    ═══════════════════════════════════════════════════════════════════════ */
    public function searchPatients(Request $request)
    {
        $this->authorize('pos.create');

        $results = User::whereNotIn('role', ['doctor', 'admin'])
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('phone', 'like', '%' . $request->q . '%');
            })
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($results);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       QUICK CREATE PATIENT — AJAX (from POS Add Patient modal)
    ═══════════════════════════════════════════════════════════════════════ */
    public function quickCreatePatient(Request $request)
    {
        $this->authorize('pos.create');

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => bcrypt(\Illuminate\Support\Str::random(12)),
            'role'     => 'patient',
            'status'   => 1,
        ]);

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'phone' => $user->phone ?? '',
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       CASCADING LOCATION — AJAX
    ═══════════════════════════════════════════════════════════════════════ */
    public function getStates(Request $request)
    {
        $states = \App\Models\State::where('country_id', $request->country_id)
            ->active()->orderBy('name')->get(['id', 'name']);
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $cities = \App\Models\City::where('state_id', $request->state_id)
            ->active()->orderBy('name')->get(['id', 'name']);
        return response()->json($cities);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       REPORT PAGE
    ═══════════════════════════════════════════════════════════════════════ */
    public function report(Request $request)
    {
        $this->authorize('pos.report');

        $user      = auth()->user();
        $clinics   = $user->isSuperAdmin() ? Clinic::orderBy('name')->get() : collect();
        $categories = Category::orderBy('name')->get(['id', 'name']);

        $query = PosOrder::query();
        if (!$user->isSuperAdmin()) {
            $query->where('clinic_id', $user->clinic_id);
        } elseif ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $from = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->input('date_to',   now()->format('Y-m-d'));
        $query->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to);

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders        = $query->get();
        $totalOrders   = $orders->count();
        $grossSales    = $orders->sum('subtotal');
        $totalDiscount = $orders->sum('discount');
        $totalTax      = $orders->sum('tax_amount');
        $netRevenue    = $orders->sum('grand_total');

        $dailySales = PosOrder::when(!$user->isSuperAdmin(), fn($q) => $q->where('clinic_id', $user->clinic_id))
            ->when($user->isSuperAdmin() && $request->filled('clinic_id'), fn($q) => $q->where('clinic_id', $request->clinic_id))
            ->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->payment_status))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as order_count, SUM(grand_total) as revenue')
            ->groupBy('day')->orderBy('day')->get();

        $salesByCategory = PosOrderItem::join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('pos_orders.clinic_id', $user->clinic_id))
            ->when($user->isSuperAdmin() && $request->filled('clinic_id'), fn($q) => $q->where('pos_orders.clinic_id', $request->clinic_id))
            ->whereDate('pos_orders.created_at', '>=', $from)->whereDate('pos_orders.created_at', '<=', $to)
            ->when($request->filled('payment_status'), fn($q) => $q->where('pos_orders.payment_status', $request->payment_status))
            ->selectRaw('categories.name as category, SUM(pos_order_items.line_total) as total')
            ->groupBy('categories.name')->orderByDesc('total')->get();

        $productBreakdown = PosOrderItem::join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->join('products', 'pos_order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('pos_orders.clinic_id', $user->clinic_id))
            ->when($user->isSuperAdmin() && $request->filled('clinic_id'), fn($q) => $q->where('pos_orders.clinic_id', $request->clinic_id))
            ->whereDate('pos_orders.created_at', '>=', $from)->whereDate('pos_orders.created_at', '<=', $to)
            ->when($request->filled('category_id'), fn($q) => $q->where('products.category_id', $request->category_id))
            ->when($request->filled('payment_status'), fn($q) => $q->where('pos_orders.payment_status', $request->payment_status))
            ->selectRaw('pos_order_items.product_name, categories.name as category, SUM(pos_order_items.quantity) as total_qty, SUM(pos_order_items.line_total) as total_revenue')
            ->groupBy('pos_order_items.product_name', 'categories.name')
            ->orderByDesc('total_revenue')->get();

        $ordersList = $query->with(['patient:id,name', 'creator:id,name', 'clinic:id,name'])->latest()->get();

        return view('admin.pos.report', compact(
            'clinics', 'categories', 'from', 'to',
            'totalOrders', 'grossSales', 'totalDiscount', 'totalTax', 'netRevenue',
            'dailySales', 'salesByCategory', 'productBreakdown', 'ordersList'
        ));
    }
}
