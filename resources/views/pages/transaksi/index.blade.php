@extends('pages.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Reprint Transaksi/ Cancel Transaksi</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th></th>
                                        <th>invno</th>
                                        <th>nama customer</th>
                                        <th>tanggal trx</th>
                                        <th>lunas</th>
                                        <th>status order</th>
                                        <th>kasir</th>
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
@include('pages.transaksi.modal-show')
@include('pages.transaksi.modal-cancel')
@endsection

@push('js')
<script>
$(function(){

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('transaksi.list') }}',
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
                    <button class="btn btn-xs btn-outline-primary" onclick="showDetail('${data.invno}')">
                        <i class="ti ti-eye d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-info" onclick="reprintStruk('${data.invno}')">
                        <i class="ti ti-receipt d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="cancelSales('${data.invno}')">
                        <i class="ti ti-circle-minus d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.invno}},
            {data: (data) =>{return data.nama_customer}},
            {data: (data) =>{return data.tanggal_transaksi}},
            {data: (data) =>{
                return data.terima > 0 ? `<span class="badge text-bg-success">LUNAS</span>` : `<span class="badge text-bg-danger">BELUM</span>`;
            }},
            {data: (data) =>{
                return data.status_order === "DALAM ANTRIAN"
                ? `<span class="badge text-bg-warning w-100">${data.status_order}</span>`
                : data.status_order === "CANCEL SALES"
                ? `<span class="badge text-bg-danger w-100">${data.status_order}</span>`
                : `<span class="badge text-bg-success w-100">${data.status_order}</span>`;
            }},
            {data: (data) =>{return data.kasir.name}},
        ],
    });
})

function reprintStruk(invno){
    Swal.fire({
        text: "Reprint invoice ?",
        showCancelButton: true,
        confirmButtonText: 'Reprint',
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light',
            cancelButton: 'btn btn-label-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            $.get(`{{ route('reprint-transaksi.print')}}/${invno}`)
            .done((response) => {
                notification('success', response.pesan);
            })
            .fail((response) => {
                notification('error', response.responseJSON.message, 'Gagal Print');
            });
        }
    });
}

function showDetail(invno = '#'){
    $.get(`{{ route('pengeluaran.show') }}/${invno}`)
    .done((response) => {
        showDetailTransaksi(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    });
}

function showDetailTransaksi(data){
    $('#modalPelunasanTitle').text(`Detail Transaksi ${data.invno}`);
    $('#modalPelunasan').modal('show');

    $('#invno').val(data.invno);
    $('#nama_customer').val(data.nama_customer);
    $('#nomor_telepone').val(data.nomor_telepone);
    $('#tanggal_transaksi').val(data.tanggal_transaksi);
    $('#subtotal').val(formatRupiah(data.subtotal));
    $('#diskon').val(formatRupiah(data.diskon));
    $('#total').val(formatRupiah(data.total));
    $('#uang_muka').val(formatRupiah(data.uang_muka));
    $('#terima').val(formatRupiah(data.terima));
    $('#kembali').val(formatRupiah(data.kembali));

    if(data.uang_muka > 0){
        $('#bayar_pelunasan').val(formatRupiah(data.terima - data.kembali));
    } else {
        $('#bayar_pelunasan').val(formatRupiah(0));
    }

    $('#tipe_bayar').val(data.tipe_bayar);
    $('#tipe_bayar_pelunasan').val(data.tipe_bayar_pelunasan);
    $('#nama_kasir').val(data.kasir['name']);
    $('#status_order').val(data.status_order);

    $('#transaksiDetailTable').DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: data['transaksi_log'],
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

var invnoCancel;
function cancelSales(invno){
    invnoCancel = invno;
    $('#modalCancelSalesTitle').text(`Cancel Sales ${invno}`)
    $('#modalCancelSales').modal('show');
}

$('#CancelSalesForm').on('submit', function(e){
    e.preventDefault();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('transaksi.cancel') }}/${invnoCancel}`,
        type: 'POST',
        data: {
            password: $('#user_password').val()
        }
    })
    .done((response) => {
        reloadDataTable($('.datatables-basic'));
        $('#modalCancelSales').modal('hide');
        notification('success', response.pesan, null, 2000);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message, null, 3500);
    })
})

$('#modalCancelSales').on('hidden.bs.modal', function(){
    $('#CancelSalesForm')[0].reset();
})
</script>
@endpush
