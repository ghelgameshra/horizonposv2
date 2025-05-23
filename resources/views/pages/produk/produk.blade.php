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
                        <small class="d-block mb-1 text-muted">Master produk</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <form action="" id="addForm" enctype="multipart/form-data"
                                class="input-group input-group-sm">
                                <input type="file" class="form-control flex-fill" id="file_produk" name="file_produk" />
                                <button class="btn btn-primary" type="submit">Upload</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" id="tambahProdukButton">
                                    + Produk
                                </button>
                                <button class="btn btn-sm flex-fill btn-info" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEndKategori" aria-controls="offcanvasEndKategori" id="tambahKategoriButton">
                                    + Kategori
                                </button>
                                <button class="btn btn-sm flex-fill btn-success" type="button" id="openSatuanJual">
                                    + Satuan Jual
                                </button>
                                <button class="btn btn-sm flex-fill btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Download
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ url('template/template_upload_produk.xlsx') }}">Template produk</a></li>
                                    <li><a class="dropdown-item" href="#">Panduan setting</a></li>
                                </ul>
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
                                        <th>plu</th>
                                        <th>bisa jual</th>
                                        <th>aktif</th>
                                        <th>jual minus</th>
                                        <th>qty</th>
                                        <th>nama produk</th>
                                        <th>kategori</th>
                                        <th>harga jual</th>
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
@include('pages.produk.modal-satuan-jual')
@include('pages.produk.tambah-data')
@include('pages.produk.tambah-kategori')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('produk.get') }}',
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
                    <button class="btn btn-xs btn-outline-warning" onclick="getProduk(${data.plu})">
                        <i class="ti ti-edit d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteProduk(${data.plu})">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.plu}},
            {data: (data) =>{
                return data.bisa_jual ? `<span class="badge text-bg-success">Y</span>` : `<span class="badge text-bg-danger">N</span>`;
            }},
            {data: (data) =>{
                return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.plu_aktif ? 'checked' : '' } onclick="changeStatusActive('${data.plu}')" />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
            }},
            {data: (data) =>{
                return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.jual_minus ? 'checked' : '' } onclick="changeStatusJual('${data.plu}')" ${data.plu_aktif ? '' : 'disabled'} />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
            }},
            {data: (data) =>{return data.stok}},
            {data: (data) =>{return data.nama_produk}},
            {data: (data) =>{return data.nama_kategori}},
            {data: (data) =>{return formatRupiah(data.harga_jual)}},
        ],
    });

    $('#addForm').on('submit', function(e){
        showLoading();
        e.preventDefault();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('produk.upsert') }}',
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

function deleteProduk(plu){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('produk.delete') }}?plu=' + plu,
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
    $('.offcanvas-header h5').text('Tambah Data Produk');
    showSelect2('jenis_ukuran', 'addFormOffCanvas', `{{ route('select2JenisUkuran') }}`);
    showSelect2('id_kategori', 'addFormOffCanvas', `{{ route('select2JenisKategori') }}`);
    showSelect2('satuan', 'addFormOffCanvas', `{{ route('select2JenisSatuan') }}`);
})

$('#addFormOffCanvas').on('submit', function(e){
    e.preventDefault();

    if($('#pluUbahProduk').val()){ // run saveEditProduk() if this value not null
        saveEditProduk();
        return;
    }

    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('produk.insert') }}',
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
        });
        notification('error', err.responseJSON.message);
    });
});

$('#addKategori').on('submit', function(e){
    e.preventDefault();
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('kategori.insert') }}',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.pesan);
        $('#addKategori')[0].reset();
        $('.offcanvas-header button').click();
    })
    .fail((err) =>{
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
            $(`#${key}`).siblings().addClass('border-danger');
        });
        notification('error', err.responseJSON.message);
    });
});

$('#harga_beli').on('keyup', function(){
    $('#preview_harga_beli').val(formatRupiah( $('#harga_beli').val() ));
})

$('#harga_jual').on('keyup', function(){
    $('#preview_harga_jual').val(formatRupiah( $('#harga_jual').val() ));
})

function changeStatusActive(plu){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ url('produk-activate-status/${plu}') }}`,
        type: 'PUT',
        data: plu,
        contentType: false,
        processData: false,
    })
    .done((res) =>{

        notification('success', res.pesan, null, 30000);
        setTimeout(() => {
            reloadDataTable($('.datatables-basic'));
        }, 1000);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

function changeStatusJual(plu){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ url('produk-activate-status-jual-minus/${plu}') }}`,
        type: 'PUT',
        data: plu,
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.pesan, null, 2000);

        setTimeout(() => {
            reloadDataTable($('.datatables-basic'));
        }, 1000);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

function getProduk(plu){
    showLoading();
    $.get(`{{ route('produk.data') }}/${plu}`)
    .done((response) => {
        showProduk(response.data.produk);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function showProduk(produk){
    $('#tambahProdukButton').click();
    $('#offcanvasEndLabel').text('Edit data produk');

    $('#addFormOffCanvas').append(`<input type="text" id="pluUbahProduk" hidden value="${produk.plu}">`);
    $('#nama_produk').val(produk.nama_produk);

    $('#id_kategori').children().remove();
    $('#id_kategori').append(`<option value="${produk.kategori.id}" selected>${produk.kategori.nama_kategori}</option>`)
    showSelect2('id_kategori', 'addFormOffCanvas', `{{ route('select2JenisKategori') }}`);

    $('#harga_beli').val(produk.harga_beli);
    $('#preview_harga_beli').val(formatRupiah(produk.harga_beli));

    $('#harga_jual').val(produk.harga_jual);
    $('#preview_harga_jual').val(formatRupiah(produk.harga_jual));

    $('#merk').val(produk.merk);

    $('#jenis_ukuran').children().remove();
    $('#jenis_ukuran').append(`<option value="${produk.jenis_ukuran}" selected>${produk.jenis_ukuran}</option>`)
    showSelect2('jenis_ukuran', 'addFormOffCanvas', `{{ route('select2JenisUkuran') }}`);


    $('#satuan').children().remove();
    $('#satuan').append(`<option value="${produk.satuan}" selected>${produk.satuan}</option>`)
    showSelect2('satuan', 'addFormOffCanvas', `{{ route('select2JenisSatuan') }}`);

    $('#kode_supplier').children().remove();
    $('#kode_supplier').append(`<option value="${produk.kode_supplier ?? ''}" selected>${produk.kode_supplier ?? ''}</option>`);

    $('#simpanProduk').text(`+ Simpan`);
}

function saveEditProduk(){
    showLoading();
    var plu = $('#pluUbahProduk').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('produk.update') }}/${plu}`,
        type: 'PUT',
        data: $('#addFormOffCanvas').serialize(),
    })
    .done((response) => {
        $('#pluUbahProduk').remove();
        $('#addFormOffCanvas')[0].reset();
        $('.offcanvas-header button').click();
        reloadDataTable($('.datatables-basic'));
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}
</script>
@endpush
