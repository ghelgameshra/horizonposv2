@extends('pages.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Ambil Pesanan</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap" id="tabelPesanan">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th class="text-center">
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
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
@include('pages.pesanan.modal-show')
@endsection

@push('js')
<script src="{{ asset('js/clipboard.js/clipboard.min.js') }}"></script>
<script>
$(function(){
    showLoading();
    $.get(`{{ route('transaksi.list') }}`)
    .done((response) => {
        loadListTransaksi(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })

    /* Ambil data rekening */
    $.get(`{{ route('get.defaultRekening') }}`)
    .done((response) => {
        const data = JSON.stringify(response.data.rekening);
        localStorage.setItem("defaultRekening", data);
    })
    .fail((response) => {
        notification('info', response.responseJSON.message, null, 5000);

        const data = {
            "nama_bank": "AAAA",
            "nomor_rekening": "0000-000-000",
            "nama_pemilik": "LOREM"
        }
        localStorage.setItem("defaultRekening", JSON.stringify(data));
    })
})

function loadListTransaksi(data){
    var table = $('.datatables-basic').DataTable({
        destroy: true,
        data: data,
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
                    <button class="btn btn-xs btn-outline-primary" onclick="showDetail('${data.invno}')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Lihat detail transaksi">
                        <i class="ti ti-eye d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-primary" onclick="copyDetail('${data.invno}')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Copy detail transaksi ke clipboard">
                        <i class="ti ti-copy d-block"></i>
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

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}

function copyDetail(invno = '#') {
    showLoading();
    $.get(`{{ route('pengeluaran.show') }}/${invno}`)
    .done((response) => {
        actionCopy(response.data);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

$(document).on('click', '[id^="copyButton_"]', function() {
    const buttonId = $(this).attr('id'); // Mendapatkan ID tombol yang diklik
    const clipboardText = $(this).attr('data-clipboard-text'); // Mendapatkan data dari atribut

    const clipboard = new ClipboardJS(`#${buttonId}`, {
        container: document.getElementById('tabelPesanan')
    });

    clipboard.on('success', function(e) {
        notification('success', 'Berhasil copy data ke clipboard');
        e.clearSelection();
        $(`#${buttonId}`).remove();
    });

    clipboard.on('error', function(e) {
        notification('error', 'Gagal copy data ke clipboard')
        e.clearSelection();
        $(`#${buttonId}`).remove();
    });
});

function actionCopy(data){

const defaultRekening = JSON.parse(localStorage.getItem("defaultRekening"));

const transaksiLog = data.transaksi_log.map((item, index) => {
return `
${index + 1}. ${item.namafile ? (item.namafile + item.nama_produk) : item.nama_produk} ${item.ukuran || '-'}
${formatRupiah(item.total)} X ${item.jumlah} = ${formatRupiah(item.total)}`;
}).join('\n');

/* all data */
const dataToCopy = `
Invoice No: ${data.invno || '-'}
Nama Customer: ${data.nama_customer || '-'}
Nomor Telepon: ${data.nomor_telepone || '-'}
Tanggal Transaksi: ${data.tanggal_transaksi || '-'}
Kasir: ${data.kasir?.name || '-'}

${transaksiLog}

Diskon: ${formatRupiah(data.diskon)}
Subtotal: ${formatRupiah(data.subtotal)}
================
*Total: ${formatRupiah(data.total)}*
================

Transfer ke rekening ${defaultRekening.nama_bank} Atas Nama *${defaultRekening.nama_pemilik}*
Nomor Rekening: ${defaultRekening.nomor_rekening}
*Total Pembayaran: ${formatRupiah(data.total)}*

*Jika Anda telah melakukan pembayaran, mohon segera mengirimkan bukti transfer agar pesanan dapat segera diproses. Terima kasih.*
`;

    $('#tabelPesanan').append(`<button id="copyButton_${data.invno}" class="d-none" data-clipboard-text="${dataToCopy}">copy</button>`);
    $(`#copyButton_${data.invno}`).click();
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

function ambilPesanan(){
    var invno = $('#invno').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('pesanan.ambil') }}/${invno}`,
        type: 'PUT',
    })
    .done((res) =>{
        notification('success', res.pesan, null, 3000);
        $('.datatables-basic').DataTable().ajax.reload();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message, null, 3500);
    });

    $('#modalPelunasan').modal('hide');
}
</script>
@endpush
