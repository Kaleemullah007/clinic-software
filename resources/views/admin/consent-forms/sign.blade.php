@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row pt-3 mx-1">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold"><i class="bi bi-pen me-2 text-theme-color"></i>Patient Signature — {{ $consentForm->form_title }}</h4>
            <a href="{{ route('consent-forms.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
        <hr class="my-2">
    </div>
    <div class="row mx-1 mt-2 justify-content-center">
        <div class="col-lg-7 col-12">
            <div class="shadow-css p-4">
                @if($consentForm->form_content)
                <div class="border rounded p-3 mb-4 bg-light" style="max-height:300px;overflow-y:auto">
                    {!! $consentForm->form_content !!}
                </div>
                @endif
                <label class="form-label fw-semibold">Patient Signature <span class="text-danger">*</span></label>
                <canvas id="signatureCanvas" class="border rounded w-100" style="height:200px;cursor:crosshair;background:#fafafa;"></canvas>
                <div class="d-flex gap-2 mt-2">
                    <button class="btn btn-sm btn-outline-secondary" id="clearSig">Clear</button>
                </div>

                <form method="POST" action="{{ route('consent-form.save-signature', $consentForm) }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="signature_image" id="signatureData">
                    <button type="submit" class="btn btn-theme" id="submitSig"><i class="bi bi-check-lg me-1"></i> Confirm Signature</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<style>.text-theme-color{color:#B1083C;}.btn-theme{background:linear-gradient(90deg,#B1083C,#d13729);color:#fff;border:none;}.shadow-css{background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08);}</style>
<script>
// Simple canvas signature pad
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;

function resize(){ canvas.width=canvas.offsetWidth; canvas.height=200; ctx.fillStyle='#fafafa'; ctx.fillRect(0,0,canvas.width,canvas.height); }
window.addEventListener('resize', resize); resize();

canvas.addEventListener('mousedown', e=>{ drawing=true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
canvas.addEventListener('mousemove', e=>{ if(!drawing) return; ctx.lineTo(e.offsetX, e.offsetY); ctx.strokeStyle='#333'; ctx.lineWidth=2; ctx.stroke(); });
canvas.addEventListener('mouseup', ()=>drawing=false);
canvas.addEventListener('touchstart', e=>{ e.preventDefault(); drawing=true; const r=canvas.getBoundingClientRect(); ctx.beginPath(); ctx.moveTo(e.touches[0].clientX-r.left, e.touches[0].clientY-r.top); });
canvas.addEventListener('touchmove', e=>{ e.preventDefault(); if(!drawing) return; const r=canvas.getBoundingClientRect(); ctx.lineTo(e.touches[0].clientX-r.left, e.touches[0].clientY-r.top); ctx.strokeStyle='#333'; ctx.lineWidth=2; ctx.stroke(); });
canvas.addEventListener('touchend', ()=>drawing=false);

document.getElementById('clearSig').onclick = ()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); ctx.fillStyle='#fafafa'; ctx.fillRect(0,0,canvas.width,canvas.height); };

document.querySelector('form').addEventListener('submit', function(e){
    const data = canvas.toDataURL();
    if(data === canvas.toDataURL('image/png').replace('data:image/png;base64,', '')){
        // blank check approximate
    }
    document.getElementById('signatureData').value = data;
});
</script>
@endsection
