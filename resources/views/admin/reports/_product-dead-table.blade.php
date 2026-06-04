{{--
    Variables expected:
    $tableId, $deadProducts, $everAppt, $everPos,
    $apptIds, $posIds, $showAppt, $showPos,
    $emptyMsg, $title, $accentColor
--}}
<div class="row mx-1 g-4 mb-5">
    <div class="col-12">
        <div class="rpt-panel" style="border:2px solid {{ $accentColor }}22">
            <div class="rpt-panel-head" style="background:{{ $accentColor }}08">
                <i class="bi bi-exclamation-triangle me-2" style="color:{{ $accentColor }}"></i>
                {{ $title }}
                <span class="badge ms-2" style="background:{{ $accentColor }}">{{ $deadProducts->count() }}</span>
            </div>
            <div class="rpt-panel-body p-0">
                <table class="table table-hover mb-0 rpt-table" id="{{ $tableId }}">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-end">Listed Price</th>
                            <th class="text-end">Stock Qty</th>
                            <th class="text-end">Idle Value</th>
                            @if($showAppt)
                            <th>Last in Appt</th>
                            @endif
                            @if($showPos)
                            <th>Last in POS</th>
                            @endif
                            @if($showAppt && !$showPos)
                            <th class="text-center">POS Status</th>
                            @endif
                            @if($showPos && !$showAppt)
                            <th class="text-center">Appt Status</th>
                            @endif
                            <th class="text-center">Diagnosis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deadProducts as $i => $prod)
                        @php
                            $stock     = $prod->inventory?->quantity ?? 0;
                            $idleVal   = $stock * ($prod->price ?? 0);
                            $lastAppt  = $everAppt[$prod->id]->last_used ?? null;
                            $lastPos   = $everPos[$prod->id]->last_sold ?? null;

                            // Diagnosis based on most recent activity across both channels
                            $lastAny   = collect(array_filter([$lastAppt, $lastPos]))->max();
                            if (!$lastAny) {
                                $diagBadge = '<span class="dead-badge-never">Never Used</span>';
                            } elseif (\Carbon\Carbon::parse($lastAny)->diffInDays(now()) > 180) {
                                $days = \Carbon\Carbon::parse($lastAny)->diffInDays(now());
                                $diagBadge = '<span class="dead-badge-dormant">Dormant (' . $days . 'd)</span>';
                            } else {
                                $diagBadge = '<span class="dead-badge-period">Not This Period</span>';
                            }
                        @endphp
                        <tr class="dead-row">
                            <td class="text-muted small">{{ $i + 1 }}</td>
                            <td class="fw-semibold" style="color:{{ $accentColor }}">
                                {{ $prod->name }}
                                @if(!$prod->status)
                                    <span class="badge bg-secondary ms-1" style="font-size:.65rem">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end text-muted">{{ $prod->price ? 'PKR ' . number_format($prod->price, 0) : '—' }}</td>
                            <td class="text-end">
                                @if($stock <= 0)
                                    <span class="badge bg-danger">0</span>
                                @elseif($stock < 5)
                                    <span class="badge bg-warning text-dark">{{ $stock }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $stock }}</span>
                                @endif
                            </td>
                            <td class="text-end text-muted small">{{ $idleVal > 0 ? 'PKR ' . number_format($idleVal, 0) : '—' }}</td>
                            @if($showAppt)
                            <td class="small">
                                @if($lastAppt)
                                    {{ \Carbon\Carbon::parse($lastAppt)->format('d M Y') }}
                                    <span class="text-muted">({{ \Carbon\Carbon::parse($lastAppt)->diffInDays(now()) }}d ago)</span>
                                @else
                                    <span class="text-danger fw-semibold">Never</span>
                                @endif
                            </td>
                            @endif
                            @if($showPos)
                            <td class="small">
                                @if($lastPos)
                                    {{ \Carbon\Carbon::parse($lastPos)->format('d M Y') }}
                                    <span class="text-muted">({{ \Carbon\Carbon::parse($lastPos)->diffInDays(now()) }}d ago)</span>
                                @else
                                    <span class="text-danger fw-semibold">Never</span>
                                @endif
                            </td>
                            @endif
                            @if($showAppt && !$showPos)
                            <td class="text-center">
                                @if($posIds->contains($prod->id))
                                    <span class="badge bg-success">Active in POS</span>
                                @else
                                    <span class="badge bg-danger">Dead in POS</span>
                                @endif
                            </td>
                            @endif
                            @if($showPos && !$showAppt)
                            <td class="text-center">
                                @if($apptIds->contains($prod->id))
                                    <span class="badge bg-success">Active in Appts</span>
                                @else
                                    <span class="badge bg-danger">Dead in Appts</span>
                                @endif
                            </td>
                            @endif
                            <td class="text-center">{!! $diagBadge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4" style="color:#10b981">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ $emptyMsg }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($deadProducts->isNotEmpty())
                    <tfoot class="table-light">
                        <tr class="fw-semibold small">
                            <td colspan="4">Total Idle Value</td>
                            <td class="text-end">PKR {{ number_format($deadProducts->sum(fn($p) => ($p->inventory?->quantity ?? 0) * ($p->price ?? 0)), 0) }}</td>
                            <td colspan="10"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
