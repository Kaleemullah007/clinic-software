@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12">
            <h4 class="fw-bold"><i class="bi bi-bar-chart-line me-2 text-theme-color"></i>Reports</h4>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 g-3">
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('reports.revenue') }}" class="text-decoration-none">
                <div class="report-card shadow-css p-4 text-center h-100">
                    <i class="bi bi-graph-up-arrow fs-1 text-theme-color mb-2 d-block"></i>
                    <h5 class="fw-bold">Revenue</h5>
                    <p class="text-muted small mb-0">Monthly appointment revenue, doctor & clinic share breakdown</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
                <div class="report-card shadow-css p-4 text-center h-100">
                    <i class="bi bi-cash-stack fs-1 text-warning mb-2 d-block"></i>
                    <h5 class="fw-bold">Expenses</h5>
                    <p class="text-muted small mb-0">Expense breakdown by category and month</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('reports.inventory') }}" class="text-decoration-none">
                <div class="report-card shadow-css p-4 text-center h-100">
                    <i class="bi bi-boxes fs-1 text-info mb-2 d-block"></i>
                    <h5 class="fw-bold">Inventory</h5>
                    <p class="text-muted small mb-0">Stock levels, low-stock alerts and total value</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('reports.salaries') }}" class="text-decoration-none">
                <div class="report-card shadow-css p-4 text-center h-100">
                    <i class="bi bi-wallet2 fs-1 text-success mb-2 d-block"></i>
                    <h5 class="fw-bold">Salaries</h5>
                    <p class="text-muted small mb-0">Monthly salary totals per employee</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-sm-6">
            <a href="{{ route('reports.doctor-performance') }}" class="text-decoration-none">
                <div class="report-card shadow-css p-4 text-center h-100">
                    <i class="bi bi-person-badge fs-1 text-purple mb-2 d-block"></i>
                    <h5 class="fw-bold">Doctor Performance</h5>
                    <p class="text-muted small mb-0">Revenue, items and earnings per doctor</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>
    .text-theme-color{color:#B1083C;}
    .shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}
    .report-card{ border-radius:10px; transition:.2s; cursor:pointer; }
    .report-card:hover{ box-shadow:0 4px 16px rgba(177,8,60,.2) !important; transform:translateY(-2px); }
    .text-purple{ color:#6f42c1; }
</style>
@endsection
