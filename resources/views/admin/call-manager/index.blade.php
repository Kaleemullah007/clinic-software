@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="row pt-3 mx-1 align-items-center mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-telephone-outbound me-2" style="color:#B1083C"></i>Call Manager
            </h4>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-calendar3 me-1"></i>Today: {{ $today->format('d M Y') }}
                </span>
            </div>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    {{-- Summary strip --}}
    <div class="row mx-1 g-2 mb-3">
        <div class="col-auto">
            <div class="cm-stat-pill" style="background:rgba(177,8,60,.08);border-color:rgba(177,8,60,.2)">
                <i class="bi bi-calendar-day" style="color:#B1083C"></i>
                <span><strong>{{ $todayAppts->count() }}</strong> Today</span>
            </div>
        </div>
        <div class="col-auto">
            <div class="cm-stat-pill" style="background:rgba(14,165,233,.08);border-color:rgba(14,165,233,.2)">
                <i class="bi bi-calendar2-plus" style="color:#0ea5e9"></i>
                <span><strong>{{ $tomorrowAppts->count() }}</strong> Tomorrow</span>
            </div>
        </div>
        <div class="col-auto">
            <div class="cm-stat-pill" style="background:rgba(139,92,246,.08);border-color:rgba(139,92,246,.2)">
                <i class="bi bi-calendar2-week" style="color:#8b5cf6"></i>
                <span><strong>{{ $dayAfterAppts->count() }}</strong> {{ $dayAfter->format('d M') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mx-1">
        <ul class="nav nav-tabs cm-tabs" id="callTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-today">
                    <i class="bi bi-calendar-day me-1"></i>
                    Today <span class="cm-tab-badge" style="background:#B1083C">{{ $todayAppts->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-tomorrow">
                    <i class="bi bi-calendar2-plus me-1"></i>
                    Tomorrow <span class="cm-tab-badge" style="background:#0ea5e9">{{ $tomorrowAppts->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-dayafter">
                    <i class="bi bi-calendar2-week me-1"></i>
                    {{ $dayAfter->format('d M') }} <span class="cm-tab-badge" style="background:#8b5cf6">{{ $dayAfterAppts->count() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content cm-tab-content">

            {{-- TODAY --}}
            <div class="tab-pane fade show active" id="tab-today">
                <div class="cm-search-bar">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" class="cm-search-input" placeholder="Search today's appointments…" data-target="#today-table">
                </div>
                @include('admin.call-manager._table', ['appointments' => $todayAppts, 'tableId' => 'today-table', 'dayLabel' => 'Today'])
            </div>

            {{-- TOMORROW --}}
            <div class="tab-pane fade" id="tab-tomorrow">
                <div class="cm-search-bar">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" class="cm-search-input" placeholder="Search tomorrow's appointments…" data-target="#tomorrow-table">
                </div>
                @include('admin.call-manager._table', ['appointments' => $tomorrowAppts, 'tableId' => 'tomorrow-table', 'dayLabel' => 'Tomorrow'])
            </div>

            {{-- DAY AFTER --}}
            <div class="tab-pane fade" id="tab-dayafter">
                <div class="cm-search-bar">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" class="cm-search-input" placeholder="Search {{ $dayAfter->format('d M') }} appointments…" data-target="#dayafter-table">
                </div>
                @include('admin.call-manager._table', ['appointments' => $dayAfterAppts, 'tableId' => 'dayafter-table', 'dayLabel' => $dayAfter->format('d M Y')])
            </div>

        </div>
    </div>

</div>

{{-- ════════════════ NOTES MODAL ════════════════ --}}
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(90deg,#B1083C,#d13729);color:#fff">
                <h5 class="modal-title" id="notesModalLabel">
                    <i class="bi bi-telephone-outbound me-2"></i>Call Notes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">

                {{-- Patient info strip --}}
                <div id="notePatientInfo" class="cm-patient-strip"></div>

                {{-- Existing notes --}}
                <div class="px-4 py-3">
                    <h6 class="fw-semibold text-muted mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">Call History</h6>
                    <div id="notesContainer">
                        <div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm"></div></div>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Add new note form --}}
                @can('call-manager.notes')
                <div class="px-4 py-3">
                    <h6 class="fw-semibold mb-3" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#B1083C">
                        <i class="bi bi-plus-circle me-1"></i>Add New Note
                    </h6>
                    <input type="hidden" id="noteAppointmentId">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Call Type</label>
                            <select class="form-select form-select-sm border-secondary" id="noteCallType">
                                <option value="reminder">Reminder</option>
                                <option value="follow_up">Follow Up</option>
                                <option value="reschedule">Reschedule</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Call Status</label>
                            <select class="form-select form-select-sm border-secondary" id="noteCallStatus">
                                <option value="answered">Answered</option>
                                <option value="no_answer">No Answer</option>
                                <option value="busy">Busy</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Notes</label>
                        <textarea class="form-control border-secondary" id="noteText" rows="3" placeholder="Write your call notes here…"></textarea>
                    </div>
                    <button class="btn btn-sm btn-danger" id="saveNoteBtn">
                        <i class="bi bi-save me-1"></i>Save Note
                    </button>
                </div>
                @endcan

            </div>
        </div>
    </div>
</div>

{{-- ════════════════ EDIT NOTE MODAL ════════════════ --}}
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#1a1a2e;color:#fff">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Note</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editNoteId">
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Call Type</label>
                        <select class="form-select form-select-sm border-secondary" id="editCallType">
                            <option value="reminder">Reminder</option>
                            <option value="follow_up">Follow Up</option>
                            <option value="reschedule">Reschedule</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Call Status</label>
                        <select class="form-select form-select-sm border-secondary" id="editCallStatus">
                            <option value="answered">Answered</option>
                            <option value="no_answer">No Answer</option>
                            <option value="busy">Busy</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Notes</label>
                    <textarea class="form-control border-secondary" id="editNoteText" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-danger" id="updateNoteBtn"><i class="bi bi-save me-1"></i>Update</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<style>
/* ── stat pills ────────────────────────────────────── */
.cm-stat-pill {
    display:inline-flex; align-items:center; gap:8px;
    padding:7px 16px; border-radius:20px;
    border:1px solid; font-size:.84rem;
}

/* ── tabs ──────────────────────────────────────────── */
.cm-tabs { border-bottom:2px solid #dee2e6; gap:4px; }
.cm-tabs .nav-link {
    color:#6b7280; font-weight:500; font-size:.88rem;
    padding:10px 18px; border:none; border-radius:8px 8px 0 0;
    display:flex; align-items:center; gap:6px;
}
.cm-tabs .nav-link:hover { background:#f9fafb; color:#B1083C; }
.cm-tabs .nav-link.active { background:#fff; color:#B1083C; border:2px solid #dee2e6; border-bottom:2px solid #fff; font-weight:600; }
.cm-tab-badge {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:20px; height:20px; border-radius:10px;
    color:#fff; font-size:.72rem; font-weight:700; padding:0 5px;
}

/* ── tab content panel ─────────────────────────────── */
.cm-tab-content { background:#fff; border:2px solid #dee2e6; border-top:none; border-radius:0 8px 8px 8px; }

/* ── search bar ────────────────────────────────────── */
.cm-search-bar {
    display:flex; align-items:center; gap:10px;
    padding:12px 16px; border-bottom:1px solid #f3f4f6;
    background:#fafafa;
}
.cm-search-input {
    border:none; background:transparent; outline:none;
    font-size:.88rem; width:100%;
}

/* ── table ─────────────────────────────────────────── */
.cm-table thead th {
    background:linear-gradient(90deg,#B1083C,#d13729);
    color:#fff; border:none; font-size:.78rem;
    font-weight:600; text-transform:uppercase;
    letter-spacing:.4px; padding:10px 14px; white-space:nowrap;
}
.cm-table tbody td { font-size:.84rem; padding:10px 14px; vertical-align:middle; }
.cm-table tbody tr:hover { background:#fdf5f7; }

/* ── day badge ─────────────────────────────────────── */
.days-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; border-radius:12px; font-size:.75rem; font-weight:600;
}

/* ── status badges ──────────────────────────────────── */
.call-status-answered  { background:#d1fae5; color:#065f46; }
.call-status-no_answer { background:#fee2e2; color:#991b1b; }
.call-status-busy      { background:#fef3c7; color:#92400e; }
.call-status-scheduled { background:#e0e7ff; color:#3730a3; }
.call-type-reminder    { background:#fce7f3; color:#9d174d; }
.call-type-follow_up   { background:#ede9fe; color:#5b21b6; }
.call-type-reschedule  { background:#fef9c3; color:#713f12; }
.call-type-other       { background:#f3f4f6; color:#374151; }

/* ── patient strip ──────────────────────────────────── */
.cm-patient-strip {
    padding:14px 20px; background:#fdf5f7;
    border-bottom:1px solid #fce7ee;
    font-size:.88rem;
}

/* ── note card ──────────────────────────────────────── */
.note-card {
    border:1px solid #f3f4f6; border-radius:8px;
    padding:12px 14px; margin-bottom:10px; background:#fafafa;
}
.note-card:hover { background:#fdf5f7; }
</style>

<script>
$(function () {

    // ── Live search within each tab ──────────────────────────────────
    $(document).on('input', '.cm-search-input', function () {
        const q     = $(this).val().toLowerCase();
        const tbl   = $($(this).data('target'));
        tbl.find('tbody tr').each(function () {
            $(this).toggle($(this).text().toLowerCase().includes(q));
        });
    });

    // ── Open notes modal ─────────────────────────────────────────────
    $(document).on('click', '.btn-open-notes', function () {
        const id = $(this).data('id');
        $('#noteAppointmentId').val(id);
        $('#noteText').val('');
        $('#noteCallType').val('reminder');
        $('#noteCallStatus').val('answered');
        $('#notesContainer').html('<div class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm"></div></div>');
        $('#notesModal').modal('show');
        loadNotes(id);
    });

    // ── Load notes via AJAX ──────────────────────────────────────────
    function loadNotes(appointmentId) {
        $.get('{{ route("call-manager.notes.get", ":id") }}'.replace(':id', appointmentId), function (res) {
            if (!res.success) return;

            const a = res.appointment;
            $('#notePatientInfo').html(
                '<div class="d-flex flex-wrap gap-4">'
                + '<div><i class="bi bi-person me-1" style="color:#B1083C"></i><strong>' + a.name + '</strong></div>'
                + '<div><i class="bi bi-telephone me-1 text-muted"></i>' + (a.phone || '—') + '</div>'
                + '<div><i class="bi bi-calendar3 me-1 text-muted"></i>' + a.date + '</div>'
                + '<div><i class="bi bi-hash me-1 text-muted"></i>' + a.serial + '</div>'
                + '</div>'
            );

            if (res.notes.length === 0) {
                $('#notesContainer').html('<p class="text-muted text-center py-3 small">No call notes yet.</p>');
                return;
            }

            let html = '';
            res.notes.forEach(function (n) {
                html += '<div class="note-card" id="note-card-' + n.id + '">'
                    + '<div class="d-flex justify-content-between align-items-start mb-1">'
                    + '  <div class="d-flex gap-2 flex-wrap">'
                    + '    <span class="days-badge call-type-' + n.call_type + '">' + formatLabel(n.call_type) + '</span>'
                    + '    <span class="days-badge call-status-' + n.call_status + '">' + formatLabel(n.call_status) + '</span>'
                    + '  </div>'
                    + '  <div class="d-flex gap-1 align-items-center">'
                    + '    <small class="text-muted">' + n.created_at + ' &nbsp;·&nbsp; ' + n.called_by + '</small>'
                    + '    @can("call-manager.notes")<button class="btn btn-link btn-sm p-0 ms-2 btn-edit-note" data-id="' + n.id + '" data-type="' + n.call_type + '" data-status="' + n.call_status + '" data-notes="' + escHtml(n.notes || "") + '" title="Edit"><i class="bi bi-pencil text-muted"></i></button>@endcan'
                    + '  </div>'
                    + '</div>'
                    + '<p class="mb-0 small text-dark mt-1">' + (n.notes ? escHtml(n.notes) : '<em class="text-muted">No notes written.</em>') + '</p>'
                    + '</div>';
            });
            $('#notesContainer').html(html);
        });
    }

    // ── Save new note ────────────────────────────────────────────────
    $('#saveNoteBtn').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url:  '{{ route("call-manager.notes.save") }}',
            type: 'POST',
            data: {
                _token:         '{{ csrf_token() }}',
                appointment_id: $('#noteAppointmentId').val(),
                call_type:      $('#noteCallType').val(),
                call_status:    $('#noteCallStatus').val(),
                notes:          $('#noteText').val(),
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Note');
                if (res.success) {
                    $('#noteText').val('');
                    loadNotes($('#noteAppointmentId').val());
                    // Update last-call indicator in table
                    updateRowCallBadge($('#noteAppointmentId').val(), res.note);
                } else {
                    alert('Failed to save note.');
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Note');
                alert('Error saving note.');
            }
        });
    });

    // ── Edit note — open modal ────────────────────────────────────────
    $(document).on('click', '.btn-edit-note', function () {
        $('#editNoteId').val($(this).data('id'));
        $('#editCallType').val($(this).data('type'));
        $('#editCallStatus').val($(this).data('status'));
        $('#editNoteText').val($(this).data('notes'));
        $('#editNoteModal').modal('show');
    });

    // ── Edit note — save ──────────────────────────────────────────────
    $('#updateNoteBtn').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url:  '{{ url("call-manager/notes") }}/' + $('#editNoteId').val(),
            type: 'POST',
            data: {
                _token:       '{{ csrf_token() }}',
                _method:      'PUT',
                call_type:    $('#editCallType').val(),
                call_status:  $('#editCallStatus').val(),
                notes:        $('#editNoteText').val(),
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
                if (res.success) {
                    $('#editNoteModal').modal('hide');
                    loadNotes($('#noteAppointmentId').val());
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Update');
            }
        });
    });

    // ── Update call badge in the table row after saving ───────────────
    function updateRowCallBadge(appointmentId, note) {
        const statusMap = {
            answered:  '<span class="days-badge call-status-answered">Answered</span>',
            no_answer: '<span class="days-badge call-status-no_answer">No Answer</span>',
            busy:      '<span class="days-badge call-status-busy">Busy</span>',
            scheduled: '<span class="days-badge call-status-scheduled">Scheduled</span>',
        };
        $('[data-row-id="' + appointmentId + '"]').find('.last-call-cell').html(
            statusMap[note.call_status] || ''
        );
    }

    function formatLabel(s) {
        return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }
    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
});
</script>
@endsection
