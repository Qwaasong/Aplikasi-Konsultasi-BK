@extends('layouts.landing')

@section('title', 'Profil Siswa - Bimbingan Konseling')

@push('styles')
<style>
    .siswa-wrapper {
        min-height: 100vh;
        background-color: #f8fafc;
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
        background: #ffffff;
        padding: 14px 20px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .siswa-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: #086375;
        font-size: 1rem;
    }
    .siswa-brand > span.inline-flex {
        width: 20px !important;
        height: 20px !important;
        flex-shrink: 0 !important;
    }
    .siswa-topbar-actions a {
        font-size: 0.85rem;
        font-weight: 500;
        color: #475569;
        text-decoration: none;
        padding: 6px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
        transition: all 0.15s ease-in-out;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .siswa-topbar-actions a > span.inline-flex {
        width: 16px !important;
        height: 16px !important;
        flex-shrink: 0 !important;
    }
    .siswa-topbar-actions a:hover {
        background: #f8fafc;
        color: #ef4444;
        border-color: #fca5a5;
    }
    .hero-card {
        background-color: #086375;
        border-radius: 16px;
        padding: 28px;
        color: white;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .hero-avatar {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 700;
        border: 2px solid rgba(255,255,255,0.3);
        flex-shrink: 0;
    }
    .hero-info h2 { margin: 0 0 4px; font-size: 1.35rem; font-weight: 700; }
    .hero-info p  { margin: 0; opacity: 0.9; font-size: 0.875rem; }
    .hero-badges  { display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
    .hero-badge {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 6px; padding: 2px 10px;
        font-size: 0.75rem; font-weight: 600;
    }
    .tabs-bar {
        display: flex; gap: 6px;
        background: #ffffff; padding: 6px;
        border-radius: 12px; border: 1px solid #f1f5f9;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        flex-wrap: wrap;
    }
    .tab-btn {
        flex: 1; min-width: 130px; text-align: center;
        padding: 10px 16px; border-radius: 8px;
        border: none; background: transparent;
        font-size: 0.85rem; font-weight: 600; color: #64748b;
        cursor: pointer; transition: all 0.15s ease-in-out;
        font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    }
    .tab-btn > span.inline-flex {
        width: 18px !important;
        height: 18px !important;
        flex-shrink: 0 !important;
    }
    .tab-btn:hover { background: #f8fafc; color: #086375; }
    .tab-btn.active {
        background: #086375; color: #ffffff;
    }
    .card-panel {
        background: #ffffff; border-radius: 16px;
        border: 1px solid #f1f5f9; padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .card-header-section {
        display: flex; align-items: center; gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 14px; margin-bottom: 20px;
    }
    .card-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .card-icon > span.inline-flex {
        width: 20px !important;
        height: 20px !important;
        flex-shrink: 0 !important;
    }
    .card-header-section h3 { margin: 0; font-size: 0.95rem; font-weight: 700; color: #0f172a; }
    .form-row { display: grid; gap: 16px; margin-bottom: 16px; }
    .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
    .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 700px) {
        .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
        .hero-card { flex-direction: column; text-align: center; }
        .hero-badges { justify-content: center; }
    }
    .form-group label {
        display: block; font-size: 0.75rem; font-weight: 600;
        color: #475569; margin-bottom: 6px;
        text-transform: uppercase; letter-spacing: 0.04em;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; border: 1px solid #cbd5e1; border-radius: 8px;
        padding: 9px 12px; font-size: 0.875rem; color: #0f172a;
        background: #ffffff; transition: border-color 0.15s;
        font-family: 'Poppins', sans-serif; box-sizing: border-box;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none; border-color: #086375;
        box-shadow: 0 0 0 3px rgba(8,99,117,0.1);
    }
    .form-group .error-msg { color: #ef4444; font-size: 0.75rem; margin-top: 4px; }
    .checkbox-row {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; border: 1px solid #cbd5e1;
        border-radius: 8px; background: #ffffff;
        cursor: pointer; transition: all 0.15s;
    }
    .checkbox-row:hover { border-color: #086375; }
    .checkbox-row input[type=checkbox] { width: 16px; height: 16px; accent-color: #086375; flex-shrink:0; }
    .checkbox-row span { font-size: 0.875rem; color: #0f172a; }
    .save-btn {
        background-color: #086375;
        color: #ffffff; border: none; padding: 10px 24px;
        border-radius: 8px; font-size: 0.875rem; font-weight: 600;
        cursor: pointer; transition: background-color 0.15s ease-in-out;
        font-family: 'Poppins', sans-serif;
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    }
    .save-btn > span.inline-flex > span.inline-flex {
        width: 16px !important;
        height: 16px !important;
        flex-shrink: 0 !important;
    }
    .save-btn:hover { background-color: #064e5b; }
    .save-btn:active { background-color: #043943; }
    .alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;
        padding: 10px 14px; border-radius: 8px;
        font-size: 0.85rem; font-weight: 600; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .section-divider {
        display: flex; align-items: center; gap: 12px; margin: 20px 0 14px;
    }
    .section-divider span {
        font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.05em;
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
