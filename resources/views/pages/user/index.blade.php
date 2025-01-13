@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <small class="d-block mb-1 text-muted">Daftar User</small>
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap" id="table_user">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
                                        <th>nama user</th>
                                        <th>email</th>
                                        <th>nomor meja</th>
                                        <th>role</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <small class="d-block mb-1 text-muted">Daftar User & Role</small>
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap" id="table_roles">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
                                        <th>nama role</th>
                                        <th>jumlah user</th>
                                        <th>jumlah permission</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('pages.user.modal-form')
@include('pages.user.modal-form-role')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    getData();
})

function getData(){
    showLoading();
    $.get(`{{ route('get.user') }}`)
    .done((response) => {
        hideLoading();
        setTable(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function setTable(data){
    const dataUser = data.user;
    const dataRoles = data.roles;
    const roles = data.roles.map(item => ({
        text: item.name,
        id: item.name
    }));
    const permissions = data.permissions.map(item => ({
        text: item.name,
        id: item.name
    }));

    localStorage.setItem("userPermissions", JSON.stringify(permissions));
    localStorage.setItem("userRoles", JSON.stringify(roles));


    var table = $('#table_user').DataTable({
        processing: true,
        destroy: true,
        data: dataUser,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) =>{
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-warning" onclick="editUserRole('${data.id}','${data.name}', '${data.roles[0] ? data.roles[0].name : 'Tidak Ada Role'}')">
                        <i class="ti ti-edit d-block"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger" onclick="deleteUser('${data.id}')">
                            <i class="ti ti-trash d-block"></i>
                        </button>
                </div>
                `
            }},
            {data: (data) =>{return data.name}},
            {data: (data) =>{return data.email}},
            {data: (data) =>{
                return `
                    <input class="form-control w-50" type="text" id="nomorMejaUser${data.id}" name="nomorMejaUser${data.id}" value="${data.nomor_meja || ''}" data-bs-old="${data.nomor_meja || ''}" placeholder="0" autocomplete="off" autofocus />
                `;
            }},
            {data: (data) =>{return data.roles[0] ? (data.roles[0].name).toUpperCase() : '-'}},
        ],
    });

    var tableRole = $('#table_roles').DataTable({
        processing: true,
        destroy: true,
        data: dataRoles,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) =>{
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-primary" onclick="showUserInRole('${data.id}')">
                        <i class="ti ti-eye d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-warning" onclick="editRolePermission('${data.id}')">
                        <i class="ti ti-edit d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return (data.name).toUpperCase()}},
            {data: (data) =>{
                return `
                <span class="badge text-bg-primary d-block">${data.usersInRole}</span>
                `;
            }},
            {data: (data) =>{
                return `
                <span class="badge text-bg-primary d-block">${data.permissionsInRole}</span>
                `;
            }},
        ],
    });
}

$(document).on('focus', '[id^="nomorMejaUser"]', function () {
    // Simpan nilai awal sebelum perubahan
    $(this).data('oldValue', $(this).val());
});

$(document).on('change', '[id^="nomorMejaUser"]', function () {
    const userId = (this.id).replace('nomorMejaUser', '');
    const value = $(this).val();
    const oldValue = $(this).data('bs-old');

    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: `{{ route('changeTable.user') }}/${userId}`,
        type: 'PUT',
        data: {
            tableNumber: value
        }
    })
    .done((response) => {
        notification('success', response.message);
    })
    .fail((response) => {
        $(this).val(oldValue);
        notification('error', response.responseJSON.message);
    })
});

function editRolePermission(id){
    $.get(`{{ route('get.rolePermission') }}/${id}`)
    .done((response) => {
        setRolePermission(response.data);
    })
    .fail((response) => {
        $(this).val(oldValue);
        notification('error', response.responseJSON.message);
    })
}

function setRolePermission(data){
    const permissions = data.role.permissions;

    $('#modalUserLabel').text(`Permission Role ${data.role.name}`);
    $('#roleId').val(data.role.id);
    $('#activePermission').val(permissions);

    var tablePermission = $('#table_permission').DataTable({
        processing: true,
        destroy: true,
        data: permissions,
        pageLength: 4,
        columns: [
            {
                data: null, // Tidak ada data yang terkait langsung
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {
                data: 'name', // Data yang diambil dari properti 'name'
                render: function(data, type, row) {
                    // Pastikan data ada sebelum manipulasi
                    return data ? data.charAt(0).toUpperCase() + data.slice(1) : '';
                },
                title: 'Name'
            }
        ]
    });

    const dataPermissions = JSON.parse(localStorage.getItem("userPermissions"));
    $('#permission').select2({
        dropdownParent: `#modalUser`,
        data: dataPermissions
    });

    $('#modalUser').modal('show');
}

$('#modalUser').on('hidden.bs.modal', function() {
    $('#permission').val('');
});

$('#modalUser').on('submit', function(e) {
    e.preventDefault();
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: `{{ route('updatePermissions') }}/${$('#roleId').val()}`,
        type: 'PUT',
        data: {
            permissions: $('#permission').val()
        }
    })
    .done((response) => {
        $('#modalUser').modal('hide');
        notification('success', response.message);
        getData();
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
});

$('#modalUserRole').on('hidden.bs.modal', function() {
    $('#userRole').val('');
});
function editUserRole(id, name, role){
    $('#userId').val(id);
    $('#modalUserRoleLabel').text(`Role ${name.toLowerCase()} - ${role || 'Tidak Ada Role'}`)
    const dataRoles = JSON.parse(localStorage.getItem("userRoles"));
    $('#userRole').select2({
        dropdownParent: `#modalUserRole`,
        data: dataRoles
    });

    $('#modalUserRole').modal('show');
}

$('#modalUserRole').on('submit', function(e) {
    e.preventDefault();
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: `{{ route('updateUserRole') }}/${$('#userId').val()}`,
        type: 'PUT',
        data: {
            role: $('#userRole').val()
        }
    })
    .done((response) => {
        $('#modalUserRole').modal('hide');
        notification('success', response.message);
        getData();
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
});


</script>
@endpush
