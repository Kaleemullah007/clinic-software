<?php

namespace App\DataTables;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PurchasesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('vendor_col', fn (Purchase $p) =>
                $p->vendor ? e($p->vendor->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('total_col', fn (Purchase $p) =>
                'PKR ' . number_format($p->total_amount ?? 0, 2)
            )
            ->addColumn('creator_col', fn (Purchase $p) =>
                $p->creator ? e($p->creator->name) : '<span class="text-muted">—</span>'
            )
            ->addColumn('action', function (Purchase $p) {
                $view = '<a href="' . route('purchases.show', $p->id) . '"
                            class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-eye"></i>
                         </a>';
                $del = auth()->user()->can('purchases.delete')
                    ? '<form action="' . route('purchases.destroy', $p->id) . '" method="POST" class="d-inline"
                           onsubmit="return confirm(\'Delete this purchase?\')">
                           ' . csrf_field() . method_field('DELETE') . '
                           <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                       </form>'
                    : '';
                return $view . $del;
            })
            ->rawColumns(['vendor_col', 'creator_col', 'action'])
            ->filterColumn('po_number', fn ($q, $k) => $q->where('po_number', 'like', "%{$k}%"))
            ->filterColumn('vendor_col', fn ($q, $k) => $q->whereHas('vendor', fn ($s) => $s->where('name', 'like', "%{$k}%")));
    }

    public function query(Purchase $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['vendor:id,name', 'creator:id,name'])
            ->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('purchasesTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search purchases…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::make('po_number')->title('PO #'),
            Column::computed('vendor_col')->title('Vendor')->orderable(false),
            Column::computed('total_col')->title('Total')->orderable(false)->searchable(false),
            Column::make('purchase_date')->title('Date'),
            Column::computed('creator_col')->title('Created By')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Purchases_' . date('YmdHis');
    }
}
