@extends('layouts.app')
@section('title', $folder->name . ' - RadarFiles')
@section('page_title', $folder->name)

@section('content')
<div class="container mt-3">

    {{-- Hidden input untuk parent_id agar modal global tahu folder saat ini --}}
    <input type="hidden" name="current_folder_id" value="{{ $folder->id }}">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h4>📁 {{ $folder->name }}</h4>
    </div>

    {{-- Subfolders --}}
    @include('folders._folder_cards', ['folders' => $subfolders, 'allFolders' => $allFolders])

    {{-- Files --}}
    <h5 class="mt-4 mb-3 d-flex align-items-center"><i class="bi bi-file-earmark text-primary me-2"></i> File</h5>

    <div class="row g-3">
        @foreach ($files as $file)
            @php
                $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                $fileUrl = asset('storage/' . $file->file_path);
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $isPDF = $ext === 'pdf';
                $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
                $isAudio = in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']);
                $isDoc = in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                $isText = in_array($ext, ['txt', 'csv', 'json', 'xml']);
                $canPreview = $isImage || $isPDF || $isVideo || $isAudio || $isText;
            @endphp

            <div class="col-6 col-sm-4 col-md-3">
                <div class="card h-100 shadow-sm border-0 rounded file-card position-relative" style="overflow: visible;">

                    {{-- Menu 3 Titik --}}
                    <div class="dropdown position-absolute end-0 mt-1 me-1 file-menu-dropdown" style="z-index: 1000; opacity: 0; transition: opacity 0.2s;">
                        <button class="btn btn-sm btn-light p-1 rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Unduh</a>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFileModal{{ $file->id }}">
                                    <i class="bi bi-pencil me-2"></i>Ubah Nama
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#moveFileModal{{ $file->id }}">
                                    <i class="bi bi-folder-symlink me-2"></i>Pindahkan
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareFileModal{{ $file->id }}">
                                    <i class="bi bi-share me-2"></i>Berbagi
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item toggle-favorite-btn" data-id="{{ $file->id }}">
                                    @if($file->isFavoritedBy(auth()->id()))
                                        <i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit
                                    @else
                                        <i class="bi bi-star me-2"></i>Tambah ke Favorit
                                    @endif
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Pindahkan file ke sampah?')">
                                        <i class="bi bi-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body text-center">
                        @if ($isImage)
                            <img src="{{ $fileUrl }}" class="img-fluid rounded preview-file" data-url="{{ $fileUrl }}" data-type="image" data-name="{{ $file->file_name }}" style="height: 100px; object-fit: cover; width: 100%; cursor:pointer;">
                        @elseif ($isVideo)
                            <div class="position-relative preview-file" data-url="{{ $fileUrl }}" data-type="video" data-name="{{ $file->file_name }}" style="cursor:pointer;">
                                <video class="img-fluid rounded video-thumbnail" style="height: 100px; object-fit: cover; width: 100%;" muted>
                                    <source src="{{ $fileUrl }}">
                                </video>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <div class="bg-dark bg-opacity-75 rounded-circle p-2">
                                        <i class="bi bi-play-fill text-white" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="position-absolute bottom-0 end-0 m-1">
                                    <span class="badge bg-dark bg-opacity-75">
                                        <i class="bi bi-camera-video"></i>
                                    </span>
                                </div>
                            </div>
                        @elseif ($isPDF)
                            <i class="bi bi-file-pdf text-danger preview-file" data-url="{{ $fileUrl }}" data-type="pdf" data-name="{{ $file->file_name }}"  style="font-size: 45px; cursor:pointer;"></i>
                        @elseif ($isAudio)
                            <i class="bi bi-file-music text-success preview-file" data-url="{{ $fileUrl }}" data-type="audio" data-name="{{ $file->file_name }}" style="font-size: 45px; cursor:pointer;"></i>
                        @elseif ($isText)
                            <i class="bi bi-file-text text-secondary preview-file" data-url="{{ $fileUrl }}" data-type="text" data-name="{{ $file->file_name }}" style="font-size: 45px; cursor:pointer;"></i>
                        @elseif ($isDoc)
                            <i class="bi bi-file-earmark-word text-primary" style="font-size: 45px"></i>
                        @else
                            <i class="bi bi-file-earmark-text text-primary" style="font-size: 45px"></i>
                        @endif

                        <p class="mt-2 fw-semibold text-truncate">{{ $file->file_name }}</p>
                        <small class="text-muted">{{ $file->created_at->diffForHumans() }}</small>
                    </div>

                </div>
            </div>

            {{-- Modal Edit File Name --}}
            <div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('files.update', $file->id) }}" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Nama File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="file_name" class="form-control" value="{{ $file->file_name }}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Move File --}}
            <div class="modal fade" id="moveFileModal{{ $file->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('files.move', $file->id) }}" class="modal-content">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Pindahkan File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <select name="folder_id" class="form-select" required>
                                <option value="">Pilih Folder Tujuan</option>
                                @foreach($allFolders as $f)
                                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Pindahkan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Share File --}}
            <div class="modal fade" id="shareFileModal{{ $file->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content bg-white rounded-4" method="POST" action="{{ route('files.share', $file->id) }}">
                        @csrf
                        <div class="modal-header bg-white">
                            <h5 class="modal-title">Bagikan File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Email Pengguna</label>
                                <input type="text" name="email" class="form-control"
                                    placeholder="Masukkan email, pisahkan dengan koma (,)" required>
                            </div>

                            <div class="mb-3">
                                <label>Pesan (opsional)</label>
                                <textarea name="message" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer bg-white">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Bagikan</button>
                        </div>
                    </form>
                </div>
            </div>

        @endforeach
    </div>
