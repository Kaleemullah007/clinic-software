<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('price_col', fn (Product $p) =>
                $p->has_variations
                    ? '<span class="text-muted">—</span>'
                    : 'PKR ' . number_format($p->price, 2)
            )
            ->addColumn('variations_col', fn (Product $p) =>
                $p->has_variations ? $p->variations_count . ' var.' : '—'
            )
            ->addColumn('stock_col', function (Product $p) {
                if (!$p->track_inventory) return '<span class="text-muted">—</span>';
                $qty = $p->inventory?->quantity ?? 0;
                $cls = $qty > 0 ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $cls . '">' . $qty . '</span>';
            })
            ->addColumn('track_col', fn (Product $p) =>
                $p->track_inventory
                    ? '<span class="badge bg-info">Yes</span>'
                    : '<span class="badge bg-secondary">No</span>'
            )
            ->addColumn('status_badge', fn (Product $p) =>
                $p->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn('action', function (Product $p) {
                $edit = $del = '';
                if (auth()->user()->can('products.edit')) {
                    $edit = '<a href="' . route('products.edit', $p) . '" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                             </a>';
                }
                if (auth()->user()->can('products.delete')) {
                    $del = '<form action="' . route('products.destroy', $p) . '" method="POST" class="d-inline"
                                onsubmit="return confirm(\'Delete this product?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                           </form>';
                }
                return $edit . $del;
            })
            ->rawColumns(['price_col', 'variations_col', 'stock_col', 'track_col', 'status_badge', 'action'])
            ->filterColumn('name', fn ($q, $k) => $q->where('products.name', 'like', "%{$k}%"));
    }

    public function query(Product $model): QueryBuilder
    {
        return $model->newQuery()
            ->withCount('variations')
            ->with('inventory');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('productTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(25)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search products…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::make('name')->title('Name'),
            Column::computed('price_col')->title('Price')->orderable(false)->searchable(false),
            Column::computed('variations_col')->title('Variations')->orderable(false)->searchable(false),
            Column::computed('stock_col')->title('Stock')->orderable(false)->searchable(false),
            Column::computed('track_col')->title('Track Inv.')->orderable(false)->searchable(false),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Products_' . date('YmdHis');
    }
}
