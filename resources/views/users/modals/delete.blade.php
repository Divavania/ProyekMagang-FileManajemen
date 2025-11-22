<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus User: {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            @php
                $authRole = auth()->user()->role;
                $canDelete = $authRole === 'superadmin' || ($authRole === 'admin' && $user->role === 'user');
            @endphp

            @if($canDelete)
                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah kamu yakin ingin menghapus user <strong>{{ $user->name }}</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            @else
                <div class="modal-body">
                    <p class="text-danger">
                        Kamu tidak memiliki izin untuk menghapus user ini.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            @endif

        </div>
    </div>
</div>