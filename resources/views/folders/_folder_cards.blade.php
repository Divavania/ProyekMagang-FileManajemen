@php
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

// fallback: jika parent view tidak mengirim $allFolders, ambil semua folder milik user login
$allFolders = $allFolders ?? Folder::with('children')->where('created_by', Auth::id())->get();
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
                <button type="button" class="dropdown-item"
                        data-bs-toggle="modal"
                        data-bs-target="#editFolderModal{{ $folder->id }}">
                  <i class="bi bi-pencil me-2"></i>Ubah Nama
                </button>
              </li>

              {{-- Pindahkan --}}
              <li>
                <button type="button" class="dropdown-item"
                        data-bs-toggle="modal"
                        data-bs-target="#moveFolderModal{{ $folder->id }}">
                  <i class="bi bi-folder-symlink me-2"></i>Pindahkan
                </button>
              </li>

              {{-- Berbagi Folder --}}
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
                      onsubmit="return confirm('Yakin ingin menghapus folder ini? Semua file dan subfolder juga akan terhapus!')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-trash me-2"></i>Hapus
                  </button>
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

<script>
  document.querySelectorAll('.toggle-favorite-folder').forEach(btn => {
    btn.addEventListener('click', async () => {
      const folderId = btn.dataset.id;
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
          // update icon
          if (data.favorited) {
            btn.innerHTML = `<i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit`;
          } else {
            btn.innerHTML = `<i class="bi bi-star me-2"></i>Tambah ke Favorit`;

            // Jika berada di halaman /favorites → hapus card dari DOM
            const isFavoritesPage = window.location.pathname.includes('/favorites');
            if (isFavoritesPage) {
              const card = btn.closest('.col-lg-3') || btn.closest('.folder-card');
              if (card) card.remove();
            }
          }
        }
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
      }
    });
  });
</script>