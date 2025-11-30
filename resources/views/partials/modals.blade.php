<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.folder') }}">
      @csrf
      <input type="hidden" name="parent_id" id="folderParentId" value="">
      <div class="modal-header">
        <h5>Tambah Folder Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" placeholder="Nama folder" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Buat</button>
      </div>
    </form>
  </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('store.file') }}" enctype="multipart/form-data" id="uploadFileForm">
        @csrf
        <input type="hidden" name="folder_id" id="fileParentId" value="">
        <div class="modal-header">
          <h5>Unggah File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Pilih file dari komputer Anda untuk diunggah.
          </div>
          <input type="file" name="file" id="fileInputGlobal" class="form-control" required>
          <div id="filePreviewGlobal" class="mt-3"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="uploadFileBtnGlobal">
            <span class="spinner-border spinner-border-sm d-none" id="uploadFileSpinnerGlobal"></span>Unggah
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Upload Folder Modal -->
<div class="modal fade" id="uploadFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Unggah Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>Pilih folder dari komputer Anda. Semua file dan subfolder akan diunggah.
        </div>
        <input type="file" id="folderInputGlobal" webkitdirectory directory multiple class="form-control">
        <div id="folderPreviewGlobal" class="mt-3"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="uploadFolderBtnGlobal" disabled>
          <span class="spinner-border spinner-border-sm d-none" id="uploadSpinnerGlobal"></span>Unggah
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Script untuk semua modals -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentFolderIdInput = document.querySelector('input[name="current_folder_id"]');
    
    // ===== MODAL CREATE FOLDER =====
    const createFolderModal = document.getElementById('createFolderModal');
    if (createFolderModal) {
        createFolderModal.addEventListener('show.bs.modal', function() {
            const parentId = currentFolderIdInput ? currentFolderIdInput.value : '';
            document.getElementById('folderParentId').value = parentId;
        });
    }
    
    // ===== MODAL UPLOAD FILE =====
    const uploadFileModal = document.getElementById('uploadFileModal');
    const fileInput = document.getElementById('fileInputGlobal');
    const filePreview = document.getElementById('filePreviewGlobal');
    const uploadFileBtn = document.getElementById('uploadFileBtnGlobal');
    const uploadFileSpinner = document.getElementById('uploadFileSpinnerGlobal');
    
    if (uploadFileModal) {
        uploadFileModal.addEventListener('show.bs.modal', function() {
            const folderId = currentFolderIdInput ? currentFolderIdInput.value : '';
            document.getElementById('fileParentId').value = folderId;
        });
        
        // Reset saat modal ditutup
        uploadFileModal.addEventListener('hidden.bs.modal', function() {
            fileInput.value = '';
            filePreview.innerHTML = '';
        });
    }
    
    // Preview file yang dipilih
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const fileSize = (file.size / 1024).toFixed(2); // KB
                filePreview.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-file-earmark me-2"></i>
                        <strong>${file.name}</strong>
                        <br>
                        <small>${fileSize} KB</small>
                    </div>
                `;
            } else {
                filePreview.innerHTML = '';
            }
        });
    }
    
    // Submit form dengan spinner
    const uploadFileForm = document.getElementById('uploadFileForm');
    if (uploadFileForm) {
        uploadFileForm.addEventListener('submit', function() {
            uploadFileBtn.disabled = true;
            uploadFileSpinner.classList.remove('d-none');
        });
    }
    
    // ===== MODAL UPLOAD FOLDER =====
    const folderInput = document.getElementById('folderInputGlobal');
    const folderPreview = document.getElementById('folderPreviewGlobal');
    const uploadFolderBtn = document.getElementById('uploadFolderBtnGlobal');
    const uploadSpinner = document.getElementById('uploadSpinnerGlobal');
    let selectedFiles = [];

    if (folderInput) {
        folderInput.addEventListener('change', function(e) {
            selectedFiles = Array.from(e.target.files);
            
            if (selectedFiles.length > 0) {
                uploadFolderBtn.disabled = false;
                
                const folderName = selectedFiles[0].webkitRelativePath.split('/')[0];
                folderPreview.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-folder2-open me-2"></i>
                        <strong>${folderName}</strong>
                        <br>
                        <small>${selectedFiles.length} file dipilih</small>
                    </div>
                `;
            } else {
                uploadFolderBtn.disabled = true;
                folderPreview.innerHTML = '';
            }
        });

        uploadFolderBtn.addEventListener('click', function() {
            if (selectedFiles.length === 0) return;

            uploadFolderBtn.disabled = true;
            uploadSpinner.classList.remove('d-none');

            const formData = new FormData();
            
            // Ambil parent_id dari halaman saat ini jika ada
            const parentId = currentFolderIdInput ? currentFolderIdInput.value : '';
            if (parentId) {
                formData.append('parent_id', parentId);
            }

            selectedFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
                formData.append(`paths[${index}]`, file.webkitRelativePath);
            });

            fetch('{{ route("folders.upload") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Folder berhasil diunggah!');
                    location.reload();
                } else {
                    alert('Gagal mengunggah folder: ' + (data.message || 'Unknown error'));
                    uploadFolderBtn.disabled = false;
                    uploadSpinner.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengunggah folder.');
                uploadFolderBtn.disabled = false;
                uploadSpinner.classList.add('d-none');
            });
        });
        
        // Reset saat modal ditutup
        const uploadFolderModal = document.getElementById('uploadFolderModal');
        if (uploadFolderModal) {
            uploadFolderModal.addEventListener('hidden.bs.modal', function() {
                folderInput.value = '';
                folderPreview.innerHTML = '';
                uploadFolderBtn.disabled = true;
                selectedFiles = [];
            });
        }
    }
});
</script>