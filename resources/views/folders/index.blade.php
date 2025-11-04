@extends('layouts.app')

@section('title', 'My Folders - RadarFiles')
@section('page_title', 'My Folders')

@section('content')
<div class="container-fluid px-4 py-3">

  <!-- Search Folder -->
  <form class="row g-2 align-items-center mb-4" method="GET" action="{{ route('folders.index') }}">
    <div class="col-md-6 col-lg-5">
      <input type="text" name="search" class="form-control"
             placeholder="Search folders..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2 col-lg-2">
      <button class="btn btn-primary w-100">Search</button>
    </div>
  </form>

  <!-- Folder Section -->
  <h5 class="mb-3 d-flex align-items-center">
    <i class="bi bi-folder-fill text-warning me-2"></i> All Folders
  </h5>

  <div class="row g-3">
    @foreach ($folders as $folder)
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card shadow-sm border-0 folder-card p-3">
          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('folders.show', $folder->id) }}" class="fw-bold text-decoration-none text-dark">
              📁 {{ $folder->name }}
            </a>

            <div class="dropdown">
              <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFolderModal{{ $folder->id }}">
                    ✏️ Rename
                  </button>
                </li>
                <li>
                  <form action="{{ route('folders.destroy', $folder->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus folder ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">🗑️ Delete</button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Created: {{ $folder->created_at->format('d M Y') }}</p>
        </div>
      </div>

      <!-- Modal Edit Folder -->
      <div class="modal fade" id="editFolderModal{{ $folder->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form class="modal-content" method="POST" action="{{ route('folders.update', $folder->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Folder</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Folder Name</label>
                <input type="text" name="name" class="form-control" value="{{ $folder->name }}" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection