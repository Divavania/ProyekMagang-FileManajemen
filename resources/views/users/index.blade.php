@extends('layouts.app') 
@section('title', 'Daftar User')

@section('content')
<div class="container mt-4">
    <h3>Daftar User</h3>

    <!-- Tombol buka modal tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Tambah User
    </button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ ucfirst($user->status) }}</td>
                    <td>
                        @php
                            $authRole = auth()->user()->role;
                        @endphp

                        @if(
                            ($authRole === 'superadmin') || 
                            ($authRole === 'admin' && $user->role === 'user')
                        )
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                Hapus
                            </button>
                        @else
                            <span class="text-muted">Tidak bisa diubah</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada user</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ======================= -->
<!-- MODAL TAMBAH USER       -->
<!-- ======================= -->
@include('users.modals.add', ['authRole' => auth()->user()->role])

<!-- ======================= -->
<!-- MODAL EDIT PER USER      -->
<!-- ======================= -->
@foreach($users as $user)
    @include('users.modals.edit', ['user' => $user, 'authRole' => auth()->user()->role])
@endforeach

<!-- ======================= -->
<!-- MODAL HAPUS PER USER     -->
<!-- ======================= -->
@foreach($users as $user)
    @include('users.modals.delete', ['user' => $user, 'authRole' => auth()->user()->role])
@endforeach

@endsection