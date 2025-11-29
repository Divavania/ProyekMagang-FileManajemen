@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas')

@section('content')
<div class="container mt-3">

    <div class="card soft-card border-0">

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" action="{{ route('activity.logs') }}" class="row g-3 mb-4 align-items-end modern-filter">

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Pencarian</label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        class="form-control modern-input"
                        placeholder="Cari nama, aksi, atau deskripsi...">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Tanggal</label>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ request('date') }}" 
                        class="form-control modern-input">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Pengguna</label>
                    <select name="user" class="form-select modern-input">
                        <option value="">Semua Pengguna</option>
                        @foreach($userList as $u)
                            <option value="{{ $u->id }}" 
                                {{ request('user') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100 shadow-sm">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>

            </form>

            <!-- AUTO DELETE INFO -->
            <div class="soft-alert">
                <i class="bi bi-info-circle me-2"></i>
                Log aktivitas disimpan 15 hari sebelum dihapus otomatis.
            </div>

            <div class="section-divider"></div>

            <!-- TABLE -->
            <div class="table-card mt-3">
                 <div class="table-responsive-modern">
                    <table class="table modern-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($logs as $index => $log)
                        <tr>
                            <td>{{ $index + $logs->firstItem() }}</td>

                            <td>
                                @if($log->user)
                                    <span class="fw-semibold">{{ $log->user->name }}</span><br>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="text-danger fw-semibold">User dihapus</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge-action" title="{{ $log->action }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="table-desc-col">
                                {{ $log->description }}
                            </td>

                            <td class="text-muted small">
                                {{ $log->created_at->format('d M Y - H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Tidak ada log aktivitas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="d-flex justify-content-end mt-3">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection

<style>
/* ===========================
   GLOBAL CARD & LAYOUT
=========================== */
.soft-card {
    border-radius: 18px;
    padding: 22px;
    background: #ffffff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

/* Divider tipis */
.section-divider {
    height: 1px;
    background: #e6e9f4;
    margin: 12px 0 !important; 
}

/* ===========================
   FILTER AREA
=========================== */
.modern-filter .form-label {
    color: #3b4a62;
    font-size: 15px;
}

.modern-input {
    border-radius: 12px !important;
    padding: 10px 14px;
    border: 1px solid #d5ddf2;
    background: #fafbff;
    transition: all 0.2s ease;
}

.modern-input:focus {
    border-color: #4f7dff;
    background: #ffffff;
    box-shadow: 0 0 0 0.18rem rgba(79, 125, 255, 0.25);
}

/* ===========================
   ALERT INFO
=========================== */
.soft-alert {
    border-radius: 10px;
    background: #eef4ff;
    border: 1px solid #c8d5ff;
    padding: 10px 14px;
    font-size: 14px;
    color: #244a9a;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.soft-alert i {
    font-size: 18px;
    color: #1d4ffe;
}

/* ===========================
   TABLE WRAPPER
=========================== */
.table-card {
    background: #fff;
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.04);
}

.table-responsive-modern {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

/* Minim white space di kanan */
.table-responsive-modern::-webkit-scrollbar {
    height: 6px;
}
.table-responsive-modern::-webkit-scrollbar-thumb {
    background: #ccd4e6;
    border-radius: 50px;
}

/* ===========================
   TABLE
=========================== */
.modern-table thead {
    background: #eff3ff;
}

.modern-table thead th {
    font-size: 15px !important;
    font-weight: 600 !important;
    padding-bottom: 12px !important;
    border-bottom: 2px solid #eff3ff !important; /* warna kuning */
}

.modern-table td,
.modern-table th {
    vertical-align: middle !important;
    padding: 10px 10px !important;
    font-size: 14px;
}
.table thead th {
    padding-top: 14px;   
    padding-bottom: 14px;
    font-size: 16px;     
}

.modern-table tbody tr {
    transition: 0.18s ease;
    border-bottom: 1px solid #f1f3f9;
}

.modern-table tbody tr:hover {
    background: #f7f9ff !important;
}

/* Kolom Deskripsi */
.modern-table td:nth-child(4) {
    max-width: 340px;
    white-space: normal;
    word-wrap: break-word;
}

/* Badge Aksi */
.badge-action {
    background: #e3edff;
    color: #254dff;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

/* Deskripsi - multiline clamp */
.truncate {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Batasi lebar kolom deskripsi */
.table-desc-col {
    max-width: 420px;     /* bebas kamu atur 300–500px */
    white-space: normal; 
    word-wrap: break-word;
}


/* ===========================
   RESPONSIVE
=========================== */
@media (max-width: 768px) {

    .modern-filter .col-md-4,
    .modern-filter .col-md-3,
    .modern-filter .col-md-2 {
        width: 100% !important;
    }

    .soft-card {
        padding: 16px !important;
    }

    .table-card {
        padding: 12px !important;
    }
}

@media (max-width: 576px) {

    .modern-table thead th,
    .modern-table tbody td {
        font-size: 13px !important;
    }

    .badge-action {
        font-size: 11px !important;
        padding: 4px 8px !important;
    }

    .truncate {
        -webkit-line-clamp: 3 !important;
    }

    .soft-alert {
        font-size: 12px !important;
        padding: 6px 10px !important;
    }
}


</style>


