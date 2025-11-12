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
                    <td class="d-flex gap-1 flex-wrap">
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