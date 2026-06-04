@php
    use Carbon\Carbon;
    $today = Carbon::today();
@endphp

<div class="table-responsive">
    <table class="table cm-table mb-0" id="{{ $tableId }}">
        <thead>
            <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Services</th>
                <th>Payment</th>
                <th>Last Call</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $i => $a)
            @php
                $apptDate   = Carbon::parse($a->date);
                $daysLeft   = $today->diffInDays($apptDate, false); // negative if past
                $lastLog    = $a->callLogs->first();
                $callCount  = $a->callLogs->count();
            @endphp
            <tr data-row-id="{{ $a->id }}">
                <td class="text-muted small">{{ $i + 1 }}</td>

                {{-- Patient --}}
                <td>
                    <div class="fw-semibold">{{ $a->name }}</div>
                    <small class="text-muted">{{ $a->phone }}</small>
                </td>

                {{-- Doctor --}}
                <td class="small">{{ $a->doctor?->name ?? '—' }}</td>

                {{-- Date + days remaining --}}
                <td>
                    <div class="small fw-semibold">{{ $apptDate->format('d M Y') }}</div>
                    @if($daysLeft === 0)
                        <span class="days-badge mt-1" style="background:#fce7f3;color:#9d174d;">Today</span>
                    @elseif($daysLeft === 1)
                        <span class="days-badge mt-1" style="background:#e0e7ff;color:#3730a3;">Tomorrow</span>
                    @elseif($daysLeft > 1)
                        <span class="days-badge mt-1" style="background:#ede9fe;color:#5b21b6;">In {{ $daysLeft }} days</span>
                    @else
                        <span class="days-badge mt-1" style="background:#fee2e2;color:#991b1b;">{{ abs($daysLeft) }}d ago</span>
                    @endif
                </td>

                {{-- Services --}}
                <td style="max-width:200px">
                    @forelse($a->appointmentService as $svc)
                        <div style="font-size:.8rem;line-height:1.5">
                            {{ $svc->name }}
                            <span style="color:#B1083C;font-weight:600;font-size:.75rem">
                                PKR {{ number_format($svc->discounted_price ?? $svc->price, 0) }}
                            </span>
                        </div>
                    @empty
                        <span class="text-muted small">—</span>
                    @endforelse
                </td>

                {{-- Payment --}}
                <td>
                    @if($a->is_paid === 'paid')
                        <span class="days-badge" style="background:#d1fae5;color:#065f46">Paid</span>
                    @else
                        <span class="days-badge" style="background:#fef3c7;color:#92400e">Unpaid</span>
                    @endif
                </td>

                {{-- Last call status --}}
                <td class="last-call-cell">
                    @if($lastLog)
                        <span class="days-badge call-status-{{ $lastLog->call_status }}">
                            {{ ucwords(str_replace('_',' ', $lastLog->call_status)) }}
                        </span>
                        @if($callCount > 1)
                            <br><small class="text-muted">{{ $callCount }} calls</small>
                        @endif
                        <br><small class="text-muted" style="font-size:.72rem">{{ $lastLog->created_at->diffForHumans() }}</small>
                    @else
                        <span class="text-muted small">No calls yet</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="text-center">
                    <button class="btn btn-sm btn-wa btn-open-notes" data-id="{{ $a->id }}" title="View / Add Call Notes" style="background:#B1083C;color:#fff;border:none;border-radius:6px;padding:5px 12px">
                        <i class="bi bi-telephone me-1"></i>
                        @if($callCount > 0)
                            Notes <span class="badge bg-white text-danger ms-1" style="font-size:.7rem">{{ $callCount }}</span>
                        @else
                            Add Note
                        @endif
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x d-block fs-2 mb-2"></i>
                    No appointments for {{ $dayLabel }}.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
