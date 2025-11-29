@extends('layouts.app')
@section('title', 'File Dibagikan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">

  <!-- Navigation Tabs -->
  <ul class="nav nav-tabs mb-4">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('shared.index') ? 'active' : '' }}"
         href="{{ route('shared.index') }}">
        Semua File Dibagikan
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('shared.public') ? 'active' : '' }}"
         href="{{ route('shared.public') }}">
        Semua File Publik
      </a>
    </li>
  </ul>
    <div class="d-flex gap-2">

      <!-- Sort -->
      <form method="GET">
        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
          <option value="latest" {{ request('sort') == 'oldest' ? '' : 'selected' }}>Terbaru</option>
          <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
        </select>
      </form>

      <!-- LIST VIEW -->
      <button id="globalListBtn" class="btn btn-primary btn-sm" title="List View">
        <i class="bi bi-list-ul"></i>
      </button>

      <!-- GRID VIEW -->
      <button id="globalGridBtn" class="btn btn-outline-primary btn-sm" title="Grid View">
        <i class="bi bi-grid-fill"></i>
      </button>
    </div>
  </div>

  @include('shared.folder_share')
  @include('shared.folder_share_modals', [ 'allShared' => $allShared, 'users' => $users ])
  <hr>
  @include('shared.file_share')
  @include('shared.file_share_modals', [ 'allShared' => $allShared, 'users' => $users ])
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
    const globalListBtn = document.getElementById('globalListBtn');
    const globalGridBtn = document.getElementById('globalGridBtn');

    const lists = [document.getElementById('folderSharedList'), document.getElementById('fileSharedList')].filter(Boolean);
    const grids = [document.getElementById('folderSharedGrid'), document.getElementById('fileSharedGrid')].filter(Boolean);

    const hasListContent = lists.some(el => el.querySelectorAll('li, p').length > 0);
    const hasGridContent = grids.some(el => el.children.length > 0);

    if (!hasListContent && !hasGridContent) {
        globalListBtn.disabled = true;
        globalGridBtn.disabled = true;
        return;
    }

    function showList() {
        lists.forEach(el => el.classList.remove('d-none'));
        grids.forEach(el => el.classList.add('d-none'));
        globalListBtn.classList.add('btn-primary'); globalListBtn.classList.remove('btn-outline-primary');
        globalGridBtn.classList.add('btn-outline-primary'); globalGridBtn.classList.remove('btn-primary');
    }

    function showGrid() {
        lists.forEach(el => el.classList.add('d-none'));
        grids.forEach(el => el.classList.remove('d-none'));
        globalGridBtn.classList.add('btn-primary'); globalGridBtn.classList.remove('btn-outline-primary');
        globalListBtn.classList.add('btn-outline-primary'); globalListBtn.classList.remove('btn-primary');
    }

    showList(); 
    globalListBtn.addEventListener('click', showList);
    globalGridBtn.addEventListener('click', showGrid);
}, 10);
});
</script>
@endpush