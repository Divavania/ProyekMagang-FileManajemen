@extends('layouts.app')
@section('title', 'File Dibagikan')

@section('content')
<div class="container-fluid mt-3">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📂 Semua File Dibagikan</h4>
    <div>
      <button id="listViewBtn" class="btn btn-primary btn-sm me-1" title="Tampilan Daftar">
        <i class="bi bi-list-ul"></i>
      </button>
      <button id="gridViewBtn" class="btn btn-outline-primary btn-sm" title="Tampilan Grid">
        <i class="bi bi-grid-fill"></i>
      </button>
    </div>
  </div>

  @php
    $allShared = $shared_by->merge($shared_with);
  @endphp

  @if($allShared->isEmpty())
    <p class="text-muted">Belum ada file yang dibagikan atau diterima.</p>
  @else
    {{-- LIST VIEW --}}
    <div id="sharedList" class="mb-4">
      <ul class="list-group">
        @foreach($allShared as $share)
          @php 
            $file = $share->file; 
            $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); 
            $partner = $share->receiver->name ?? $share->sender->name ?? 'Tidak diketahui';
          @endphp
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}" class="fw-semibold text-decoration-none">
                {{ $file->file_name }}
              </a>
              <div class="small text-muted">
                @if($share->shared_by == Auth::id())
                  Dibagikan ke: {{ $partner }} ({{ $share->permission }})
                @else
                  Dibagikan oleh: {{ $partner }} ({{ $share->permission }})
                @endif
              </div>
            </div>
            <div class="dropdown">
              <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a href="{{ route('files.download', $file->id) }}" class="dropdown-item"><i class="bi bi-download me-2"></i> Download</a></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameModal{{ $file->id }}"><i class="bi bi-pencil me-2"></i> Rename</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal{{ $file->id }}"><i class="bi bi-share me-2"></i> Share</button></li>
                <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $file->id }}"><i class="bi bi-trash me-2"></i> Delete</button></li>
              </ul>
            </div>
          </li>
        @endforeach
      </ul>
    </div>

    {{-- GRID VIEW --}}
    <div id="sharedGrid" class="row g-3 d-none mb-4">
      @foreach($allShared as $share)
        @php 
          $file = $share->file; 
          $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); 
          $partner = $share->receiver->name ?? $share->sender->name ?? 'Tidak diketahui';
        @endphp
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
          <div class="card border-0 shadow-sm position-relative h-100">
            <div class="position-absolute top-0 end-0 m-2">
              <div class="dropdown">
                <button class="btn btn-sm btn-light p-1" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a href="{{ route('files.download', $file->id) }}" class="dropdown-item"><i class="bi bi-download me-2"></i> Download</a></li>
                  <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameModal{{ $file->id }}"><i class="bi bi-pencil me-2"></i> Rename</button></li>
                  <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal{{ $file->id }}"><i class="bi bi-share me-2"></i> Share</button></li>
                  <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $file->id }}"><i class="bi bi-trash me-2"></i> Delete</button></li>
                </ul>
              </div>
            </div>

            <div class="card-body text-center p-3">
              <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
                @if(in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']))
                  <img src="{{ asset('storage/' . $file->file_path) }}" class="img-fluid mb-2" style="height:120px;object-fit:cover;">
                @elseif($ext == 'pdf')
                  <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-2"></i>
                @elseif($ext == 'txt')
                  <i class="bi bi-file-earmark-text fs-1 text-muted mb-2"></i>
                @else
                  <i class="bi bi-file-earmark fs-1 text-secondary mb-2"></i>
                @endif
              </a>
              <h6 class="text-truncate">{{ $file->file_name }}</h6>
              <p class="text-muted small mb-0">
                @if($share->shared_by == Auth::id())
                  Ke: {{ $partner }}
                @else
                  Dari: {{ $partner }}
                @endif
              </p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- ==== MODAL PREVIEW ==== --}}
@foreach($allShared as $share)
  @php 
    $file = $share->file; 
    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); 
  @endphp
  <div class="modal fade" id="previewModal{{ $file->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ $file->file_name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          @php
            $fileUrl = asset('storage/' . $file->file_path);
            $txtPath = storage_path('app/public/' . $file->file_path);
          @endphp
          @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
            <img src="{{ $fileUrl }}" class="img-fluid">
          @elseif($ext == 'pdf')
            <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
          @elseif($ext == 'txt' && file_exists($txtPath))
            <textarea class="form-control" rows="20" readonly>{{ file_get_contents($txtPath) }}</textarea>
          @elseif(in_array($ext, ['mp4','webm','ogg']))
            <video controls width="100%" style="max-height:600px;"><source src="{{ $fileUrl }}" type="video/{{ $ext }}"></video>
          @else
            <p class="text-muted">Preview tidak tersedia untuk tipe file ini.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endforeach

{{-- === Script Toggle View === --}}
<script>
  const listBtn = document.getElementById('listViewBtn');
  const gridBtn = document.getElementById('gridViewBtn');
  const sharedList = document.getElementById('sharedList');
  const sharedGrid = document.getElementById('sharedGrid');

  listBtn.addEventListener('click', () => {
    sharedList.classList.remove('d-none');
    sharedGrid.classList.add('d-none');
    listBtn.classList.replace('btn-outline-primary', 'btn-primary');
    gridBtn.classList.replace('btn-primary', 'btn-outline-primary');
  });

  gridBtn.addEventListener('click', () => {
    sharedList.classList.add('d-none');
    sharedGrid.classList.remove('d-none');
    gridBtn.classList.replace('btn-outline-primary', 'btn-primary');
    listBtn.classList.replace('btn-primary', 'btn-outline-primary');
  });
</script>
@endsection