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
                        <small class="d-block mb-1 text-muted">Master Data Member</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" id="tambahProdukButton">
                                    + Member
                                </button>
                                <button class="btn btn-sm flex-fill btn-success">Export Excel</button>
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
                                        <th>kode member</th>
                                        <th>nama</th>
                                        <th>telepone</th>
                                        <th>email</th>
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
@include('pages.member.tambah-data')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('member.get') }}',
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
                    <button class="btn btn-xs btn-outline-warning" onclick="showMemberDetail(${data.kode_member})">
                        <i class="ti ti-edit d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteMember('${data.kode_member}')">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.kode_member}},
            {data: (data) =>{return data.nama_lengkap}},
            {data: (data) =>{return data.telepone}},
            {data: (data) =>{return data.email}},
        ],
    });

    $('#addForm').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('member.insert') }}',
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

function deleteMember(kode_member){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('member.delete') }}?kode_member=' + kode_member,
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

$('#tambahProdukButton').on('click', function(){
    $('.offcanvas-header h5').text('Tambah Data Member');
})

$('#addFormOffCanvas').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('member.insert') }}',
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
        console.log(err.responseJSON['errors']);
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
        });
        notification('error', err.responseJSON.message);
    });
});
</script>
@endpush
