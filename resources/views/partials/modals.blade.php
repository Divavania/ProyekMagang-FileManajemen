<!-- resources/views/partials/modals.blade.php -->

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.folder') }}">
      @csrf
      <div class="modal-header">
        <h5>Create New Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" placeholder="Enter folder name" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Create</button>
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
        <h5>Upload File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="file" name="file" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>

<!-- Upload Folder Modal -->
<div class="modal fade" id="uploadFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.upload.folder') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5>Upload Folder (ZIP)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="file" name="folder_zip" accept=".zip" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>