<?php

namespace App\DataTables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AppointmentsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('patient_col', fn (Appointment $a) =>
                '<div class="fw-semibold">' . e($a->name) . '</div>'
                . '<small class="text-muted">' . e($a->phone ?? '') . '</small>'
            )
            ->addColumn('doctor_col', fn (Appointment $a) =>
                $a->doctor ? e($a->doctor->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('service_col', function (Appointment $a) {
                $services = $a->appointmentService;
                if (!$services || $services->isEmpty()) return '<span class="text-muted">—</span>';
                return $services->map(fn ($s) =>
                    '<span class="badge bg-light text-dark border">' . e($s->service_name) . '</span>'
                )->implode(' ');
            })
            ->addColumn('amount_col', fn (Appointment $a) =>
                'PKR ' . number_format($a->subtotal_discounted_price_after_discount ?? 0, 0)
            )
            ->addColumn('paid_col', fn (Appointment $a) =>
                $a->is_paid === 'paid'
                    ? '<span class="badge bg-success">Paid</span>'
                    : '<span class="badge bg-warning text-dark">Unpaid</span>'
            )
            ->addColumn('action', function (Appointment $a) {
                $view = '<a href="' . route('appointments.show', $a->id) . '"
                            class="btn btn-sm btn-outline-secondary me-1" title="View">
                            <i class="bi bi-eye"></i>
                         </a>';
                $edit = auth()->user()->can('update', $a)
                    ? '<a href="' . route('appointments.edit', $a->id) . '"
                          class="btn btn-sm btn-outline-theme me-1" title="Edit">
                          <i class="bi bi-pencil-square"></i>
                       </a>'
                    : '';
                $del = auth()->user()->can('delete', $a)
                    ? '<form action="' . route('appointments.destroy', $a->id) . '" method="POST" class="d-inline"
                           onsubmit="return confirm(\'Delete this appointment?\')">
                           ' . csrf_field() . method_field('DELETE') . '
                           <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                               <i class="bi bi-trash3"></i>
                           </button>
                       </form>'
                    : '';
                return $view . $edit . $del;
            })
            ->rawColumns(['patient_col', 'doctor_col', 'service_col', 'paid_col', 'action'])
            // Filter by name / phone via global search
            ->filterColumn('patient_col', fn ($q, $k) =>
                $q->where(fn ($s) =>
                    $s->where('appointments.name', 'like', "%{$k}%")
                      ->orWhere('appointments.phone', 'like', "%{$k}%")
                )
            )
            // Custom filters passed as extra query params
            ->filter(function ($q) {
                if ($v = request('filter_date')) {
                    $q->whereDate('date', $v);
                }
                if (request('filter_paid') !== null && request('filter_paid') !== '') {
                    $paid = request('filter_paid');
                    if ($paid === '1') $q->where('is_paid', 'paid');
                    if ($paid === '0') $q->where('is_paid', '!=', 'paid');
                }
                if ($v = request('filter_patient')) {
                    $q->where('user_id', $v);
                }
            });
    }

    public function query(Appointment $model): QueryBuilder
    {
        $user  = auth()->user();
        $query = $model->newQuery()
            ->with(['appointmentService', 'doctor:id,name'])
            ->latest('date');

        if (!$user->isSuperAdmin() && $user->role === 'doctor') {
            $query->where('doctor_id', $user->id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('appointmentsTable')
            ->columns($this->getColumns())
            ->ajax([
                'url'  => route('appointments.index'),
                'type' => 'GET',
                'data' => 'function(d){
                    d.filter_date    = $("#filterDate").val();
                    d.filter_paid    = $("#filterStatus").val();
                    d.filter_patient = $("#filterPatient").val();
                }',
            ])
            ->orderBy(3, 'desc')   // Date column
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search name or phone…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::computed('patient_col')->title('Patient')->orderable(false),
            Column::computed('doctor_col')->title('Doctor')->orderable(false)->searchable(false),
            Column::make('date')->title('Date'),
            Column::computed('service_col')->title('Services')->orderable(false)->searchable(false),
            Column::computed('amount_col')->title('Amount')->orderable(false)->searchable(false),
            Column::computed('paid_col')->title('Payment')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Appointments_' . date('YmdHis');
    }
}
