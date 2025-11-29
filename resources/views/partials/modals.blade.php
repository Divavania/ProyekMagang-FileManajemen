<!-- resources/views/partials/modals.blade.php -->

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.folder') }}">
      @csrf
      <div class="modal-header">
        <h5>Tambah Folder Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" placeholder="Enter folder name" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Buat</button>
      </div>
    </form>
  </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.file') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5>Unggah File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="file" name="file" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Ungah</button>
      </div>
    </form>
  </div>
</div>

<!-- Upload Folder Modal (bisa upload folder biasa) -->
<div class="modal fade" id="uploadFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="uploadFolderForm">
      @csrf
      <div class="modal-header">
        <h5>Unggah Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="file" id="folderInput" webkitdirectory directory multiple class="form-control" required>
        <small class="text-muted">Pilih folder, termasuk subfolder dan file di dalamnya.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Unggah</button>
      </div>
    </form>
  </div>
</div>

<!-- Script JS untuk Upload Folder -->
<script>
document.getElementById('uploadFolderForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let input = document.getElementById('folderInput');
    let files = Array.from(input.files);

    if(files.length === 0) {
        alert('Please select a folder');
        return;
    }

    // Struktur folder
    let folders = {};

    files.forEach(file => {
        let path = file.webkitRelativePath.split('/');
        let fileName = path.pop();
        let folderPath = path.join('/');

        if(!folders[folderPath]) {
            folders[folderPath] = {
                name: path[path.length - 1] || '', 
                path: folderPath, 
                files: []
            };
        }

        folders[folderPath].files.push(file);
    });

    // Konversi ke array
    let folderArray = Object.values(folders);

    // Kirim dengan FormData
    let formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');

    folderArray.forEach((folder, idx) => {
        formData.append(`folders[${idx}][name]`, folder.name);
        formData.append(`folders[${idx}][path]`, folder.path);
        if(folder.files){
            folder.files.forEach(file => {
                formData.append(`folders[${idx}][files][]`, file);
            });
        }
    });

    fetch("{{ route('folders.upload.ajax') }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            alert(res.message);
            location.reload();
        } else {
            alert('Upload failed');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed');
    });
});
</script>