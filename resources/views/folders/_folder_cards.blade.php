@php
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

$allFolders = $allFolders ?? Folder::with('children')->where('created_by', Auth::id())->get();
@endphp

<style>
  .folder-card-grid {
      border-radius: 12px;
      transition: .2s;
      cursor: pointer;
      text-align: center;
      overflow: visible; /* Penting! biar dropdown keluar card */
      position: relative; /* biar dropdown absolute relatif ke card */
  }
  .folder-card-grid .dropdown-menu {
      z-index: 1050; /* pastikan muncul di atas semua */
  }
  .folder-card-grid .stretched-link {
      pointer-events: none; /* biar dropdown clickable */
  }
  .folder-icon {
      font-size: 48px;
      color: #fbbf24;
  }
  .folder-name {
      font-weight: 600;
      font-size: 15px;
      display: block;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
  }
</style>

<div class="row g-3 mb-4">

  @forelse ($folders as $folder)
    <div class="col-6 col-sm-4 col-md-3">
      <div class="card shadow-sm border-0 folder-card-grid p-3 h-100 position-relative">

        {{-- Dropdown --}}
        <div class="position-absolute top-0 end-0 m-2">
          <div class="dropdown">
             <button class="btn btn-sm btn-light p-1 rounded-circle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>

            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button type="button" class="dropdown-item"
                        data-bs-toggle="modal"
                        data-bs-target="#editFolderModal{{ $folder->id }}">
                  <i class="bi bi-pencil me-2"></i>Ubah Nama
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item"
                        data-bs-toggle="modal"
                        data-bs-target="#moveFolderModal{{ $folder->id }}">
                  <i class="bi bi-folder-symlink me-2"></i>Pindahkan
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item"
                        data-bs-toggle="modal"
                        data-bs-target="#shareFolderModal{{ $folder->id }}">
                  <i class="bi bi-share me-2"></i>Bagikan
                </button>
              </li>
              <li>
                <button type="button"
                        class="dropdown-item toggle-favorite-folder"
                        data-id="{{ $folder->id }}"
                        data-url="{{ route('favorites.toggle.folder', $folder->id) }}">
                  @if($folder->isFavoritedBy(auth()->id()))
                    <i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit
                  @else
                    <i class="bi bi-star me-2"></i>Tambah ke Favorit
                  @endif
                </button>
              </li>

              <li>
                <form action="{{ route('folders.destroy', $folder->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus folder ini? Semua file & subfolder juga terhapus!')">
                  @csrf @method('DELETE')
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-trash me-2"></i>Hapus
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>

        {{-- LINK folder --}}
        <a href="{{ route('folders.show', $folder->id) }}" class="stretched-link"></a>

        {{-- Icon --}}
        <div class="folder-icon mb-2">📁</div>

        {{-- Nama --}}
        <span class="folder-name">{{ $folder->name }}</span>

        <p class="text-muted small mb-0">Created: {{ $folder->created_at->format('d M Y') }}</p>

      </div>

      @include('folders._modals', ['folder' => $folder, 'allFolders' => $allFolders])
    </div>

  @empty
    <div class="col-12 text-muted">No folders.</div>
  @endforelse

</div>

<script>
  document.querySelectorAll('.toggle-favorite-folder').forEach(btn => {
    btn.addEventListener('click', async () => {
      const url = btn.dataset.url;

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });

        const data = await res.json();

        if (data.status === 'ok') {
          if (data.favorited) {
            btn.innerHTML = `<i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit`;
          } else {
            btn.innerHTML = `<i class="bi bi-star me-2"></i>Tambah ke Favorit`;
            if (window.location.pathname.includes('/favorites')) {
              const card = btn.closest('.col-6') || btn.closest('.folder-card-grid');
              if (card) card.remove();
            }
          }
        }
      } catch (err) {
        console.error(err);
      }
    });
  });
</script>