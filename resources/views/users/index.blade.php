@extends('layouts.app') 
@section('page_title', 'Daftar Pengguna')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@section('content')

<style>
/* CARD */
.table-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: none;
}

/* TABLE */
.modern-table thead {
    background: rgba(0, 98, 255, 0.07);
}
.modern-table thead th {
    font-weight: 600;
    color: #2b3a55;
    border-bottom: 2px solid #dce6ff;
}
.modern-table tbody tr {
    transition: 0.2s;
}
.modern-table tbody tr:hover {
    background: #f5f7ff !important;
}

/* BADGES */
.badge-modern {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-superadmin { background: #ffe3e3; color: #d40000; }
.badge-admin { background: #e2efff; color: #005fcc; }
.badge-user { background: #e9ffe9; color: #008c35; }

.badge-active { background: #e4ffe5; color: #0c8c0c; }
.badge-nonactive { background: #ffeaea; color: #d52222; }

/* ACTION BUTTON */
.action-btn {
    border: none;
    padding: 6px 10px;
    border-radius: 8px;
    transition: 0.15s ease;
}
.action-btn:hover {
    transform: scale(1.1);
}

/* ALERT */
#alertSuccess {
    transition: opacity 0.7s ease;
}
.alert-fixed {
    position: fixed;
    top: 90px;
    left: 50%;
    transform: translateX(-50%); 
    z-index: 9999;
    width: max-content;
    transition: 0.6s ease;

}
</style>

<div class="container mt-4">

    <!-- Tombol Add -->
    <button class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus"></i> Tambah User
    </button>

    @if(session('success'))
    <div class="alert alert-success shadow-sm alert-fixed" id="alertSuccess">
        {{ session('success') }}
    </div>
        </div>

        <script>
document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.getElementById('alertSuccess');
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            alertBox.style.transform = "translateX(-50%) translateY(-10px)";
        }, 3000);
    }
});
</script>
    @endif

   <div class="table-card table-responsive">
    <table class="table modern-table align-middle">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <!-- ROLE BADGE -->
                        <td>
                            <span class="badge-modern 
                                {{ $user->role == 'superadmin' ? 'badge-superadmin' : '' }}
                                {{ $user->role == 'admin' ? 'badge-admin' : '' }}
                                {{ $user->role == 'user' ? 'badge-user' : '' }}
                            ">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <!-- STATUS BADGE -->
                        <td>
                            <span class="badge-modern 
                                {{ $user->status == 'aktif' ? 'badge-active' : 'badge-nonactive' }}
                            ">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>

                        <td class="text-center">
                            @php $authRole = auth()->user()->role; @endphp

                            @if(($authRole == 'superadmin') || ($authRole == 'admin' && $user->role == 'user'))

                                <!-- Edit -->
                                <button class="action-btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete -->
                                <button class="action-btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <i class="bi bi-trash3"></i>
                                </button>

                            @else
                                <span class="text-muted">Tidak bisa diubah</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada user</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
</div>

<!-- MODAL -->
<!-- Modal Tambah User -->
@include('users.modals.add', ['authRole' => auth()->user()->role])

<!-- Modal Edit dan Hapus Per User -->
@foreach($users as $user)
    @include('users.modals.edit', ['user' => $user, 'authRole' => auth()->user()->role])
    @include('users.modals.delete', ['user' => $user, 'authRole' => auth()->user()->role])
@endforeach

<style>
    /* RESPONSIVE MOBILE */
@media (max-width: 576px) {

    /* Perkecil padding kartu */
    .table-card {
        padding: 12px;
    }

    /* Perkecil teks agar muat */
    .modern-table th,
    .modern-table td {
        font-size: 0.8rem;
        white-space: nowrap;
    }

    /* Badge lebih kecil */
    .badge-modern {
        padding: 4px 8px;
        font-size: 0.65rem;
    }

    /* Tombol aksi lebih kecil */
    .action-btn {
        padding: 4px 6px;
        border-radius: 6px;
    }

    /* Kolom aksi agar rapi */
    td.text-center {
        display: flex;
        gap: 6px;
        justify-content: center;
    }
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .modern-table th,
    .modern-table td {
        font-size: 0.9rem;
    }

    .badge-modern {
        padding: 5px 10px;
        font-size: 0.7rem;
    }
}

</style>
@endsection
