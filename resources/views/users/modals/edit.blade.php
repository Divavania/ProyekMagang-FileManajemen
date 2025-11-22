<!-- resources/views/users/modals/edit.blade.php -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">

                    <!-- Nama -->
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ganti password">
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control" required>
                            @if($authRole === 'superadmin')
                                <option value="superadmin" @if($user->role == 'superadmin') selected @endif>Superadmin</option>
                                <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
                                <option value="user" @if($user->role == 'user') selected @endif>User</option>
                            @elseif($authRole === 'admin')
                                <option value="admin" disabled @if($user->role == 'admin') selected @endif>Admin</option>
                                <option value="user" @if($user->role == 'user') selected @endif>User</option>
                            @endif
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="aktif" @if($user->status == 'aktif') selected @endif>Aktif</option>
                            <option value="nonaktif" @if($user->status == 'nonaktif') selected @endif>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Foto -->
                    <div class="mb-3">
                        <label class="form-label">Foto (opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        @if($user->photo)
                            <div class="mt-1">
                                <small class="text-muted">Foto saat ini:</small><br>
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto {{ $user->name }}" style="height:60px; border-radius:4px;">
                            </div>
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>