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
                        <small class="d-block mb-1 text-muted">Master Data Promosi</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" id="tambahDataButton">
                                    + Promosi
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
                                        <th>kode promo</th>
                                        <th>promo member</th>
                                        <th>periode akhir</th>
                                        <th>total penggunaan</th>
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
@include('pages.promosi.tambah-data')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    const pesan = 'Penambahan promo selain kode promo MBRTKP24 masih dalam tahap experimental. silahkan konsultasikan dengan tim IT';

    setTimeout(() => {
        toastr.info(pesan, {timeOut: 30000});
    }, 1000);

    setInterval(() => {
        toastr.info(pesan, {timeOut: 30000});
    }, 45000);

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('promo.get') }}',
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
                    <button class="btn btn-xs btn-outline-warning" onclick="showDataDetail(${data.kode_promo})">
                        <i class="ti ti-edit d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteData('${data.kode_promo}')">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.kode_promo}},
            {data: (data) =>{return data.promo_member ? 'Y' : 'N'}},
            {data: (data) =>{return data.periode_akhir}},
            {data: (data) =>{return data.total_penggunaan}},
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

function deleteData(kode_promo){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('promo.delete') }}?kode_promo=' + kode_promo,
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

$('#tambahDataButton').on('click', function(){
    $('.offcanvas-header h5').text('Tambah Data Promosi');
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
