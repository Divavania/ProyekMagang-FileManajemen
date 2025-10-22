@extends('layouts.app')
@section('title', 'Dashboard - RadarFiles')

@section('content')
<form class="row g-2 mb-4 mt-3" method="GET" action="{{ route('dashboard') }}">
  <div class="col-md-4">
    <input type="text" name="keyword" class="form-control" placeholder="Search file..." value="{{ request('keyword') }}">
  </div>
  <div class="col-md-3">
    <select name="type" class="form-select">
      <option value="">All</option>
      <option value="pdf" {{ request('type')=='pdf'?'selected':'' }}>PDF</option>
      <option value="image" {{ request('type')=='image'?'selected':'' }}>Image</option>
    </select>
  </div>
  <div class="col-md-2">
    <button class="btn btn-primary w-100">Search</button>
  </div>
</form>

<div class="row text-center mb-4">
  <div class="col-md-3 col-6 mb-3">
    <div class="card p-3 shadow-sm border-0">
      <h6 class="text-muted mb-1">📄 Documents</h6>
      <h4 class="text-success">{{ $totalDocuments }}</h4>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-3">
    <div class="card p-3 shadow-sm border-0">
      <h6 class="text-muted mb-1">🖼️ Images</h6>
      <h4 class="text-info">{{ $totalImages }}</h4>
    </div>
  </div>
  <div class="col-md-3 col-6 mb-3">
    <div class="card p-3 shadow-sm border-0">
      <h6 class="text-muted mb-1">🎞️ Videos</h6>
      <h4 class="text-warning">{{ $totalVideos }}</h4>
    </div>
  </div>
</div>


<div class="row g-3 mb-5">
  @forelse($folders as $folder)
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card shadow-sm border-0 folder-card p-3">
        <div class="d-flex justify-content-between align-items-center">
          <strong>{{ $folder->name }}</strong>

          <!-- Dropdown Aksi -->
          <div class="dropdown">
            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFolderModal{{ $folder->id }}">
                  ✏️ Edit
                </button>
              </li>
              <li>
                <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Yakin hapus folder ini?')">
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
  @empty
    <p class="text-muted">No folders yet.</p>
  @endforelse
</div>

<h5>🗂 Recent Files</h5>
<div class="table-responsive">
  <table class="table table-hover mt-3">
    <thead>
      <tr><th>File Name</th><th>Category</th><th>Uploader</th><th>Upload Date</th></tr>
    </thead>
    <tbody>
      @forelse($files as $file)
      <tr>
        <td>{{ $file->file_name }}</td>
        <td>{{ $file->file_type }}</td>
        <td>{{ $file->uploader->name ?? 'Unknown' }}</td>
        <td>{{ $file->created_at->format('d M Y') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="text-center text-muted">No files found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection