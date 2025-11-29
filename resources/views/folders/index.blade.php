@extends('layouts.app')

@section('title', 'My Folders - RadarFiles')
@section('page_title', 'Folder Saya')

@section('content')
<div class="container-fluid px-4 py-3">

  <!-- Search Folder -->
  <form class="row g-2 align-items-center mb-4" method="GET" action="{{ route('folders.index') }}">
    <div class="col-md-6 col-lg-5">
      <input type="text" name="search" class="form-control"
             placeholder="Cari folder..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2 col-lg-2">
      <button class="btn btn-primary w-100">Cari</button>
    </div>
  </form>

  <!-- Folder Section -->
  <h5 class="mb-3 d-flex align-items-center">
    <i class="bi bi-folder-fill text-warning me-2"></i> Semua Folder
  </h5>

  <div class="row g-3">
      @include('folders._folder_cards', ['folders' => $folders, 'allFolders' => $allFolders])
  </div>
</div>
@endsection