</div>

{{-- Modal Preview File --}}
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title" id="previewModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-dark text-center p-4" id="previewModalBody">
                <!-- Content will be inserted here -->
            </div>
            <div class="modal-footer bg-dark border-0">
                <a id="previewDownloadBtn" href="#" class="btn btn-primary" download>
                    <i class="bi bi-download me-2"></i>Unduh File
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Load video thumbnails
    document.querySelectorAll('.video-thumbnail').forEach(video => {
        video.addEventListener('loadeddata', function() {
            this.currentTime = 1; // Capture frame at 1 second
        });
    });

    // Show/hide menu on card hover
    document.querySelectorAll('.file-card').forEach(card => {
        const menu = card.querySelector('.file-menu-dropdown');
        
        card.addEventListener('mouseenter', function() {
            menu.style.opacity = '1';
        });
        
        card.addEventListener('mouseleave', function() {
            if (!menu.querySelector('.dropdown-menu.show')) {
                menu.style.opacity = '0';
            }
        });
        
        menu.addEventListener('show.bs.dropdown', function() {
            menu.style.opacity = '1';
        });
        
        menu.addEventListener('hidden.bs.dropdown', function() {
            if (!card.matches(':hover')) {
                menu.style.opacity = '0';
            }
        });
    });

    // Preview file
    document.querySelectorAll(".preview-file").forEach(element => {
        element.addEventListener("click", function () {
            const url = this.getAttribute("data-url");
            const type = this.getAttribute("data-type");
            const name = this.getAttribute("data-name");
            const modalBody = document.getElementById("previewModalBody");
            const modalTitle = document.getElementById("previewModalTitle");
            const downloadBtn = document.getElementById("previewDownloadBtn");
            
            modalTitle.textContent = name;
            downloadBtn.href = url;
            modalBody.innerHTML = '';
            
            switch(type) {
                case 'image':
                    modalBody.innerHTML = `<img src="${url}" class="img-fluid rounded" style="max-height: 70vh;">`;
                    break;
                    
                case 'pdf':
                    modalBody.innerHTML = `<iframe src="${url}" style="width: 100%; height: 70vh; border: none;" class="rounded"></iframe>`;
                    break;
                    
                case 'video':
                    modalBody.innerHTML = `
                        <video controls autoplay style="max-width: 100%; max-height: 70vh;" class="rounded">
                            <source src="${url}">
                            Browser Anda tidak mendukung video tag.
                        </video>`;
                    break;
                    
                case 'audio':
                    modalBody.innerHTML = `
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 40vh;">
                            <i class="bi bi-file-music text-success mb-4" style="font-size: 80px;"></i>
                            <audio controls autoplay style="width: 100%; max-width: 500px;">
                                <source src="${url}">
                                Browser Anda tidak mendukung audio tag.
                            </audio>
                        </div>`;
                    break;
                    
                case 'text':
                    modalBody.innerHTML = `<div class="text-start"><div class="spinner-border text-light" role="status"></div></div>`;
                    fetch(url)
                        .then(response => response.text())
                        .then(text => {
                            modalBody.innerHTML = `<pre class="text-start text-white p-3 rounded" style="max-height: 70vh; overflow: auto; background-color: #1a1a1a;">${escapeHtml(text)}</pre>`;
                        })
                        .catch(error => {
                            modalBody.innerHTML = `<p class="text-danger">Gagal memuat file.</p>`;
                        });
                    break;
            }
            
            new bootstrap.Modal(document.getElementById("previewModal")).show();
        });
    });
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Toggle favorite
    document.querySelectorAll('.toggle-favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const fileId = this.getAttribute('data-id');
            const button = this;
            
            fetch(`/favorites/file/${fileId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'ok') {
                    const icon = button.querySelector('i');
                    const textNode = button.childNodes[button.childNodes.length - 1];
                    
                    if(data.favorited) {
                        icon.className = 'bi bi-star-fill text-warning me-2';
                        textNode.textContent = 'Hapus dari Favorit';
                    } else {
                        icon.className = 'bi bi-star me-2';
                        textNode.textContent = 'Tambah ke Favorit';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    });
});
</script>
@endpush