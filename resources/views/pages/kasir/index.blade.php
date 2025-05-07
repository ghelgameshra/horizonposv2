@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1" style="margin-top: -40px">
    <div class="row">
        @include('pages.kasir.form-add')
    </div>

    @include('pages.kasir.pilih-produk')
</div>
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(document).keydown(function(event) {
    // Cek jika tombol Ctrl dan Enter ditekan bersamaan
    if (event.ctrlKey && event.key === 'Enter') {
        $('#addDataModal').modal('show');
    }
});

$(function(){;
    getTransaksi();

    const userName = '{{ auth()->user()->name }}'; // Ambil nama pengguna dari server-side
    setInterval(() => {
        // Ambil waktu sekarang dengan JavaScript
        let now = new Date();
        let formattedTime = now.getFullYear() + '-'
                            + String(now.getMonth() + 1).padStart(2, '0') + '-'
                            + String(now.getDate()).padStart(2, '0') + ' '
                            + String(now.getHours()).padStart(2, '0') + ':'
                            + String(now.getMinutes()).padStart(2, '0') + ':'
                            + String(now.getSeconds()).padStart(2, '0');

        // Update nilai di input field dengan waktu dan nama pengguna
        $('#nameTag').val(formattedTime + ' | ' + userName);
    }, 1000); // Perbarui setiap 1 detik

    setTimeout( () => {
        getProdukJual();
    }, 500);
});

function getTransaksi(){
    showLoading();
    const user = {{ auth()->user()->id }};
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('transaksiBaru') }}/${user}`,
        type: 'POST',
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        const total = res.data['subtotal'];

        localStorage.setItem("configSatuan", JSON.stringify(res.satuan));

        $('#total_view').val(`${formatRupiah(total)}`);
        $('#id_transaksi').val(res.data['id']);

        getTransaksiDetail(res.data['id']);
    })
    .fail((err) =>{
        hideLoading();
        notification('error', err.responseJSON.message);
    })
}

function getTransaksiDetail(id){
    $.get(`{{ route('transaksiBaruDetail') }}?id_transaksi=${id}`)
    .done((response) => {
        hideLoading()
        loadDataSales(response.data);
    })
    .fail((response) => {
        hideLoading()
        notification('error', response.responseJSON)
    })
}

function generateInputGroup(data) {
    const configSatuan = JSON.parse(localStorage.getItem("configSatuan") || "[]");
    const config = configSatuan.find(c => c.nama_satuan === data.satuan);

    // Default fallback jika config tidak ditemukan
    const inputNamaFileEnabled = config?.input_namafile === 1;
    const inputUkuranEnabled = config?.input_ukuran === 1;

    const inputNamaFile = `
        <input type="text" class="form-control" placeholder="nama file"
            name="namaFilePlu_${data.plu}_${data.id}"
            id="namaFilePlu_${data.plu}_${data.id}"
            value="${data.namafile || ''}" autocomplete="off"
            ${inputNamaFileEnabled ? 'required' : 'disabled'}
            onchange="addFileSize('${data.id}', '${data.plu}')">
    `;

    const inputUkuran = inputUkuranEnabled ? `
        <input type="text" class="form-control" placeholder="100x100"
            name="ukuranPlu_${data.plu}_${data.id}"
            id="ukuranPlu_${data.plu}_${data.id}"
            value="${data.ukuran || ''}" autocomplete="off"
            required
            onchange="addFileSize('${data.id}', '${data.plu}')">
    ` : '';

    return `
        <div class="input-group input-group-sm text-center">
            ${inputNamaFile}
            ${inputUkuran}
        </div>
    `;
}

/* load data detail */
function loadDataSales(data){
    tableSales = $('.datatables-basic').DataTable({
        dom: '',
        ordering: false,
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
                return data.nama_produk.split(' ').slice(0, 2).join(' ');
            }},
            {data: (data) =>{return data.plu}},
            {data: (data) =>{
                return generateInputGroup(data);
            }},
            {data: (data) =>{return formatRupiah(data.harga_jual)}},
            {data: (data) =>{
                return `
                    <div class="input-group input-group-sm text-center">
                        <input type="text" inputmode="numeric" class="form-control" name="" id="tambahQtyPlu_${data.plu}_${data.id}" autocomplete="off" value="${data.jumlah}" onchange="tambahQty('${data.id}', '${data.plu}')" data-old-value="${data.jumlah}">
                    </div>
                `
            }},
            {data: (data) =>{return formatRupiah(data.total)}},
            {data: (data) =>{
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-danger" onclick="deleteDataSales('${data.id}')">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                `
            }},
        ],
    });
}

