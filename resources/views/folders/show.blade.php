@extends('layouts.app')
@section('title', $folder->name . ' - RadarFiles')
@section('page_title', $folder->name)

@section('content')
<div class="container mt-3">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4>📁 {{ $folder->name }}</h4>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFolderModal">➕ New Folder</button>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addFileModal">📤 Upload File</button>
        </div>
    </div>

    {{-- Subfolders --}}
    <h5>📂 Subfolders</h5>
    @include('folders._folder_cards', ['folders' => $subfolders, 'allFolders' => $allFolders])

    {{-- Files --}}
    <h5 class="mt-4 mb-3 d-flex align-items-center">
  <i class="bi bi-file-earmark text-primary me-2"></i> Files
</h5>

<div class="row g-3">
  @foreach ($files as $file)
    @php
      $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
      $fileUrl = asset('storage/' . $file->file_path);
    @endphp

    <div class="col-6 col-sm-4 col-md-3">
      <div class="card h-100 shadow-sm border-0 rounded file-card position-relative">

        <div class="dropdown position-absolute end-0 mt-1 me-1">
           <button class="btn btn-sm btn-light p-1 rounded-circle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ $fileUrl }}" target="_blank">Open</a></li>
            <li><a class="dropdown-item" download href="{{ $fileUrl }}">Download</a></li>
            <li>
              <form action="{{ route('files.destroy', $file->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="dropdown-item text-danger" onclick="return confirm('Delete this file?')">
                  Delete
                </button>
              </form>
            </li>
          </ul>
        </div>

        <div class="card-body text-center">

  @php
    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
  @endphp

  @if ($isImage)
  <img src="{{ $fileUrl }}"
       class="img-fluid rounded preview-image"
       data-image="{{ $fileUrl }}"
       style="height: 100px; object-fit: cover; width: 100%; cursor:pointer;">
@else
  <i class="bi bi-file-earmark-text text-primary" style="font-size: 45px"></i>
@endif


  <p class="mt-2 fw-semibold text-truncate">{{ $file->file_name }}</p>
  <small class="text-muted">{{ $file->created_at->diffForHumans() }}</small>
</div>


      </div>
    </div>
  @endforeach
</div>
</div>

{{-- Modal Add Folder --}}
<div class="modal fade" id="addFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('folders.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $folder->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Add New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="name" class="form-control" placeholder="Folder name" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Upload File --}}
<div class="modal fade" id="addFileModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="folder_id" value="{{ $folder->id }}">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="file" name="file" class="form-control mb-3" required>
                <textarea name="description" class="form-control" placeholder="File description (optional)"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Preview Image -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-center">
            <img id="previewModalImage" class="img-fluid rounded" src="">
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".preview-image").forEach(img => {
        img.addEventListener("click", function () {
            const src = this.getAttribute("data-image");
            document.getElementById("previewModalImage").src = src;
            new bootstrap.Modal(document.getElementById("previewModal")).show();
        });
    });
});
</script>