@extends('layouts.app')
@section('title', 'Notifications')
@section('page_title', 'Notifikasi')

@section('content')
<div class="container mt-4">
  @php
      $user = Auth::user();
      $unreadCount = $user ? $user->unreadNotifications->count() : 0;
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
      @if($unreadCount > 0)
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-check2-all me-1"></i> Tandai Semua Dilihat
          </button>
        </form>
      @endif

      <button id="toggleSelectBtn" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-check2-square me-1"></i> Pilih Semua
      </button>

      <form id="deleteSelectedForm" method="POST" action="{{ route('notifications.deleteSelected') }}" class="d-none">
        @csrf
        <input type="hidden" name="selected_ids" id="selectedIdsInput">
        <button type="submit" id="deleteSelectedBtn" class="btn btn-sm btn-danger">
          <i class="bi bi-trash me-1"></i> Hapus yang Dipilih
        </button>
      </form>
    </div>
  </div>

  <form id="notifListForm">
    <div class="table-responsive shadow-sm rounded-3">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th style="width: 50px;">
              <div class="form-check d-none checkbox-container">
                <input type="checkbox" class="form-check-input" id="checkAll">
              </div>
            </th>
            <th>Notifikasi</th>
            <th style="width: 180px;">Waktu</th>
            <th style="width: 100px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($notifications as $notification)
            <tr class="{{ is_null($notification->read_at) ? 'table-light' : '' }}">
              {{-- Checkbox --}}
              <td class="text-center">
                <div class="form-check d-none checkbox-container">
                  <input type="checkbox" class="form-check-input notif-checkbox"
                         value="{{ $notification->id }}" id="notif_{{ $notification->id }}">
                </div>
              </td>

              {{-- Isi Notifikasi --}}
              <td>
                <a href="{{ $notification->data['link'] ?? '#' }}" class="text-decoration-none text-dark">
                  <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</div>
                  <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                </a>
              </td>

              {{-- Waktu --}}
              <td class="text-center text-muted small">
                {{ $notification->created_at->diffForHumans() }}
              </td>

              {{-- Aksi --}}
              <td class="text-center">
                @if(is_null($notification->read_at))
                    <a href="{{ route('notifications.read', $notification->id) }}"
                      class="text-decoration-none">
                      <i class="bi bi-eye fs-5 text-primary"></i>
                    </a>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                Tidak ada notifikasi saat ini
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </form>

  <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    {{-- Info halaman --}}
    <div class="text-muted small">
      Hal {{ $notifications->currentPage() }}/{{ $notifications->lastPage() }}
      ({{ $notifications->total() }} data)
    </div>

    <div class="d-flex align-items-center gap-2">
      {{-- Pilih jumlah baris --}}
      <form method="GET" action="{{ url()->current() }}" id="perPageForm" class="mb-0">
        <select name="per_page" class="form-select form-select-sm" style="width:auto;" 
                onchange="document.getElementById('perPageForm').submit();">
          <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 baris</option>
          <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
          <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
          <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
        </select>
      </form>

      {{-- Navigasi halaman --}}
      <nav aria-label="Pagination" class="mb-0">
        <ul class="pagination pagination-sm mb-0">
          {{-- Tombol awal --}}
          <li class="page-item {{ $notifications->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $notifications->appends(request()->except('page'))->url(1) }}" aria-label="First">&laquo;</a>
          </li>

          {{-- Tombol sebelumnya --}}
          <li class="page-item {{ $notifications->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $notifications->previousPageUrl() }}" aria-label="Previous">&lt;</a>
          </li>

          {{-- Input ke halaman tertentu --}}
          <li class="page-item">
            <form id="gotoPageForm" method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-1 mb-0">
              @foreach(request()->except('page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
              @endforeach
              <input type="number" min="1" max="{{ $notifications->lastPage() }}" 
                    name="page" value="{{ $notifications->currentPage() }}" 
                    class="form-control form-control-sm text-center" style="width: 70px;"
                    onchange="document.getElementById('gotoPageForm').submit();">
            </form>
          </li>

          {{-- Tombol berikutnya --}}
          <li class="page-item {{ !$notifications->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $notifications->nextPageUrl() }}" aria-label="Next">&gt;</a>
          </li>

          {{-- Tombol akhir --}}
          <li class="page-item {{ !$notifications->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $notifications->appends(request()->except('page'))->url($notifications->lastPage()) }}" aria-label="Last">&raquo;</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>

  <div class="mt-3">
    {{ $notifications->links() }}
  </div>
</div>

{{-- === Script Select & Delete === --}}
<script>
  const toggleSelectBtn = document.getElementById('toggleSelectBtn');
  const deleteForm = document.getElementById('deleteSelectedForm');
  const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
  const checkboxes = document.querySelectorAll('.notif-checkbox');
  const checkContainers = document.querySelectorAll('.checkbox-container');
  const selectedIdsInput = document.getElementById('selectedIdsInput');
  let selectMode = false;

  toggleSelectBtn.addEventListener('click', () => {
    selectMode = !selectMode;

    checkContainers.forEach(el => el.classList.toggle('d-none', !selectMode));

    if (selectMode) {
      deleteForm.classList.remove('d-none');
      toggleSelectBtn.classList.replace('btn-outline-secondary', 'btn-secondary');
      toggleSelectBtn.innerHTML = '<i class="bi bi-x-square me-1"></i> Batal';
      checkboxes.forEach(cb => cb.checked = true);
      updateSelected();
    } else {
      deleteForm.classList.add('d-none');
      toggleSelectBtn.classList.replace('btn-secondary', 'btn-outline-secondary');
      toggleSelectBtn.innerHTML = '<i class="bi bi-check2-square me-1"></i> Pilih Semua';
      checkboxes.forEach(cb => cb.checked = false);
      updateSelected();
    }
  });

  function updateSelected() {
    const selected = Array.from(checkboxes)
      .filter(c => c.checked)
      .map(c => c.value);
    selectedIdsInput.value = selected.join(',');
    deleteSelectedBtn.disabled = selected.length === 0;
  }

  checkboxes.forEach(cb => cb.addEventListener('change', updateSelected));
</script>
@endsection