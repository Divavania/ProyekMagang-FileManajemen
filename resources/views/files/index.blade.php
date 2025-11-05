@extends('layouts.app')
@section('title', 'My Files - RadarFiles')
@section('page_title', 'My Files')

@section('content')
<div class="container-fluid mt-3">

  {{-- 🔍 Filter / Search --}}
  <form class="row g-2 mb-4" method="GET" action="{{ route('files.index') }}">
    <div class="col-lg-4 col-md-6 col-sm-8 col-12">
      <input type="text" name="keyword" class="form-control" placeholder="Search files..." value="{{ request('keyword') }}">
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
      <select name="type" class="form-select">
        <option value="">All</option>
        <option value="pdf" {{ request('type')=='pdf'?'selected':'' }}>PDF</option>
        <option value="jpg" {{ request('type')=='jpg'?'selected':'' }}>Image</option>
        <option value="txt" {{ request('type')=='txt'?'selected':'' }}>TXT</option>
      </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4 col-12">
      <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
    </div>
  </form>

  {{-- 📂 Grid Files --}}
  <div class="row g-3">
    @forelse($files as $file)
      @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); @endphp
      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card shadow-sm border-0 h-100 position-relative file-card">

          {{-- Actions dropdown --}}
          <div class="position-absolute top-0 end-0 m-2">
            <div class="dropdown">
              <button class="btn btn-sm btn-light p-1" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a href="{{ route('files.download', $file->id) }}" class="dropdown-item">
                    <i class="bi bi-download me-2"></i> Download
                  </a>
                </li>
                <li>
                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFileModal{{ $file->id }}">
                    <i class="bi bi-pencil me-2"></i> Rename
                  </button>
                </li>
                <li>
                  <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteFileModal{{ $file->id }}">
                    <i class="bi bi-trash me-2"></i> Delete
                  </button>
                </li>
              </ul>
            </div>
          </div>

          {{-- File preview --}}
          <div class="card-body text-center p-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#previewFileModal{{ $file->id }}">
              @if(in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']))
                <img src="{{ asset('storage/' . $file->file_path) }}" alt="{{ $file->file_name }}" class="img-fluid mb-2" style="height:120px;object-fit:cover;">
              @elseif($ext == 'pdf')
                <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-2"></i>
              @elseif($ext == 'txt')
                <i class="bi bi-file-earmark-text fs-1 text-muted mb-2"></i>
              @else
                <i class="bi bi-file-earmark fs-1 text-secondary mb-2"></i>
              @endif
            </a>
            <h6 class="text-truncate" title="{{ $file->file_name }}">{{ $file->file_name }}</h6>
            <p class="text-muted small mb-0">{{ $file->uploader->name ?? 'Unknown' }}</p>
            <p class="text-muted small">{{ $file->created_at->format('d M Y') }}</p>
          </div>
        </div>
      </div>

      {{-- ===== Modal Preview ===== --}}
      <div class="modal fade" id="previewFileModal{{ $file->id }}" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ $file->file_name }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              @php
                $ext = pathinfo($file->file_name, PATHINFO_EXTENSION);
                $fileUrl = asset('storage/' . $file->file_path);
                $txtPath = storage_path('app/public/' . $file->file_path);
              @endphp

              @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','bmp','webp']))
                <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}" class="img-fluid">
              @elseif(strtolower($ext) == 'pdf')
                <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
              @elseif(strtolower($ext) == 'txt')
                @if(file_exists($txtPath))
                  <textarea class="form-control" rows="20" readonly>{{ file_get_contents($txtPath) }}</textarea>
                @else
                  <p class="text-danger">File not found.</p>
                @endif
              @elseif(in_array(strtolower($ext), ['mp4','webm','ogg']))
                <video controls width="100%" style="max-height:600px;">
                  <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                  Your browser does not support the video tag.
                </video>
              @else
                <p class="text-muted">Preview not available for this file type.</p>
              @endif
            </div>
            <div class="modal-footer">
              <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary">Open in New Tab</a>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      {{-- ===== Modal Rename ===== --}}
      <div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <form class="modal-content" method="POST" action="{{ route('files.update', $file->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Rename File</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="text" name="file_name" class="form-control" value="{{ $file->file_name }}" required>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>

      {{-- ===== Modal Delete ===== --}}
      <div class="modal fade" id="deleteFileModal{{ $file->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <form class="modal-content" method="POST" action="{{ route('files.destroy', $file->id) }}">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title">Delete File</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to delete <strong>{{ $file->file_name }}</strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Delete</button>
            </div>
          </form>
        </div>
      </div>

    @empty
      <div class="col-12 text-center text-muted py-5">
        No files found.
      </div>
    @endforelse
  </div>
</div>
@endsection