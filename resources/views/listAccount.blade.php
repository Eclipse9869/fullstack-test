<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Daftar Akun</h2>
    <button class="btn btn-success mb-3" onclick="addUser()">
        <i class="fa fa-plus"></i>  Tambah User
    </button>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Umur</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->age }}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editUser({{ $item->id }})">
                        <i class="fa-solid fa-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $item->id }})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal Tambah/Edit User --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addUserForm">
        <input type="hidden" id="userId" name="id">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserLabel">Tambah User Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="age" class="form-label">Umur</label>
            <input type="number" class="form-control" id="age" name="age" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function addUser() {
        // Kosongkan form
        document.getElementById('addUserForm').reset();
        document.getElementById('userId').value = '';
        const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
        modal.show();
    }

    function editUser(id) {
        fetch(`/accounts/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('userId').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('email').value = data.email;
                document.getElementById('age').value = data.age;
                const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error fetching user:', error);
                alert('Gagal mengambil data user.');
            });
    }

    function deleteUser(id) {
        if (!confirm("Yakin ingin menghapus user ini?")) return;

        fetch(`/accounts/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ _method: 'DELETE' })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus user.');
        });
    }

    function spoofFormData(formData, method) {
        formData.append('_method', method);
        return formData;
    }

    document.getElementById('addUserForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const userId = formData.get('id');
        const url = userId
            ? `/accounts/${userId}`
            : "{{ route('accounts.store') }}";

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: userId ? spoofFormData(formData, 'PUT') : formData
        })
        .then(async response => {
            if (!response.ok) {
                const error = await response.json();
                alert('Gagal simpan user: ' + JSON.stringify(error.errors));
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan.');
        });
    });
</script>
</body>
</html>
