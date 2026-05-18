<?php

namespace App\DataTables;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VendorsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('status_badge', fn (Vendor $v) =>
                $v->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn('action', function (Vendor $v) {
                $edit = $del = '';
                if (auth()->user()->can('vendors.edit')) {
                    $edit = '<a href="' . route('vendor.edit', $v) . '" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                             </a>';
                }
                if (auth()->user()->can('vendors.delete')) {
                    $del = '<form action="' . route('vendor.destroy', $v) . '" method="POST" class="d-inline"
                                onsubmit="return confirm(\'Delete this vendor?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                           </form>';
                }
                return $edit . $del;
            })
            ->rawColumns(['status_badge', 'action'])
            ->filterColumn('name',    fn ($q, $k) => $q->where('name', 'like', "%{$k}%"))
            ->filterColumn('company', fn ($q, $k) => $q->where('company', 'like', "%{$k}%"))
            ->filterColumn('phone',   fn ($q, $k) => $q->where('phone', 'like', "%{$k}%"))
            ->filterColumn('email',   fn ($q, $k) => $q->where('email', 'like', "%{$k}%"));
    }

    public function query(Vendor $model): QueryBuilder
    {
        return $model->newQuery()->latest();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('vendorTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(25)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search vendors…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::make('name')->title('Name'),
            Column::make('company')->title('Company'),
            Column::make('phone')->title('Phone'),
            Column::make('email')->title('Email'),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Vendors_' . date('YmdHis');
    }
}
