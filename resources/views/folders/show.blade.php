@extends('layouts.app')
@section('title', $folder->name . ' - RadarFiles')

@section('content')
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📁 {{ $folder->name }}</h4>
        <div>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFolderModal">➕ New Folder</button>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addFileModal">📤 Upload File</button>
        </div>
    </div>

    {{-- Subfolders --}}
    <h5>📂 Subfolders</h5>
    <div class="row g-3 mb-4">
        @forelse($subfolders as $sub)
        <div class="col-md-3">
            <div class="card p-3 shadow-sm h-100 position-relative folder-card">

                {{-- Dropdown actions --}}
                <div class="position-absolute top-0 end-0 m-2" style="z-index: 10;">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light p-1" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFolderModal{{ $sub->id }}">
                                    ✏️ Rename
                                </button>
                            </li>
                            <li>
                                <form action="{{ route('folders.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Yakin hapus folder ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">🗑️ Delete</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Link ke folder --}}
                <a href="{{ route('folders.show', $sub->id) }}" class="stretched-link text-decoration-none text-dark">
                    <strong>{{ $sub->name }}</strong>
                    <p class="small text-muted mb-0">Created: {{ $sub->created_at->format('d M Y') }}</p>
                </a>
            </div>
        </div>

        {{-- Modal Edit Subfolder --}}
        <div class="modal fade" id="editFolderModal{{ $sub->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('folders.update', $sub->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Rename Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" value="{{ $sub->name }}" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="col-12 text-muted">No subfolders.</div>
        @endforelse
    </div>

    {{-- Files --}}
    <h5>📄 Files</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>Uploaded</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                <tr>
                    <td>{{ $file->file_name }}</td>
                    <td>{{ $file->file_type }}</td>
                    <td>{{ $file->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Open</a>
                        <a href="{{ route('files.download', $file->id) }}" class="btn btn-sm btn-outline-success">Download</a>
                        <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">No files in this folder.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
@endsection