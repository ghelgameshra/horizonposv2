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
                        <small class="d-block mb-1 text-muted">Data Pengeluaran</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#" id="tambahPengeluran">
                                    + Pengeluaran Baru
                                </button>
                                <button class="btn btn-sm flex-fill btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" id="tambahJenisPengeluaran">
                                    + Jenis Pengeluaran
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
                                        <th>invno</th>
                                        <th class="text-center">img ref</th>
                                        <th>jenis</th>
                                        <th>tgl pengeluaran</th>
                                        <th>nominal</th>
                                        <th>pembuat</th>
                                        <th class="text-center">
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
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
@include('pages.pengeluaran.tambah-data')
@include('pages.pengeluaran.modal-form')
@include('pages.pengeluaran.modal-img')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('pengeluaran.get') }}',
        },
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) =>{return data.invno}},
            {data: (data) =>{
                return data.image ? `
                <button class="btn btn-xs btn-primary w-100" onclick="showImage('${data.image}', '${data.invno}')">
                    <i class="ti ti-photo d-block"></i>
                </button>
                ` : `
                <button class="btn btn-xs btn-danger w-100 disabled">
                    <i class="ti ti-photo-x d-block"></i>
                </button>`;
            }},
            {data: (data) =>{return data.jenis_pengeluaran}},
            {data: (data) =>{return data.tanggal_pengeluaran}},
            {data: (data) =>{return formatRupiah(data.total)}},
            {data: (data) =>{return data.pembuat}},
            {data: (data) =>{
                return `
                <input type="file" hidden id="file_ref_${data.invno}" name="file_ref">
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-warning" onclick="uploadFileRef('${data.invno}')">
                        <i class="ti ti-file-upload d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteData('${data.invno}')">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `
            }},
        ],
    });
})

$('#addFormOffCanvas').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('jenis-pengeluaran.insert') }}',
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
        });
        notification('error', err.responseJSON.message);
    });
});

$('#tambahPengeluran').on('click', function(){
    $('#modalListPengeluaran').modal('show');
});

$('#modalListPengeluaran').on('shown.bs.modal', function(){
    showSelect2('jenis_pengeluaran', 'modalListPengeluaran', `{{ route('select2JenisPengeluaran') }}`);
});

$('#total').on('keyup', function(){
    var total = parseInt($(this).val());
    isNaN(total) ? $('#preview_total').val(formatRupiah(0)) : $('#preview_total').val(formatRupiah(total));
})

$('#listPengeluaranForm').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('pengeluaran.insert') }}',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.pesan);
        reloadDataTable($('.datatables-basic'));
        $('#modalListPengeluaran').modal('hide');
        $('#listPengeluaranForm')[0].reset();
    })
    .fail((err) =>{
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
        });
        notification('error', err.responseJSON.message);
    });
});

function uploadFileRef(invno) {
    var file_ref = `file_ref_${invno}`;
    var fileInput = $(`#${file_ref}`);

    var formData = new FormData();
    // Trigger klik pada input file
    fileInput.click();

    // Pastikan event listener hanya dipasang sekali
    if (!fileInput.data('listener-added')) {
        fileInput.on('input', function () {

            formData.append('_token', $('meta[name="csrf-token"]').attr('content')); // CSRF token
            formData.append('file_ref', fileInput[0].files[0]); // Tambahkan file
            formData.append('invno', invno); // Tambahkan invno

            $.ajax({
                url: '{{ route('pengeluaran.uploadRef') }}',
                type: 'POST',
                data: formData,
                processData: false, // Jangan memproses data
                contentType: false,
            })
            .done((res) => {
                notification('success', res.pesan)
                reloadDataTable($('.datatables-basic'));
            })
            .fail((err) => {
                notification('error', err.responseJSON.message)
            })

        });

        // Tandai bahwa event listener sudah dipasang
        fileInput.data('listener-added', true);
    }
}

function showImage(img, invno){
    $('#modalShowImageTitle').text(`IMG REF ${invno}`);
    $('#modalShowImageSrc').attr('src', `{{ url('/') }}/${img}`);
    $('#modalShowImage').modal('show');
}

function deleteData(invno){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('pengeluaran.insert') }}',
        type: 'DELETE',
        data: {
            'invno': invno
        },
    })
    .done((res) =>{
        notification('success', res.pesan);
        reloadDataTable($('.datatables-basic'));
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}
</script>
@endpush
