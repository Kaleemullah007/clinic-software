<?php

namespace App\DataTables;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PrescriptionsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('patient_col', fn (Prescription $p) =>
                $p->patient ? e($p->patient->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('doctor_col', fn (Prescription $p) =>
                $p->doctor ? e($p->doctor->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('action', function (Prescription $p) {
                $view = '<a href="' . route('prescription.show', $p->id) . '"
                            class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-eye"></i>
                         </a>';
                $edit = auth()->user()->can('update', $p)
                    ? '<a href="' . route('prescription.edit', $p->id) . '"
                          class="btn btn-sm btn-outline-theme me-1">
                          <i class="bi bi-pencil-square"></i>
                       </a>'
                    : '';
                $del = auth()->user()->can('delete', $p)
                    ? '<form action="' . route('prescription.destroy', $p->id) . '" method="POST" class="d-inline"
                           onsubmit="return confirm(\'Delete this prescription?\')">
                           ' . csrf_field() . method_field('DELETE') . '
                           <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                       </form>'
                    : '';
                return $view . $edit . $del;
            })
            ->rawColumns(['patient_col', 'doctor_col', 'action'])
            ->filterColumn('medicine',    fn ($q, $k) => $q->where('medicine', 'like', "%{$k}%"))
            ->filterColumn('patient_col', fn ($q, $k) => $q->whereHas('patient', fn ($s) => $s->where('name', 'like', "%{$k}%")));
    }

    public function query(Prescription $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['patient:id,name', 'doctor:id,name'])
            ->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('prescriptionsTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search prescriptions…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::computed('patient_col')->title('Patient')->orderable(false),
            Column::make('medicine')->title('Medicine'),
            Column::make('dosage')->title('Dosage'),
            Column::computed('doctor_col')->title('Doctor')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Date')->render("moment(data).format('DD MMM YYYY')"),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Prescriptions_' . date('YmdHis');
    }
}
