@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        @include('pages.kasir.form-add')
        @include('pages.kasir.table-info')
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    // var table = $('.datatables-basic').DataTable({
    //     processing: true,
    //     paging: true,
    //     ajax: {
    //         url: '{{ route('produk.get') }}',
    //     },
    //     columns: [
    //         {
    //             data: null, // Tidak ada data yang terkait
    //             render: function(data, type, row, meta) {
    //                 return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
    //             },
    //             title: 'No'
    //         },
    //         {data: (data) =>{return data.plu}},
    //         {data: (data) =>{return data.nama_produk}},
    //         {data: (data) =>{return data.nama_kategori}},
    //         {data: (data) =>{return formatRupiah(data.harga_jual)}},
    //         {data: (data) =>{return data.stok}},
    //         {data: (data) =>{
    //             return `
    //             <div class="btn-group">
    //                 <button class="btn btn-xs btn-outline-danger" onclick="deleteProduk(${data.plu})">
    //                     <i class="ti ti-trash d-block"></i>
    //                 </button>
    //             </div>
    //             `
    //         }},
    //     ],
    //     dom: '',
    //     ordering: false
    // });

    $('#addForm').on('submit', function(e){
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
            // console.log(key + ': ' + messages.join(', '));
        });
        notification('error', err.responseJSON.message);
    });
});

$('#addKategori').on('submit', function(e){
    e.preventDefault();
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
            // console.log(key + ': ' + messages.join(', '));
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
</script>
@endpush
