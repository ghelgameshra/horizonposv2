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

$(function(){
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

    setTimeout(() => {
        getProdukJual();
        let idTrx = $('#id_transaksi').val();
        let table = $('.datatables-basic').DataTable({
            processing: true,
            paging: true,
            ajax: {
                url: `{{ route('transaksiBaruDetail') }}?id_transaksi=${idTrx}`,
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
                    return data.nama_produk.split(' ').slice(0, 2).join(' ');
                }},
                {data: (data) =>{return data.plu}},
                {data: (data) =>{
                    return `
                        <div class="input-group input-group-sm text-center">
                            <input type="text" class="form-control" placeholder="nama file" name="namaFilePlu_${data.plu}" id="namaFilePlu_${data.plu}" value="${data.namafile ? data.namafile : ''}" autocomplete="off" ${data.satuan === 'UNIT' ? 'disabled' : 'required' }>
                            <input type="text" class="form-control" placeholder="100x100" name="ukuranPlu_${data.plu}" id="ukuranPlu_${data.plu}" value="${data.ukuran ? data.ukuran : ''}" autocomplete="off" ${data.satuan === 'UNIT' ? 'disabled' : 'required' } onchange="tambahQty('${data.plu}')">
                        </div>
                    `
                }},
                {data: (data) =>{return formatRupiah(data.harga_jual)}},
                {data: (data) =>{
                    return `
                        <div class="input-group input-group-sm text-center">
                            <input type="text" inputmode="numeric" class="form-control" name="" id="tambahQtyPlu_${data.plu}" autocomplete="off" value="${data.jumlah}" onchange="tambahQty('${data.plu}')" data-old-value="${data.jumlah}">
                        </div>
                    `
                }},
                {data: (data) =>{return formatRupiah(data.total)}},
                {data: (data) =>{
                    return `
                    <div class="btn-group">
                        <button class="btn btn-xs btn-outline-danger" onclick="deleteDataSales('${data.plu}')">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                    `
                }},
            ],
            dom: '',
            ordering: false
        });
    }, 500);

});

function getTransaksi(){
    const user = {{ auth()->user()->id }};
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ url('kasir/transaksi-baru/${user}') }}`,
        type: 'POST',
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        $('#total_view').val(`${formatRupiah(res.data['subtotal'])}`);
        $('#id_transaksi').val(res.data['id']);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
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
                return `<input type="checkbox" onclick="tambahDataSales('${data.plu}')">`;
            }},
        ],
        dom: 'ftp',
        ordering: false,
        bSort: false,
        searchable: true,
    });
}

function tambahDataSales(plu){
    const idTrx = $('#id_transaksi').val();

    $('#addDataModal').modal('hide');
    $.post(`{{ route('transaksiLog') }}?plu=${plu}&idTransaksi=${idTrx}`, {
        '_token': $('meta[name="csrf-token"]').attr('content'),
    })
    .done((res) =>{
        notification('success', res.pesan, null, 1000);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
    reloadData();
}

function deleteDataSales(plu){
    const idTrx = $('#id_transaksi').val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('transaksiLogDelete') }}`,
        type: 'DELETE',
        data: {
            'plu': plu,
            'idTransaksi': idTrx
        }
    })
    .done((res) =>{
        notification('success', res.pesan, null, 1000);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });

    reloadData();
}

function tambahQty(plu) {
    let idTrx = $('#id_transaksi').val();
    const idInput = `#tambahQtyPlu_${plu}`;
    let value = parseInt($(idInput).val(), 10); // Ambil nilai input
    const oldValue = parseInt($(idInput).attr('data-old-value'), 10) || 0; // Ambil nilai sebelumnya, default 0 jika tidak ada

    const namaFile = $(`#namaFilePlu_${plu}`).val();
    const ukuran = $(`#ukuranPlu_${plu}`).val();

    if($(`#namaFilePlu_${plu}`).attr('required') || $(`#ukuranPlu_${plu}`).attr('required') ){
        if(namaFile === '' || ukuran === ''){
            notification('error', 'Inputan tidak sesuai. Nama file dan ukuran harus diisi');
            $(`#tambahQtyPlu_${plu}`).val(oldValue);
            return;
        }
    }

    if (isNaN(value)) {
        // Jika inputan bukan angka, tampilkan notifikasi dan kembalikan nilai input ke yang lama
        notification('error', 'Inputan tidak sesuai. Silahkan input angka');
        $(idInput).val(oldValue); // Kembalikan ke nilai sebelumnya
        return; // Keluar dari fungsi
    }

    if (value < oldValue) {
        notification('info', 'Tidak bisa input kurang');
        $(idInput).val(oldValue); // Kembalikan ke nilai sebelumnya
        return;
    }

    $.post(`{{ route('tambahQty') }}`, {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id_transaksi': idTrx,
        'plu': plu,
        'qtyTambah': value,
        'qtyAwal': oldValue,
        'nama_file': namaFile,
        'ukuran': ukuran
    })
    .done((res) =>{
        notification('success', res.pesan, null, 1000);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });

    reloadData();
}

function reloadData(){
    getTransaksi();
    $('.datatables-basic').DataTable().ajax.reload();
}

$('#nomor_telepone').on('keyup', function(){
    const idTrx = $('#id_transaksi').val();

    var inputVal = $(this).val(); // Mendapatkan nilai input
    var charCount = inputVal.length;

    if(charCount <= 6 ){
        return;
    }

    $.post(`{{ route('cekPromo') }}`, {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'id_transaksi': idTrx,
        'nomor_telepone': inputVal
    })
    .done((res) =>{
        if(res.data['potonganHarga'] > 0){
            notification('success', res.pesan, null, 1000);
        }

        $('#diskon_view').val(formatRupiah(res.data['potonganHarga']));
        $('#total_bayar_view').val(formatRupiah(res.data['total']));
        $('#total_bayar').val(res.data['total']);

        $('#terima').val(res.data['total']);
        $('#terima_view').val(formatRupiah(res.data['total']));
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });

    reloadData();
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

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `{{ route('transaksiSelesai') }}`,
                type: 'PUT',
                data: $(this).serialize(),
                "Content-Type": "application/x-www-form-urlencoded"
            })
            .done((res) =>{
                notification('success', res.pesan);
                setTimeout(() => {
                    window.location.href = '';
                }, 1000);
            })
            .fail((err) =>{
                $.each(err.responseJSON['errors'], function(key, messages) {
                    $(`#${key}`).addClass('border-danger');
                    $(`#${key}`).siblings().addClass('border-danger');
                });
                notification('error', err.responseJSON.message);
                return;
            });

        }
    });

});
</script>
@endpush
