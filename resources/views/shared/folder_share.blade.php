<h5 class="mb-3">📁 Folder Dibagikan</h5>

@php
    $folders = $shared_folder_by->merge($shared_folder_with);
@endphp

{{-- LIST --}}
<div id="folderSharedList">
    @if($folders->isEmpty())
        <p class="text-muted">Tidak ada folder yang dibagikan.</p>
    @else
        <ul class="list-group mb-3">
            @foreach($folders as $share)
                @php $folder = $share->folder; @endphp
                @if(!$folder) @continue @endif

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <a href="{{ route('folders.show', $folder->id) }}" class="fw-semibold text-decoration-none">
                            📁 {{ $folder->name }}
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
                            <li><a href="{{ route('folders.downloadZip', $folder->id) }}" class="dropdown-item"><i class="bi bi-download me-2"></i> Unduh</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameModal{{ $folder->id }}"><i class="bi bi-pencil me-2"></i> Ubah Nama</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFolderModal{{ $folder->id }}"><i class="bi bi-share me-2"></i> Berbagi</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('shared.folders.remove', $share->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i> Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

{{-- GRID --}}
<div id="folderSharedGrid" class="row g-3 d-none mt-2">
    @forelse($folders as $share)
        @php $folder = $share->folder; @endphp
        @if(!$folder) @continue @endif

        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card shadow-sm border-0 p-3 position-relative">
                <div class="position-absolute top-0 end-0 m-2">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light p-1 position-relative z-3" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('folders.downloadZip', $folder->id) }}" class="dropdown-item"><i class="bi bi-download me-2"></i> Unduh</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#renameModal{{ $folder->id }}"><i class="bi bi-pencil me-2"></i> Ubah Nama</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFolderModal{{ $folder->id }}"><i class="bi bi-share me-2"></i> Berbagi</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('shared.folders.remove', $share->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-trash me-2"></i> Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="{{ route('folders.show', $folder->id) }}" class="stretched-link fw-bold text-decoration-none">📁 {{ $folder->name }}</a>

                <div class="text-muted small mt-2">
                    @if($share->shared_by == Auth::id())
                        Dibagikan Ke: {{ $share->receiver->name }}
                    @else
                        Dibagikan Oleh: {{ $share->sender->name }}
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <p class="text-muted">Tidak ada folder yang dibagikan.</p>
        </div>
    @endforelse
</div>