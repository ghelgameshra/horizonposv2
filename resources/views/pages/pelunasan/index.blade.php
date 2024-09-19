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
                    <small class="d-block mb-1 text-muted">Pelunasan Transaksi</small>
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th>
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
                                        <th>nama customer</th>
                                        <th>invo</th>
                                        <th>tanggal TRX</th>
                                        <th>total</th>
                                        <th>uang muka</th>
                                        <th>tipe bayar</th>
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
@include('pages.pelunasan.modal-form')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('pelunasan.get') }}',
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
                <button class="btn btn-xs btn-outline-warning" onclick="showModalPelunasan('${data.invno}')">
                    <i class="ti ti-edit d-block"></i>
                </button>
                `
            }},
            {data: (data) =>{return data.nama_customer}},
            {data: (data) =>{return data.invno}},
            {data: (data) =>{return data.tanggal_transaksi}},
            {data: (data) =>{return formatRupiah(data.total)}},
            {data: (data) =>{return formatRupiah(data.uang_muka)}},
            {data: (data) =>{return data.tipe_bayar}},
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

function showModalPelunasan(invno){
    $.get('{{ route('pelunasan.detail', ['invno']) }}', {
        'invno': invno
    })
    .done((response) =>{
        $('#modalPelunasan').modal('show');
        setPelunasan(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function setPelunasan(data){
    $('#nama_customer').val(data.nama_customer);
    $('#nama_kasir').val(data.kasir.name);
    $('#invno').val(data.invno);
    $('#nomor_telepone').val(data.nomor_telepone);
    $('#tanggal_transaksi').val(data.tanggal_transaksi);
    $('#tipe_bayar').val(data.tipe_bayar);
    $('#status_order').val(data.status_order);
    $('#subtotal').val(formatRupiah(data.subtotal));
    $('#diskon').val(formatRupiah(data.diskon));
    $('#uang_muka').val(formatRupiah(data.uang_muka));
    $('#total').val(formatRupiah(data.total));

    const terima = parseInt(data.total) - parseInt(data.uang_muka);
    $('#terima').val(terima);
    $('#kurang_bayar').val(formatRupiah(terima));
    $('#kurang_bayar').attr('data-kurang-bayar', terima);

    $('#preview_terima').val(formatRupiah(terima));
    $('#preview_kembali').val(formatRupiah(0));

    $('#transaksiDetailTable').DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: data.transaksi_log,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            { data: (data) =>{
                return data.namafile ? data.namafile : data.nama_produk;
            }},
            { data: 'plu' },
            { data: 'nama_produk' },
            { data: (data) =>{
                return data.ukuran ? data.ukuran : data.satuan;
            }},
            { data: (data) =>{
                return data.harga_jual > 0 ? formatRupiah(data.harga_jual) : formatRupiah(data.harga_ukuran);
            }},
            { data: 'jumlah' },
            { data: (data) => {
                return formatRupiah(data.total)
            }},
        ]
    })
}

$('#modalPelunasan').on('shown.bs.modal', function(){
    $('#terima').focus();
});

$('#terima').on('keyup', function(){
    let value = $(this).val();
    let kurangBayar = parseInt($('#kurang_bayar').attr('data-kurang-bayar'));
    if(isNaN(value)){
        $('#preview_terima').val(formatRupiah(0));
        $('#preview_kembali').val(formatRupiah(0));
    }

    if (parseInt(value) < kurangBayar) {
        $('#preview_kembali').removeClass('bg-primary').addClass('bg-danger text-white');
        $('#kurang_bayar').removeClass('bg-primary').addClass('bg-danger text-white');
    } else {
        $('#preview_kembali').removeClass('bg-danger').addClass('bg-primary text-white');
        $('#kurang_bayar').removeClass('bg-danger').addClass('bg-primary text-white');
    }

    $('#preview_terima').val(formatRupiah(value));
    $('#preview_kembali').val(formatRupiah(value - kurangBayar));
});

$('#pelunasanForm').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('pelunasan.update') }}',
        type: 'PUT',
        data: $(this).serialize(),
    })
    .done((res) =>{
        notification('success', res.pesan);
        $('#modalPelunasan').modal('hide');
        $('.datatables-basic').DataTable().ajax.reload();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
});
</script>
@endpush
