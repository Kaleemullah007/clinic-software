<?php

namespace App\Http\Controllers;

use App\Jobs\MigrateServiceJob;
use App\Models\AppointmentService;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $this->authorize('taxonomy.manage');

        $categories = Category::orderBy('name')->get(['id', 'name', 'status']);

        return view('admin.taxonomy.index', compact('categories'));
    }

    // ── AJAX: Load appointment-services for a source category ─────────────────

    public function getAppointmentServices(Request $request)
    {
        $this->authorize('taxonomy.manage');

        $request->validate([
            'service_id' => 'required|integer|exists:categories,id',
        ]);

        $items = AppointmentService::with('appointment:id,name,date')
            ->where('service_id', $request->service_id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'appointment_id', 'name', 'price', 'service_id']);

        return response()->json(
            $items->map(fn ($item) => [
                'id'             => $item->id,
                'name'           => $item->name,
                'price'          => number_format((float) $item->price, 2),
                'appointment_id' => $item->appointment_id,
                'patient'        => $item->appointment?->name ?? '—',
                'date'           => $item->appointment?->date
                    ? Carbon::parse($item->appointment->date)->format('d M Y')
                    : '—',
                'service_id'     => $item->service_id,
            ])
        );
    }

    // ── AJAX: Preview what will change ────────────────────────────────────────

    public function preview(Request $request)
    {
        $this->authorize('taxonomy.manage');

        $request->validate([
            'ids'       => 'required|array|min:1',
            'ids.*'     => 'integer|exists:appointment_services,id',
            'target_id' => 'required|integer|exists:categories,id',
        ]);

        $targetCategory = Category::findOrFail($request->target_id);
        $count          = count($request->ids);

        // Collect distinct source category names
        $sources = AppointmentService::whereIn('id', $request->ids)
            ->with('category:id,name')
            ->get()
            ->pluck('category.name')
            ->unique()
            ->filter()
            ->values();

        return response()->json([
            'count'   => $count,
            'target'  => $targetCategory->name,
            'sources' => $sources,
        ]);
    }

    // ── AJAX: Dispatch migration job ──────────────────────────────────────────

    public function migrate(Request $request)
    {
        $this->authorize('taxonomy.manage');

        $request->validate([
            'ids'       => 'required|array|min:1',
            'ids.*'     => 'integer|exists:appointment_services,id',
            'target_id' => 'required|integer|exists:categories,id',
        ]);

        $batchId = Str::uuid()->toString();

        // Seed initial cache so the progress endpoint never returns 404
        Cache::put("taxonomy_progress_{$batchId}", [
            'total'     => count($request->ids),
            'processed' => 0,
            'status'    => 'running',
        ], now()->addMinutes(30));

        MigrateServiceJob::dispatch(
            $batchId,
            $request->ids,
            (int) $request->target_id
        );

        return response()->json(['batch_id' => $batchId]);
    }

    // ── AJAX: Poll progress ───────────────────────────────────────────────────

    public function progress(Request $request)
    {
        $this->authorize('taxonomy.manage');

        $request->validate(['batch_id' => 'required|string']);

        $data = Cache::get("taxonomy_progress_{$request->batch_id}", [
            'total'     => 0,
            'processed' => 0,
            'status'    => 'pending',
        ]);

        return response()->json($data);
    }
}
