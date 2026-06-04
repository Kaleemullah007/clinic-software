@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex align-items-center">
            <h4 class="fw-bold mb-0">
                <i class="bi bi-bar-chart-line me-2" style="color:#B1083C"></i>Reports
            </h4>
        </div>
        <hr class="my-3">
    </div>

    <div class="row mx-1 g-4">

        {{-- Revenue --}}
        @canany(['reports.revenue','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.revenue') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#B1083C,#e63368)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(177,8,60,.1)">
                            <i class="bi bi-graph-up-arrow" style="color:#B1083C"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Revenue</h6>
                            <p class="rpt-desc">Monthly appointment revenue, doctor &amp; clinic share breakdown</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#B1083C"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Expenses --}}
        @canany(['reports.expenses','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.expenses') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(245,158,11,.1)">
                            <i class="bi bi-cash-stack" style="color:#f59e0b"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Expenses</h6>
                            <p class="rpt-desc">Expense breakdown by category and month</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#f59e0b"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Inventory --}}
        @canany(['reports.inventory','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.inventory') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(14,165,233,.1)">
                            <i class="bi bi-boxes" style="color:#0ea5e9"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Inventory</h6>
                            <p class="rpt-desc">Stock levels, low-stock alerts and total value</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#0ea5e9"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Salaries --}}
        @canany(['reports.salaries','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.salaries') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#10b981,#34d399)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(16,185,129,.1)">
                            <i class="bi bi-wallet2" style="color:#10b981"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Salaries</h6>
                            <p class="rpt-desc">Monthly salary totals per employee</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#10b981"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Doctor Performance --}}
        @canany(['reports.doctor-performance','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.doctor-performance') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(139,92,246,.1)">
                            <i class="bi bi-person-badge" style="color:#8b5cf6"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Doctor Performance</h6>
                            <p class="rpt-desc">Revenue, items and earnings per doctor</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#8b5cf6"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Appointments --}}
        @canany(['reports.appointments','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.appointments') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#B1083C,#e63368)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(177,8,60,.1)">
                            <i class="bi bi-calendar2-check" style="color:#B1083C"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Appointments</h6>
                            <p class="rpt-desc">Monthly volume, paid vs unpaid, new vs returning patients</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#B1083C"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Service Revenue --}}
        @canany(['reports.services','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.services') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#ec4899,#f472b6)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(236,72,153,.1)">
                            <i class="bi bi-scissors" style="color:#ec4899"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Service Revenue</h6>
                            <p class="rpt-desc">Revenue, bookings and discounts per service type</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#ec4899"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Product Gap --}}
        @can('reports.product-gap')
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.product-gap') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#ef4444,#f97316)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(239,68,68,.1)">
                            <i class="bi bi-box-seam" style="color:#ef4444"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Product Gap</h6>
                            <p class="rpt-desc">Products idle in appointments, POS, or both — with stock value sitting unused</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#ef4444"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcan

        {{-- Service Gap --}}
        @can('reports.service-gap')
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.service-gap') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#B1083C,#7c3aed)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(177,8,60,.1)">
                            <i class="bi bi-search-heart" style="color:#B1083C"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Service Gap</h6>
                            <p class="rpt-desc">Top performing services vs unused/dormant — spot gaps by clinic or doctor</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#B1083C"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcan

        {{-- Patients --}}
        @canany(['reports.patients','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.patients') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#06b6d4,#22d3ee)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(6,182,212,.1)">
                            <i class="bi bi-people" style="color:#06b6d4"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Patients</h6>
                            <p class="rpt-desc">Top spenders, new vs returning, retention & inactive</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#06b6d4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- Products Sold --}}
        @canany(['reports.products-sold','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.products-sold') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#f97316,#fb923c)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(249,115,22,.1)">
                            <i class="bi bi-bag-check" style="color:#f97316"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">Products Sold</h6>
                            <p class="rpt-desc">Units sold, revenue, returns and return rate per product</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#f97316"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- POS Report --}}
        @canany(['pos.view','reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('pos.report') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#B1083C,#d13729)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(177,8,60,.1)">
                            <i class="bi bi-shop" style="color:#B1083C"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">POS Report</h6>
                            <p class="rpt-desc">Point-of-sale revenue, product breakdown and daily trends</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#B1083C"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

        {{-- P&L Summary --}}
        @canany(['reports.view'])
        <div class="col-xl-4 col-md-6 col-12">
            <a href="{{ route('reports.summary') }}" class="text-decoration-none">
                <div class="rpt-card h-100">
                    <div class="rpt-accent" style="background:linear-gradient(135deg,#10b981,#34d399)"></div>
                    <div class="rpt-body">
                        <div class="rpt-icon-wrap" style="background:rgba(16,185,129,.1)">
                            <i class="bi bi-graph-up-arrow" style="color:#10b981"></i>
                        </div>
                        <div class="rpt-text">
                            <h6 class="rpt-title">P&amp;L Summary</h6>
                            <p class="rpt-desc">Total income vs expenses, net profit and monthly trend</p>
                        </div>
                        <div class="rpt-arrow">
                            <i class="bi bi-arrow-right-circle-fill" style="color:#10b981"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcanany

    </div>

    {{-- No access fallback --}}
    @php
        $hasAny = auth()->user()->canAny([
            'reports.view','reports.revenue','reports.expenses',
            'reports.inventory','reports.salaries','reports.doctor-performance',
            'reports.appointments','reports.services','reports.patients','reports.products-sold',
        ]);
    @endphp
    @if(!$hasAny)
    <div class="row mx-1 mt-4">
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-lock fs-1 d-block mb-2"></i>
            You don't have permission to view any reports.
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<style>
    /* ── Card shell ─────────────────────────────────────────────────────── */
    .rpt-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,.07);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }
    .rpt-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 28px rgba(0,0,0,.13);
    }

    /* ── Top accent stripe ───────────────────────────────────────────────── */
    .rpt-accent {
        height: 5px;
        width: 100%;
        flex-shrink: 0;
    }

    /* ── Card body ───────────────────────────────────────────────────────── */
    .rpt-body {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 20px 20px 20px;
        flex: 1;
    }

    /* ── Icon circle ─────────────────────────────────────────────────────── */
    .rpt-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .rpt-icon-wrap i {
        font-size: 1.6rem;
    }

    /* ── Text block ──────────────────────────────────────────────────────── */
    .rpt-text {
        flex: 1;
        min-width: 0;
    }
    .rpt-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1a1a2e;
        margin-bottom: 4px;
    }
    .rpt-desc {
        font-size: .78rem;
        color: #6b7280;
        margin: 0;
        line-height: 1.4;
    }

    /* ── Arrow ───────────────────────────────────────────────────────────── */
    .rpt-arrow {
        flex-shrink: 0;
        opacity: 0;
        transition: opacity .2s, transform .2s;
        transform: translateX(-4px);
    }
    .rpt-arrow i { font-size: 1.3rem; }
    .rpt-card:hover .rpt-arrow {
        opacity: 1;
        transform: translateX(0);
    }
</style>
@endsection
