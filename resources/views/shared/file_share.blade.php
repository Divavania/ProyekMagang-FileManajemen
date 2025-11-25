<h5 class="mb-3">📄 File Dibagikan</h5>

@if($allShared->isEmpty())
  <p class="text-muted">Belum ada file yang dibagikan atau diterima.</p>
@else

{{-- LIST --}}
<div id="fileSharedList">
  <ul class="list-group mb-3">
    @foreach($allShared as $share)
      @php $file = $share->file; @endphp
      @if(!$file) @continue @endif

      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}" class="fw-semibold text-decoration-none">
            {{ $file->file_name }}
          </a>

          <div class="small text-muted">
            @if($share->shared_by == Auth::id())
              Dibagikan ke: {{ $share->receiver->name }} ({{ $share->permission }})
            @else
              Dibagikan oleh: {{ $share->sender->name }} ({{ $share->permission }})
            @endif
          </div>
        </div>

        <div class="dropdown">
          <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
          </button>

          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a href="{{ route('files.download', $file->id) }}" class="dropdown-item">
                <i class="bi bi-download me-2"></i> Download
              </a>
            </li>

            <li>
              <button class="dropdown-item" data-bs-toggle="modal"
                data-bs-target="#renameModal{{ $file->id }}">
                <i class="bi bi-pencil me-2"></i> Rename
              </button>
            </li>

            <li>
              <button class="dropdown-item" data-bs-toggle="modal"
                data-bs-target="#shareModal{{ $file->id }}">
                <i class="bi bi-share me-2"></i> Share
              </button>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <form action="{{ route('share.remove', $share->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="dropdown-item text-danger">
                  <i class="bi bi-trash me-2"></i> Hapus
                </button>
              </form>
            </li>
          </ul>
        </div>

      </li>
    @endforeach
  </ul>
</div>

{{-- GRID --}}
<div id="fileSharedGrid" class="row g-3 d-none">
  @foreach($allShared as $share)
    @php $file = $share->file; @endphp
    @if(!$file) @continue @endif

    <div class="col-xl-3 col-lg-4 col-md-6">
      <div class="card border-0 shadow-sm position-relative h-100 p-3">

        <div class="position-absolute top-0 end-0 m-2">
          <div class="dropdown">
            <button class="btn btn-sm btn-light p-1 position-relative z-3" data-bs-toggle="dropdown">
              <i class="bi bi-three-dots-vertical"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a href="{{ route('files.download', $file->id) }}" class="dropdown-item">
                  <i class="bi bi-download me-2"></i> Download
                </a>
              </li>

              <li>
                <button class="dropdown-item" data-bs-toggle="modal"
                  data-bs-target="#renameModal{{ $file->id }}">
                  <i class="bi bi-pencil me-2"></i> Rename
                </button>
              </li>

              <li>
                <button class="dropdown-item" data-bs-toggle="modal"
                  data-bs-target="#shareModal{{ $file->id }}">
                  <i class="bi bi-share me-2"></i> Share
                </button>
              </li>

              <li><hr class="dropdown-divider"></li>

              <li>
                <form action="{{ route('share.remove', $share->id) }}" method="POST">
                  @csrf @method('DELETE')
                  <button class="dropdown-item text-danger">
                    <i class="bi bi-trash me-2"></i> Hapus
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>

        <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}"
          class="stretched-link fw-bold text-decoration-none">

          @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); @endphp

          @if(in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']))
            <img src="{{ asset('storage/' . $file->file_path) }}" class="img-fluid mb-2"
              style="height:120px;object-fit:cover;">
          @elseif($ext == 'pdf')
            <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-2"></i>
          @elseif($ext == 'txt')
            <i class="bi bi-file-earmark-text fs-1 text-muted mb-2"></i>
          @else
            <i class="bi bi-file-earmark fs-1 text-secondary mb-2"></i>
          @endif

          <div class="mt-2 text-truncate">{{ $file->file_name }}</div>
        </a>

      </div>
    </div>
  @endforeach
</div>

@endif