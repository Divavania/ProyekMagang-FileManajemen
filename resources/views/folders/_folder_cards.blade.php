@php
  // fallback: jika parent view tidak mengirim $allFolders, ambil semua folder dari DB
  $allFolders = $allFolders ?? \App\Models\Folder::all();
@endphp

<div class="row g-3 mb-4">
  @forelse ($folders as $folder)
    <div class="col-lg-3 col-md-4 col-sm-6">
      <div class="card shadow-sm border-0 folder-card p-3 h-100 position-relative">

        {{-- Dropdown aksi --}}
        <div class="position-absolute top-0 end-0 m-2" style="z-index:10;">
          <div class="dropdown">
            <button class="btn btn-sm btn-light p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-three-dots-vertical"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFolderModal{{ $folder->id }}">✏️ Rename</button>
              </li>

              <li>
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#moveFolderModal{{ $folder->id }}">📁 Move</button>
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

        {{-- Link ke folder --}}
        <a href="{{ route('folders.show', $folder->id) }}" class="stretched-link text-decoration-none text-dark fw-bold">📁 {{ $folder->name }}</a>
        <p class="text-muted small mt-2 mb-0">Created: {{ $folder->created_at->format('d M Y') }}</p>
      </div>

      {{-- Include modals (kirim $allFolders yang sudah pasti ada) --}}
      @include('folders._modals', ['folder' => $folder, 'allFolders' => $allFolders])

    </div>
  @empty
    <div class="col-12 text-muted">No folders.</div>
  @endforelse
</div>

{{--  CSS  --}}
<style>
  .small-card { font-size: 0.9rem; }
  .folder-card:hover { transform: scale(1.02); transition: 0.2s; }
  .table td, .table th { vertical-align: middle !important; }
</style>