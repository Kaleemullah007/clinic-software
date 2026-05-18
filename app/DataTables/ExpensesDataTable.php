<?php

namespace App\DataTables;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ExpensesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('amount_col', fn (Expense $e) =>
                'PKR ' . number_format($e->amount, 2)
            )
            ->addColumn('clinic_col', fn (Expense $e) =>
                $e->clinic ? e($e->clinic->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('creator_col', fn (Expense $e) =>
                $e->creator ? e($e->creator->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('action', function (Expense $e) {
                $edit = auth()->user()->can('expenses.edit')
                    ? '<a href="' . route('expenses.edit', $e->id) . '"
                          class="btn btn-sm btn-outline-theme me-1">
                          <i class="bi bi-pencil"></i>
                       </a>'
                    : '';
                $del = auth()->user()->can('expenses.delete')
                    ? '<form action="' . route('expenses.destroy', $e->id) . '" method="POST" class="d-inline"
                           onsubmit="return confirm(\'Delete this expense?\')">
                           ' . csrf_field() . method_field('DELETE') . '
                           <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                       </form>'
                    : '';
                return $edit . $del;
            })
            ->rawColumns(['clinic_col', 'creator_col', 'action'])
            ->filterColumn('title',      fn ($q, $k) => $q->where('title', 'like', "%{$k}%"))
            ->filterColumn('category',   fn ($q, $k) => $q->where('category', 'like', "%{$k}%"))
            ->filterColumn('clinic_col', fn ($q, $k) => $q->whereHas('clinic', fn ($s) => $s->where('name', 'like', "%{$k}%")));
    }

    public function query(Expense $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['clinic:id,name', 'creator:id,name'])
            ->latest('expense_date');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('expensesTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search expenses…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::make('title')->title('Title'),
            Column::computed('amount_col')->title('Amount')->orderable(false)->searchable(false),
            Column::make('expense_date')->title('Date'),
            Column::computed('clinic_col')->title('Clinic')->orderable(false),
            Column::make('category')->title('Category'),
            Column::computed('creator_col')->title('Added By')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Expenses_' . date('YmdHis');
    }
}
