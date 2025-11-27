@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas')

@section('content')
<div class="container mt-3">

    <div class="card shadow-sm border-0">

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" action="{{ route('activity.logs') }}" class="row g-3 mb-3">

                <div class="col-md-4">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        class="form-control"
                        placeholder="Cari nama, aksi, atau deskripsi...">
                </div>

                <div class="col-md-3">
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ request('date') }}" 
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <select name="user" class="form-select">
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
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>

            </form>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
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
                                    <strong>{{ $log->user->name }}</strong><br>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                @else
                                    <span class="text-danger">User dihapus</span>
                                @endif
                            </td>

                            <td>
                                    <span class="badge bg-info text-dark truncate" title="{{ $log->action }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td style="max-width: 250px;">
                                {{ $log->description }}
                            </td>

                            <td>
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

            <div class="d-flex justify-content-end">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>
@endsection

<style>
    table tbody tr td {
        max-width: 280px;
        white-space: normal;
        word-wrap: break-word;
        vertical-align: top;
    }

    .truncate {
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        cursor: pointer;
    }
</style>
