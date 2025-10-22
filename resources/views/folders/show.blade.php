@extends('layouts.app')

@section('title', $folder->name . ' - RadarFiles')
@section('page_title', 'Folder: ' . $folder->name)

@section('content')
<div class="container-fluid mt-3">
  <h4 class="mb-4">📁 {{ $folder->name }}</h4>

  <!-- Subfolders -->
  <h6>📂 Subfolders</h6>
  <div class="row g-3 mb-5">
    @forelse($subfolders as $sub)
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card shadow-sm border-0 p-3">
          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('folders.show', $sub->id) }}" class="fw-bold text-decoration-none text-dark">
              📁 {{ $sub->name }}
            </a>

            <div class="dropdown">
              <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">⋮</button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('folders.edit', $sub->id) }}">✏️ Edit</a></li>
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
          <p class="text-muted small mb-0 mt-2">Created: {{ $sub->created_at->format('d M Y') }}</p>
        </div>
      </div>
    @empty
      <p class="text-muted">No subfolders found.</p>
    @endforelse
  </div>
@endsection