@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page_title', ' Notifkasi')

@section('content')
<div class="container-fluid py-4">

    {{-- Tombol Tandai Semua Dibaca --}}
   <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
  @csrf
  <button type="submit" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-check2-circle"></i> Tandai semua dibaca
  </button>
</form>
  </div>

  {{-- Daftar Notifikasi --}}
  @forelse ($notifications as $notification)
    <a href="{{ route('notifications.read', $notification->id) }}" 
       class="text-decoration-none text-dark">
      <div class="card mb-2 border-0 shadow-sm {{ is_null($notification->read_at) ? 'bg-light' : '' }}">
        <div class="card-body d-flex align-items-center">
          <div class="me-3">
            <i class="bi bi-info-circle text-primary fs-4"></i>
          </div>
          <div class="flex-grow-1">
            <p class="mb-1 fw-semibold">
              {{ $notification->data['message'] ?? 'Tidak ada detail notifikasi.' }}
            </p>
            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
          </div>
          @if (is_null($notification->read_at))
            <span class="badge bg-primary">Baru</span>
          @endif
        </div>
      </div>
    </a>
  @empty
    <div class="text-center mt-5">
      <i class="bi bi-bell-slash fs-1 text-muted"></i>
      <p class="text-muted mt-2">Tidak ada notifikasi saat ini.</p>
    </div>
  @endforelse

</div>
@endsection
