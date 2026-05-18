<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('roles_html', function (User $user) {
                if ($user->roles->isEmpty()) {
                    return '<span class="text-muted small">No role</span>';
                }
                return $user->roles->map(fn ($r) =>
                    '<span class="badge" style="background:#fce4ec;color:#B1083C;font-size:12px;border:1px solid #B1083C;">'
                    . ucfirst(str_replace('-', ' ', $r->name))
                    . '</span>'
                )->implode(' ');
            })
            ->addColumn('status_badge', fn (User $user) =>
                $user->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>'
            )
            ->addColumn('action', function (User $user) {
                $edit = $del = '';
                if (auth()->user()->can('users.edit')) {
                    $edit = '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-outline-theme me-1">
                                <i class="bi bi-pencil-square"></i> Edit
                             </a>';
                }
                if (auth()->user()->can('users.delete') && !$user->hasRole('super-admin')) {
                    $del = '<form action="' . route('users.destroy', $user->id) . '" method="POST" class="d-inline"
                                onsubmit="return confirm(\'Delete ' . addslashes($user->name) . '?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                           </form>';
                }
                return $edit . $del;
            })
            ->rawColumns(['roles_html', 'status_badge', 'action'])
            ->filterColumn('name', fn ($q, $k) => $q->where('name', 'like', "%{$k}%"))
            ->filterColumn('email', fn ($q, $k) => $q->where('email', 'like', "%{$k}%"));
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('roles')
            ->whereNotIn('id', [1, 13, 17, 64]);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->pageLength(15)
            ->responsive(true)
            ->autoWidth(false)
            ->parameters([
                'dom'      => '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
                'language' => ['searchPlaceholder' => 'Search users…'],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false)->searchable(false)->width(50),
            Column::make('name')->title('Name'),
            Column::make('email')->title('Email'),
            Column::computed('roles_html')->title('Role')->orderable(false)->searchable(false),
            Column::computed('status_badge')->title('Status')->orderable(false)->searchable(false),
            Column::computed('action')->title('Actions')->orderable(false)->searchable(false)->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
