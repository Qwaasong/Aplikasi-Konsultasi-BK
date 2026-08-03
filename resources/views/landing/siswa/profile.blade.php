@extends('layouts.landing')

@section('title', 'Profil Siswa - Bimbingan Konseling')

@push('styles')
<style>
    .siswa-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdf4 100%);
        padding: 24px 16px 48px;
        font-family: 'Poppins', sans-serif;
    }
    .siswa-container {
        max-width: 1100px;
        margin: 0 auto;
    }
    .siswa-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .siswa-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #086375;
        font-size: 1.1rem;
    }
    .siswa-topbar-actions a {
        font-size: 0.85rem;
        color: #64748b;
        text-decoration: none;
        padding: 6px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        transition: all 0.2s;
    }
    .siswa-topbar-actions a:hover {
        background: #f8fafc;
        color: #086375;
        border-color: #086375;
    }
    .hero-card {
        background: linear-gradient(135deg, #086375 0%, #0ea5a7 100%);
        border-radius: 20px;
        padding: 32px;
        color: white;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 24px;
        box-shadow: 0 10px 30px rgba(8,99,117,0.25);
    }
    .hero-avatar {
        width: 76px; height: 76px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 800;
        border: 3px solid rgba(255,255,255,0.4);
        flex-shrink: 0;
    }
    .hero-info h2 { margin: 0 0 4px; font-size: 1.5rem; font-weight: 800; }
    .hero-info p  { margin: 0; opacity: 0.85; font-size: 0.9rem; }
    .hero-badges  { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
    .hero-badge {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 20px; padding: 3px 12px;
        font-size: 0.78rem; font-weight: 600;
    }
    .tabs-bar {
        display: flex; gap: 4px;
        background: white; padding: 6px;
        border-radius: 14px; border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        flex-wrap: wrap;
    }
    .tab-btn {
        flex: 1; min-width: 130px; text-align: center;
        padding: 9px 18px; border-radius: 10px;
        border: none; background: transparent;
        font-size: 0.85rem; font-weight: 600; color: #64748b;
        cursor: pointer; transition: all 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .tab-btn:hover { background: #f1f5f9; color: #086375; }
    .tab-btn.active {
        background: #086375; color: white;
        box-shadow: 0 2px 8px rgba(8,99,117,0.3);
    }
    .card-panel {
        background: white; border-radius: 16px;
        border: 1px solid #e8f4f6; padding: 28px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .card-header-section {
        display: flex; align-items: center; gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px; margin-bottom: 22px;
    }
    .card-icon {
        width: 40px; height: 40px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .card-icon svg { width: 20px; height: 20px; }
    .card-header-section h3 { margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b; }
    .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
    .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
    .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 700px) {
        .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
        .hero-card { flex-direction: column; text-align: center; }
        .hero-badges { justify-content: center; }
    }
    .form-group label {
        display: block; font-size: 0.8rem; font-weight: 600;
        color: #475569; margin-bottom: 6px;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px;
        padding: 10px 14px; font-size: 0.9rem; color: #1e293b;
        background: #fafafa; transition: border-color 0.2s, box-shadow 0.2s;
        font-family: 'Poppins', sans-serif; box-sizing: border-box;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none; border-color: #086375;
        background: white; box-shadow: 0 0 0 3px rgba(8,99,117,0.08);
    }
    .form-group .error-msg { color: #ef4444; font-size: 0.78rem; margin-top: 4px; }
    .checkbox-row {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 14px; border: 1.5px solid #e2e8f0;
        border-radius: 10px; background: #fafafa;
        cursor: pointer; transition: all 0.2s;
    }
    .checkbox-row:hover { border-color: #086375; background: #f0fdf4; }
    .checkbox-row input[type=checkbox] { width: 18px; height: 18px; accent-color: #086375; flex-shrink:0; }
    .checkbox-row span { font-size: 0.9rem; color: #1e293b; }
    .save-btn {
        background: linear-gradient(135deg, #086375, #0ea5a7);
        color: white; border: none; padding: 11px 28px;
        border-radius: 10px; font-size: 0.9rem; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
        box-shadow: 0 3px 10px rgba(8,99,117,0.25);
        font-family: 'Poppins', sans-serif;
    }
    .save-btn:hover { opacity: 0.92; transform: translateY(-1px); }
    .save-btn:active { transform: translateY(0); }
    .alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;
        padding: 12px 16px; border-radius: 10px;
        font-size: 0.88rem; font-weight: 600; margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px;
    }
    .section-divider {
        display: flex; align-items: center; gap: 12px; margin: 24px 0 16px;
    }
    .section-divider span {
        font-size: 0.78rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #94a3b8; white-space: nowrap;
    }
    .section-divider::before, .section-divider::after {
        content: ''; flex: 1; height: 1px; background: #e2e8f0;
    }
    [wire\:loading] { opacity: 0.7; }
</style>
@endpush

@section('content')
<livewire:pages.siswa.profile />
@endsection
