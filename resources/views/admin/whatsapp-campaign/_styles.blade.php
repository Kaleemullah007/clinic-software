<style>
/* Stat cards */
.wc-stat-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:18px 20px; display:flex; flex-direction:column; gap:8px; }
.wc-stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.3rem; }
.wc-stat-num  { font-size:1.3rem; font-weight:700; color:#1a1a2e; line-height:1.1; }
.wc-stat-lbl  { font-size:.75rem; color:#6b7280; }

/* Form card */
.wc-form-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:hidden; margin-bottom:16px; }
.wc-section-head { padding:12px 20px; font-weight:600; font-size:.88rem; color:#fff; background:linear-gradient(90deg,#B1083C,#d13729); }
.wc-section-body { padding:20px; }
.wc-form-footer { padding:16px 20px; background:#fafafa; border-top:1px solid #f3f4f6; }

/* Tips card */
.wc-tips-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:hidden; }

/* DataTable header */
#campaignsTable thead th,
#templatesTable thead th,
#logsTable thead th { background:linear-gradient(90deg,#B1083C,#d13729) !important; color:#fff !important; border:none; font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; padding:10px 14px; white-space:nowrap; }

/* Shadow card */
.shadow-css { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.08); }
.btn-outline-theme { border-color:#B1083C; color:#B1083C; }
.btn-outline-theme:hover { background:#B1083C; color:#fff; }

/* WhatsApp bubble preview */
.wc-whatsapp-preview { background:#e5ddd5; border-radius:12px; padding:16px; min-height:120px; position:relative; }
.wc-whatsapp-preview::before { content:''; position:absolute; top:0; left:0; right:0; bottom:0; background:url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3C/svg%3E"); opacity:.03; border-radius:12px; }

/* Message type radio cards */
.wc-type-card { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border:1.5px solid #dee2e6; border-radius:8px; cursor:pointer; font-size:.85rem; font-weight:500; color:#374151; transition:all .15s; user-select:none; }
.wc-type-card:hover { border-color:#B1083C; color:#B1083C; }
.wc-type-card.selected { border-color:#B1083C; background:rgba(177,8,60,.06); color:#B1083C; font-weight:600; }
</style>
