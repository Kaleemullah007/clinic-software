<style>
/* ── Stat cards ───────────────────────────────────────────────── */
.rpt-stat-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.rpt-stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
}
.rpt-stat-num {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.1;
}
.rpt-stat-lbl {
    font-size: .75rem;
    color: #6b7280;
}

/* ── Panel ────────────────────────────────────────────────────── */
.rpt-panel {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.07);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.rpt-panel-head {
    padding: 14px 20px;
    font-weight: 600;
    font-size: .9rem;
    color: #1a1a2e;
    border-bottom: 1px solid #f3f4f6;
    background: #fafafa;
}
.rpt-panel-body {
    padding: 18px 20px;
    flex: 1;
}

/* ── Table ────────────────────────────────────────────────────── */
.rpt-table thead th {
    background: linear-gradient(90deg,#B1083C 0%,#d13729 100%);
    color: #fff;
    border: none;
    font-size: .78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 10px 14px;
    white-space: nowrap;
}
.rpt-table tbody td {
    font-size: .84rem;
    padding: 9px 14px;
    vertical-align: middle;
}
.rpt-table tfoot td {
    font-size: .84rem;
    padding: 9px 14px;
}
</style>
