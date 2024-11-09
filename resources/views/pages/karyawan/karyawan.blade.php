@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Master Data Karyawan</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <form action="" id="addForm" enctype="multipart/form-data"
                                class="input-group input-group-sm" method="POST">
                                <input type="file" class="form-control flex-fill" id="file_karyawan" name="file_karyawan" />
                                <button class="btn btn-primary" type="submit">Upload</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" id="tambahData">
                                    + Karyawan
                                </button>
                                <button class="btn btn-sm flex-fill btn-success">Export Excel</button>
                                <button class="btn btn-sm flex-fill btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Download
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('template/template_upload_karyawan.xlsx') }}">Template data karyawan</a></li>
                                    <li><a class="dropdown-item" href="#">Panduan setting</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndKategori" aria-controls="offcanvasEndKategori" id="tambahKategoriButton">
                                    + Jabatan
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th></th>
                                        <th>nama</th>
                                        <th>status</th>
                                        <th>nik</th>
                                        <th>telepone</th>
                                        <th>jobdesk</th>
                                        <th>jabatan</th>
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
@include('pages.karyawan.tambah-data')
@include('pages.karyawan.tambah-kategori')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('karyawan.get') }}',
        },
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
                    <button class="btn btn-xs btn-outline-warning" onclick="editData('${data.nik}')">
                        <i class="ti ti-edit d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteData('${data.nik}')">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.nama_lengkap}},
            {data: (data) =>{return data.status_karyawan ? 'Aktif' : 'Tidak aktif'}},
            {data: (data) =>{return data.nik}},
            {data: (data) =>{return data.telepone}},
            {data: (data) =>{return data.jobdesk}},
            {data: (data) =>{return data.jabatan}},
        ],
    });

    $('#addForm').on('submit', function(e){
        e.preventDefault();
        showLoading();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            url: '{{ route('karyawan.upsert') }}',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
        })
        .done((res) =>{
            notification('success', res.pesan);
            reloadDataTable($('.datatables-basic'));
        })
        .fail((err) =>{
            notification('error', err.responseJSON.message);
        });
    });
})

function deleteData(nik){
    Swal.fire({
        text: "Hapus Data Karyawan ?",
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        customClass: {
            confirmButton: 'btn btn-danger waves-effect waves-light',
            cancelButton: 'btn btn-label-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            showLoading();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('karyawan.delete') }}?nik=' + nik,
                type: 'delete',
                contentType: false,
                processData: false,
            })
            .done((res) =>{
                notification('success', res.pesan);
                reloadDataTable($('.datatables-basic'));
            })
            .fail((err) =>{
                notification('error', err.responseJSON.message);
            });
        }
    })
}

$('#tambahData').on('click', function(){
    $('.offcanvas-header h5').text('Tambah Data Karyawan');
    showSelect2('jabatan', 'addFormOffCanvas', `{{ route('select2Jabatan') }}`);
    showSelect2('agama', 'addFormOffCanvas', `{{ route('select2Agama') }}`);
    showSelect2('pendidikan_terakhir', 'addFormOffCanvas', `{{ route('select2Pendidikan') }}`);
})

$('#addFormOffCanvas').on('submit', function(e){
    showLoading();
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('karyawan.insert') }}',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.pesan);
        reloadDataTable($('.datatables-basic'));
        $('#addFormOffCanvas')[0].reset();
        $('.offcanvas-header button').click();
    })
    .fail((err) =>{
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
            $(`#${key}`).siblings().addClass('border-danger');
            // console.log(key + ': ' + messages.join(', '));
        });
        notification('error', err.responseJSON.message);
    });
});

function editData(nik){
    notification('info', 'sedang ambil data');

    $.get(`{{ url('karyawan/${nik}') }}`)
    .done((res) => {
        console.log(res.data);
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    })
}
</script>
@endpush
