<div class="col-md-3">
    <input type="text" class="form-control bg-primary text-center fw-bold text-white py-4" style="font-size: 2em"
        value="Rp 0" disabled id="subtotal_view">
</div>
<div class="col-md-9">
    <form action="" id="formKasir" method="POST">
        <input type="text" hidden name="id_transaksi" id="id_transaksi">
        <input type="number" value="0" disabled id="total_bayar" hidden>
        <input type="number" value="0" id="terima" name="terima" hidden>

        <div class="row g-1">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text" style="width: 35%">Total</span>
                    <input type="text" class="form-control" value="Rp. 0" disabled id="total_bayar_view">
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text" style="width: 35%">Diskon</span>
                    <input type="text" class="form-control" value="Rp. 0" disabled id="diskon_view">
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text" style="width: 35%">Kembali</span>
                    <input type="text" class="form-control" value="Rp 0" disabled id="kembali_view">
                </div>

                <div class="input-group input-group-sm mb-1">

                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text">
                        <i class="menu-icon tf-icons ti ti-phone"></i>
                    </span>
                    <input type="text" class="form-control" value="" id="nomor_telepone" name="nomor_telepone"
                        placeholder="Nomor telepone customer">
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text">
                        <i class="menu-icon tf-icons ti ti-user"></i>
                    </span>
                    <input type="text" class="form-control" value="" id="nama_customer" name="nama_customer"
                        placeholder="Nama customer" type="button" autocomplete="off">
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text">
                        <i class="menu-icon tf-icons ti ti-cash"></i>
                    </span>
                    <select name="tipe_bayar" id="tipe_bayar" class="form-select">
                        <option value="CSH">CASH</option>
                        <option value="DPCSH">DPCSH</option>
                        <option value="DPTRF">DPTRF</option>
                        <option value="TRF">TRF</option>
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm mb-1">
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text">
                            <i class="menu-icon tf-icons ti ti-currency-dollar"></i>
                        </span>
                        <input type="text" class="form-control" value="0" id="terima_view" placeholder="23000"
                            autocomplete="off" name="terima_view" inputmode="numeric" data-terima-old="0">
                    </div>
                </div>
                <div class="input-group input-group-sm mb-1">
                    <button class="btn btn-xs btn-success d-block w-100" type="submit">
                            <i class="ti ti-send"></i>
                            <span>Proses Pesanan</span>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

@push('js')
<script>
$('#nomor_telepone').on('blur', function (e) {
    const idTransaksi = $('#id_transaksi').val();
    let value = $(this).val().trim();

    if (!value) {
        value = '081';
    } else {
        value = formatNomorTelepon(value);
        $(this).val(value); // update input agar terlihat bersih
    }

    showLoading();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.checkPromo') }}/${idTransaksi}`,
        type: 'POST',
        data: {
            nomor_telepone: value
        }
    })
    .done((response) => {
        showTotal(response.data);
        hideLoading();
    })
    .fail((err) => {
        notification('error', err.responseJSON?.message || 'Gagal cek promo');
        hideLoading();
    });
});

/**
 * Format nomor telepon: +62xxx → 08xxx, hapus semua spasi dan tanda selain angka
 * @param {string} nomor
 * @returns {string}
 */
function formatNomorTelepon(nomor) {
    // Ubah +62 di awal menjadi 08
    nomor = nomor.replace(/^\+62/, '0');

    // Hapus semua karakter selain angka
    nomor = nomor.replace(/[^\d]/g, '');

    return nomor;
}


function showTotal(data) {
    const member = data.potongan.member;
    const subtotal = data.order.order.total;
    (member ? $('#nama_customer').val(member.nama_lengkap) : $('#nama_customer').val(''));

    const potonganMember = data.potongan.potongan_member;
    const potonganProduk = data.potongan.potongan_produk;
    let potonganProdukTotal = 0;

    potonganProduk.forEach(element => {
        potonganProdukTotal += element.potongan;
    });

    const potonganTotal = potonganMember + potonganProdukTotal;

    $('#diskon_view').val(formatRupiah(potonganTotal));
    $('#subtotal_view').val(formatRupiah(subtotal));
}

$('#terima_view').on('input', function () {
    const $input = $(this);
    let rawValue = $input.val().toString().replace(/[^0-9]/g, ''); // ambil angka saja
    let formattedValue = formatRupiah(rawValue);                    // format ke rupiah

    // Simpan nilai asli ke data attribute
    let numericValue = rawValue === '' ? '0' : rawValue;

    $input.val(formattedValue);
    $input.data('terima-old', numericValue);
    $('#terima').val(numericValue);

    const total = $('#total_bayar_view');
    const totalNumeric = (total.val() || '')
        .replace(/Rp\s?/i, '')   // hilangkan 'Rp ' atau 'rp'
        .replace(/\./g, '')      // hilangkan semua titik
        .replace(/,/g, '')       // (opsional) hilangkan koma, jika ada
        .trim();                 // hilangkan spasi di depan/belakang

    $('#kembali_view').val(formatRupiah(parseInt(numericValue) - parseInt(totalNumeric)));
});

$('#formKasir').on('submit', function (e) {
    e.preventDefault();

    const idTransaksi = $('#id_transaksi').val();
    const form = this;
    const data = new FormData(form);

    Swal.fire({
        text: "Simpan Transaksi ?",
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light',
            cancelButton: 'btn btn-label-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {

            const formObject = Object.fromEntries(data.entries());

            if(formObject.nomor_telepone === '') {
                notification('error', `Nomor telepone belum diinput`, null, 1000);
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `/kasir/checkout/${idTransaksi}`, // gunakan route langsung jika tidak bisa pakai blade directive
                method: 'POST',
                data: data,
                processData: false,
                contentType: false,
            })
            .done((response) => {
                notification('success', response.message || 'Pesanan baru selesai dibuat dan masuk dalam antrian');
                form.reset(); // jika mau reset form
                $('#subtotal_view').val('Rp 0');
                newOrder();
            })
            .fail((err) => {
                const msg = err.responseJSON?.message || 'Gagal selesai transaksi';
                notification('error', msg);
            });
        }
    });


});

</script>
@endpush