function addFileSize(id, plu) {
    const fileName = $(`#namaFilePlu_${plu}_${id}`).val();
    const size = $(`#ukuranPlu_${plu}_${id}`).val();

    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('addfilesize') }}/${id}`,
        type: 'PUT',
        data: {
            fileName: fileName,
            size: size
        }
    })
    .done((res) =>{
        notification('success', res.message, null, 1000);
        getTransaksi();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
        hideLoading();
    });
}


$('#terima').on('keyup', function(){
    $('#terima_view').val(formatRupiah($(this).val()))
});

/* tambah data sales */
$('#addDataModal').on('shown.bs.modal', function(){
    $('#pilihProdukTable_filter label').contents().filter(function() {
        return this.nodeType === 3; // nodeType 3 adalah text node
    }).remove();

    // Fokuskan input setelah modal benar-benar ditampilkan
    $('#pilihProdukTable_filter label input').focus();

    // Membuat input menjadi full width
    $('#pilihProdukTable_filter label input').attr('placeholder', 'cari produk');
    if (window.matchMedia("(max-width: 768px)").matches) {
        // Jika layar mobile, lebar input menjadi 20em
        $('#pilihProdukTable_filter label input').css({'width':'20em', 'margin': '0'});
    } else {
        // Jika layar lebih besar dari mobile, lebar input menjadi 40em
        $('#pilihProdukTable_filter label input').css({'width':'55em', 'margin': '0'});
    }
});

$('#addDataModal').on('hide.bs.modal', function(){;
    $('#pilihProdukTable_filter label input').val('');
    $('input[type="checkbox"]').prop('checked', false);
});

function getProdukJual(){
    let tablePilihProduk = $('#pilihProdukTable').DataTable({
        processing: true,
        autoWidth: false,
        ajax: {
            url: '{{ route('getProdukJual') }}',
        },
        columns: [
            {data: (data) =>{return data.plu}},
            {data: (data) =>{return data.nama_produk}},
            {data: (data) =>{return data.kategori['nama_kategori']}},
            {data: (data) =>{return formatRupiah(data.harga_jual)}},
            {data: (data) =>{
                return `
                <button class="btn btn-xs btn-success d-block" onclick="tambahDataSales('${data.plu}')">
                    <i class="ti ti-circle-plus"></i>
                </button>
                `;
            }},
        ],
        dom: 'ftp',
        ordering: false,
        bSort: false,
        searchable: true,
    });
}

function tambahDataSales(plu){
    showLoading()
    const idTrx = $('#id_transaksi').val();

    $('#addDataModal').modal('hide');
    $.post(`{{ route('transaksiLog') }}?plu=${plu}&idTransaksi=${idTrx}`, {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    })
    .done((res) =>{
        getTransaksi();
        notification('success', res.pesan, null, 1000);
    })
    .fail((err) =>{
        hideLoading()
        notification('error', err.responseJSON.message);
    });
}

function deleteDataSales(id){
    showLoading()
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('transaksiLogDelete') }}/${id}`,
        type: 'DELETE',
    })
    .done((res) =>{
        notification('success', res.pesan, null, 1000);
        getTransaksi();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
        hideLoading()
    });
}

function tambahQty(id, plu) {
    showLoading()
    const idInput = `#tambahQtyPlu_${plu}_${id}`;
    let value = parseInt($(idInput).val(), 10); // Ambil nilai input
    const oldValue = parseInt($(idInput).attr('data-old-value'), 10) || 0; // Ambil nilai sebelumnya, default 0 jika tidak ada

    if (isNaN(value)) {
        // Jika inputan bukan angka, tampilkan notifikasi dan kembalikan nilai input ke yang lama
        notification('error', 'Inputan tidak sesuai. Silahkan input angka');
        $(idInput).val(oldValue); // Kembalikan ke nilai sebelumnya
        hideLoading();
        return; // Keluar dari fungsi
    }

    if (value < oldValue) {
        notification('info', 'Tidak bisa input kurang');
        $(idInput).val(oldValue); // Kembalikan ke nilai sebelumnya
        hideLoading();
        return;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('tambahQty') }}/${id}/${plu}/${value}`,
        type: 'PUT',
    })
    .done((res) =>{

        notification('success', res.message, null, 1000);
        getTransaksi();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
        hideLoading();
    });

}

$('#nomor_telepone').on('keyup', function(){
    const idTrx = $('#id_transaksi').val();

    var inputVal = $(this).val(); // Mendapatkan nilai input
    var charCount = inputVal.length;

    if(charCount <= 10 ){
        return;
    }
    showLoading();

    $.post(`{{ route('cekPromo') }}`, {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id_transaksi': idTrx,
        'nomor_telepone': inputVal
    })
    .done((res) =>{
        hideLoading();
        if(res.data['potonganHarga'] > 0){
            notification('success', res.pesan, null, 1000);
        }

        $('#diskon_view').val(formatRupiah(res.data['potonganHarga']));
        $('#total_bayar_view').val(formatRupiah(res.data['subtotal']));
        $('#total_bayar').val(res.data['total']);
        $('#total_view').val(`${formatRupiah(res.data['total'])}`);

        $('#terima').val(res.data['total']);
        $('#terima_view').val(formatRupiah(res.data['total']));
    })
    .fail((err) =>{
        hideLoading();
        notification('error', err.responseJSON.message);
    });
});

$('#terima').on('keyup', function(){
    var bayarView = parseInt($(this).val());
    var kembalian = parseInt($(this).val()) - parseInt( $('#total_bayar').val() );

    if(isNaN(bayarView)){
        $('#kembali_view').val( formatRupiah(0) );
        $(this).val(0);
        notification('error', 'Inputan tidak sesuai. Silahkan input angka');
        return;
    }

    $('#terima_view').val( formatRupiah(bayarView) );
    $('#kembali_view').val( formatRupiah(kembalian) );
})

$('#formKasir').on('submit', function(e){
    e.preventDefault();

    Swal.fire({
        text: "Simpan transaksi ?",
        showCancelButton: true,
        confirmButtonText: 'simpan',
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light',
            cancelButton: 'btn btn-label-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            showLoading()
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `{{ route('transaksiSelesai') }}`,
                type: 'PUT',
                data: $('#formKasir').serialize(),
                "Content-Type": "application/x-www-form-urlencoded"
            })
            .done((res) =>{
                notification('success', res.pesan);
                resetFormKasir();
            })
            .fail((err) =>{
                $.each(err.responseJSON['errors'], function(key, messages) {
                    $(`#${key}`).addClass('border-danger');
                    $(`#${key}`).siblings().addClass('border-danger');
                });
                notification('error', err.responseJSON.message);
                hideLoading()
                return;
            });

        }
    });
});

function resetFormKasir(){
    $('#formKasir')[0].reset();
    getTransaksi();
}
</script>
@endpush
