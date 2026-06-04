<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Approval Pending</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; font-family:'Segoe UI',sans-serif; }
        .pending-card { background:#fff; border-radius:16px; box-shadow:0 8px 40px rgba(0,0,0,.12); padding:48px 40px; max-width:480px; width:100%; text-align:center; }
        .logo-bar { background:linear-gradient(90deg,#B1083C,#d13729); color:#fff; border-radius:12px; padding:12px 24px; display:inline-block; font-size:1.3rem; font-weight:700; margin-bottom:28px; letter-spacing:.5px; }
        .status-icon { width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:2.2rem; }
        .status-pending  { background:rgba(245,158,11,.12); color:#f59e0b; }
        .status-approved { background:rgba(16,185,129,.12); color:#10b981; }
        .status-rejected { background:rgba(239,68,68,.12); color:#ef4444; }
        .code-box { background:#f8f9fa; border:2px dashed #dee2e6; border-radius:10px; padding:16px; margin:20px 0; }
        .code-text { font-family:monospace; font-size:1.6rem; font-weight:700; color:#B1083C; letter-spacing:3px; }
        .code-label { font-size:.78rem; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:1px; }
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{ opacity:1; } 50%{ opacity:.5; } }
        .check-btn { background:linear-gradient(90deg,#B1083C,#d13729); border:none; color:#fff; border-radius:8px; padding:10px 28px; font-weight:600; cursor:pointer; }
        .check-btn:hover { opacity:.9; }
        .status-msg { font-size:.875rem; margin-top:16px; }
    </style>
</head>
<body>
<div class="pending-card">

    <div class="logo-bar"><i class="bi bi-shield-lock me-2"></i>RK Tech</div>

    {{-- Dynamic status section --}}
    <div id="statusSection">

        @if($device && $device->status === 'rejected')
        {{-- Rejected state --}}
        <div class="status-icon status-rejected"><i class="bi bi-x-circle-fill"></i></div>
        <h4 class="fw-bold mb-2" style="color:#ef4444">Access Denied</h4>
        <p class="text-muted">Your device access request has been <strong>rejected</strong> by the administrator. Please contact your system administrator.</p>

        @elseif($device && $device->status === 'approved')
        {{-- Approved state - auto-redirect --}}
        <div class="status-icon status-approved"><i class="bi bi-check-circle-fill"></i></div>
        <h4 class="fw-bold mb-2" style="color:#10b981">Device Approved!</h4>
        <p class="text-muted">Your device has been approved. Redirecting you to login…</p>
        <script>setTimeout(() => { window.location.href = '/login'; }, 1500);</script>

        @else
        {{-- Pending state --}}
        <div class="status-icon status-pending">
            <i class="bi bi-hourglass-split pulse"></i>
        </div>
        <h4 class="fw-bold mb-1">Awaiting Device Approval</h4>
        <p class="text-muted" style="font-size:.9rem">Your login request from this browser is waiting for administrator approval. Share the code below with your administrator.</p>

        @if($code)
        <div class="code-box">
            <div class="code-label">Your Device Code</div>
            <div class="code-text">{{ $code }}</div>
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="copyCode()">
                    <i class="bi bi-clipboard me-1"></i>Copy Code
                </button>
            </div>
        </div>
        @endif

        <div id="statusMsg" class="status-msg text-muted">
            <span class="spinner-border spinner-border-sm me-1 text-warning"></span>
            Checking approval status…
        </div>

        <div class="mt-3 d-flex gap-2 justify-content-center">
            <button class="check-btn" onclick="checkNow()">
                <i class="bi bi-arrow-clockwise me-1"></i>Check Status
            </button>
            <a href="/login" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Login
            </a>
        </div>

        <p class="text-muted mt-4" style="font-size:.78rem">
            <i class="bi bi-info-circle me-1"></i>
            This page automatically checks every 15 seconds. Once approved you will be redirected.
        </p>
        @endif

    </div>

</div>

<script>
const CODE = '{{ $code ?? '' }}';

function copyCode() {
    navigator.clipboard?.writeText(CODE).then(() => {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
        setTimeout(() => { btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy Code'; }, 2000);
    });
}

function checkNow() {
    if (!CODE) return;
    fetch('/device/check?code=' + encodeURIComponent(CODE))
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById('statusMsg');
            if (data.status === 'approved') {
                msg.innerHTML = '<span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Approved! Redirecting…</span>';
                setTimeout(() => { window.location.href = '/login?device_approved=1'; }, 1200);
            } else if (data.status === 'rejected') {
                msg.innerHTML = '<span class="text-danger fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>Request rejected. Please contact your administrator.</span>';
            } else if (data.status === 'pending') {
                msg.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split me-1"></i>Still pending…</span>';
            } else {
                msg.innerHTML = '<span class="text-muted">Status unknown.</span>';
            }
        })
        .catch(() => {});
}

// Auto-poll every 15 seconds
@if(!($device && in_array($device?->status, ['approved','rejected'])))
setInterval(checkNow, 15000);
checkNow();
@endif
</script>
</body>
</html>
