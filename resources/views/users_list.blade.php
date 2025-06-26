<!DOCTYPE html>
<html>

<head>
    <title>Users List</title>
    <!-- DataTables and Bootstrap CDN Links -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Users List</h3>
            <a href="{{ route('users.index') }}" class="btn btn-success">Add New User</a>
        </div>
        <div class="table-responsive mt-3">
            <table id="userTable" class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Mobile</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->mobile ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->dob)->format('d-m-Y') }}</td>
                            <td>{{ $user->gender == 1 ? 'Male' : ($user->gender == 2 ? 'Female' : 'Other') }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                
                                <button type="button" class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}">
                                Delete</button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts for jQuery, DataTables, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#userTable').DataTable({
                pageLength: 10,
                lengthChange: true,
                ordering: true,
                searching: true,
                columnDefs: [{
                        orderable: false,
                        targets: 6
                    } // Disables ordering for the Action column
                ]
            });
        });
    </script>

   <script>
    $(document).ready(function () {
        // Handle delete button click
        $('.delete-user').on('click', function () {
            var userId = $(this).data('id');
            var row = $(this).closest('tr');

            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    url: '/users/' + userId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            // Remove the row from DataTable
                            $('#userTable').DataTable().row(row).remove().draw();
                        } else {
                            alert("Failed to delete user.");
                        }
                    },
                    error: function (xhr) {
                        alert("An error occurred while deleting the user.");
                    }
                });
            }
        });
    });
</script>


</body>

</html>
