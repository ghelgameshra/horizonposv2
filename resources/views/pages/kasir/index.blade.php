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
$(document).ready(function(){
    showLoading();
    newOrder();

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
})

function newOrder() {
    const user = `{{ auth()->user()->id }}`;
    $.get(`{{ route('kasir.getOrder') }}`)
    .done((response) => {
        const data = response.data;
        showContent(data);
        saveConfigSatuan(data)
    })
    .fail((response) => {
        console.log(response);
    })
}

function saveConfigSatuan(data) {
    localStorage.setItem('configSatuan', JSON.stringify(data.satuan));
}

function showContent(data) {
    $('#id_transaksi').val(data.order.order.id);
    createTableOrder(data);
    hideLoading();
}
</script>
@endpush
