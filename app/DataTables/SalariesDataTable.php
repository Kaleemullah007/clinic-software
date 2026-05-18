<?php

namespace App\DataTables;

use App\Models\Salary;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalariesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('staff_col', fn (Salary $s) =>
                $s->user ? e($s->user->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('period_col', fn (Salary $s) =>
                \Carbon\Carbon::create($s->year, $s->month)->format('M Y')
            )
            ->addColumn('amount_col', fn (Salary $s) =>
                'PKR ' . number_format($s->net_salary ?? $s->amount ?? 0, 2)
            )
            ->addColumn('status_badge', fn (Salary $s) =>
                match($s->status ?? 'pending') {
                    'paid'    => '<span class="badge bg-success">Paid</span>',
                    'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
                    default   => '<span class="badge bg-secondary">Pending</span>',
                }
            )
            ->addColumn('processor_col', fn (Salary $s) =>
                $s->processedBy ? e($s->processedBy->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('action', function (Salary $s) {
                $view = '<a href="' . route('salaries.show', $s->id) . '"
                            class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-eye"></i>
                         </a>';
                $del = auth()->user()->can('salaries.delete')
                    ? '<form action="' . route('salaries.destroy', $s->id) . '" method="POST" class="d-inline"
                           onsubmit="return confirm(\'Delete this salary record?\')">
                           ' . csrf_field() . method_field('DELETE') . '
                           <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                       </form>'
                    : '';
                return $view . $del;
            })
            ->rawColumns(['staff_col', 'status_badge', 'processor_col', 'action'])
            ->filterColumn('staff_col', fn ($q, $k) => $q->whereHas('user', fn ($s) => $s->where('name', 'like', "%{$k}%")));
    }

    public function query(Salary $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['user:id,name', 'processedBy:id,name'])
            ->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('salariesTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search salaries…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::computed('staff_col')->title('Staff')->orderable(false),
            Column::computed('period_col')->title('Period')->orderable(false)->searchable(false),
            Column::computed('amount_col')->title('Net Salary')->orderable(false)->searchable(false),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(false),
            Column::computed('processor_col')->title('Processed By')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Salaries_' . date('YmdHis');
    }
}
