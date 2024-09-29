@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/flatpickr/flatpickr.css" />
@endsection

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    @include('pages.dashboard.info')
    @include('pages.dashboard.chart')
</div>
<div id="loadingSpinner" class="d-none">
    <div class="position-absolute top-50 start-50" style="z-index: 99999">
        <!-- Chase -->
        <div class="sk-chase sk-primary">
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
            <div class="sk-chase-dot"></div>
        </div>
    </div>
    <div class="offcanvas-backdrop fade show bg-light"></div>
</div>
<!-- / Content -->
@endsection

@push('js')
@include('layouts.antrian')
<script src="{{ asset('lib') }}/assets/vendor/libs/flatpickr/flatpickr.js"></script>
<script src="{{ asset('lib') }}/assets/vendor/libs/moment/moment.js"></script>
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
$(function(){
    const datepickerList = document.querySelectorAll('.dob-picker');
    if (datepickerList) {
        datepickerList.forEach(function (datepicker) {
        datepicker.flatpickr({
            monthSelectorType: 'static'
        });
        });
    }

    getData();
    setGetData();
    getDataAntrian();
})

function setGetData(){
    let timeInterval = $('#interval_get_data').val() * 60000;
    setInterval(() => {
        getData();
        getDataAntrian();
    }, timeInterval);
}

$('#periode_awal, #periode_akhir').on('input', function() {
    let periodeAwal = $('#periode_awal').val();
    let periodeAkhir = $('#periode_akhir').val();

    // Ambil tanggal hari ini
    let today = new Date();
    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0'); // Bulan (0-based, jadi ditambah 1)
    let dd = String(today.getDate()).padStart(2, '0'); // Hari

    let todayFormatted = `${yyyy}-${mm}-${dd}`;

    // Jika periode akhir melebihi hari ini, ubah kembali ke hari ini
    if (periodeAkhir > todayFormatted) {
        $('#periode_akhir').val(todayFormatted);
        periodeAkhir = todayFormatted;
    }

    if(periodeAwal > periodeAkhir){
        $('#periode_awal').val(periodeAkhir);
        periodeAwal = periodeAkhir;
    }

    getData();
});


function getData(){
    $('#loadingSpinner').removeClass('d-none');
    let periodeAwal = $('#periode_awal').val();
    let periodeAkhir = $('#periode_akhir').val();

    $.get(`{{ route('dashboard.data') }}`, {
        'tgl_awal': periodeAwal,
        'tgl_akhir': periodeAkhir
    })
    .done((response) => {
        setTimeout(() => {
            $('#loadingSpinner').addClass('d-none');
            showDataDashboard(response.data);
        }, 1000);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    });
}

function showDataDashboard(data){
    $('#totalPesanan').text(data.totalPesanan.toLocaleString('id-ID'));
    $('#totalPesananSelesai').text(data.totalPesananSelesai.toLocaleString('id-ID'));
    $('#totalPesananDiambil').text(data.totalPesananDiambil.toLocaleString('id-ID'));

    $('#totalPendapatan').text(formatRupiah(data.totalPendapatan));
    $('#pendapatanCash').text(formatRupiah(data.pendapatanCash));
    $('#pendapatanNonTunai').text(formatRupiah(data.pendapatanNonTunai));
    $('#piutang').text(formatRupiah(data.piutang));

    $('#totalProdukTerjual').text(`${data.totalProdukTerjual.toLocaleString('id-ID')} Produk Terjual`);
    $('#totalProdukJual').text(`${data.totalProdukTerjual.toLocaleString('id-ID')} Produk Terjual`);

    createChartKategori(data);
    createChartProduk(data);
    createChartPenjualan(data);
}

let chartJualKategori, chartJualProduk, chartPenjualan;
function createChartKategori(data) {
    let backgroundColor = [
        'rgba(255, 99, 132, 0.2)',  // Merah Muda
        'rgba(54, 162, 235, 0.2)',  // Biru
        'rgba(255, 206, 86, 0.2)',  // Kuning
        'rgba(75, 192, 192, 0.2)',  // Biru Muda
        'rgba(153, 102, 255, 0.2)', // Ungu
        'rgba(255, 159, 64, 0.2)',  // Oranye
        'rgba(201, 203, 207, 0.2)', // Abu-abu Muda
        'rgba(255, 127, 80, 0.2)',  // Coral
        'rgba(60, 179, 113, 0.2)',  // Hijau Tua
        'rgba(139, 69, 19, 0.2)'    // Coklat
    ];

    let borderColor = [
        'rgba(255, 99, 132, 1)',  // Merah Muda
        'rgba(54, 162, 235, 1)',  // Biru
        'rgba(255, 206, 86, 1)',  // Kuning
        'rgba(75, 192, 192, 1)',  // Biru Muda
        'rgba(153, 102, 255, 1)', // Ungu
        'rgba(255, 159, 64, 1)',  // Oranye
        'rgba(201, 203, 207, 1)', // Abu-abu Muda
        'rgba(255, 127, 80, 1)',  // Coral
        'rgba(60, 179, 113, 1)',  // Hijau Tua
        'rgba(139, 69, 19, 1)'    // Coklat
    ];

    // Jika chart sudah ada, hancurkan dulu sebelum membuat yang baru
    if (chartJualKategori) {
        chartJualKategori.destroy();
    }

    let kategori = data.kategori.map(item => item.nama_kategori);
    let jumlahPenjualan = Object.values(data.jumlahPenjualan);

    // Jika data kosong atau semua nilainya nol, ganti dengan data default
    if (jumlahPenjualan.length === 0 || jumlahPenjualan.every(item => item === 0)) {
        jumlahPenjualan = [100]; // Chart utuh jika tidak ada data
        kategori = ['Tidak Ada Data']; // Label default untuk kategori
    }

    const ctx = document.getElementById('produkTerjualChart').getContext('2d');
    chartJualKategori = new Chart(ctx, {
        type: 'doughnut', // Tipe chart: donut
        data: {
            labels: kategori, // Data label diambil dari dataKategori
            datasets: [{
                data: jumlahPenjualan,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || '';
                            return `${label}: ${value}`; // Menampilkan nilai tanpa persen
                        }
                    }
                }
            }
        }
    });
}

function createChartProduk(data) {
    let backgroundColor = [
        'rgba(255, 99, 132, 0.2)',  // Merah Muda
        'rgba(54, 162, 235, 0.2)',  // Biru
        'rgba(255, 206, 86, 0.2)',  // Kuning
        'rgba(75, 192, 192, 0.2)',  // Biru Muda
        'rgba(153, 102, 255, 0.2)', // Ungu
        'rgba(255, 159, 64, 0.2)',  // Oranye
        'rgba(201, 203, 207, 0.2)', // Abu-abu Muda
        'rgba(255, 127, 80, 0.2)',  // Coral
        'rgba(60, 179, 113, 0.2)',  // Hijau Tua
        'rgba(139, 69, 19, 0.2)'    // Coklat
    ];

    let borderColor = [
        'rgba(255, 99, 132, 1)',  // Merah Muda
        'rgba(54, 162, 235, 1)',  // Biru
        'rgba(255, 206, 86, 1)',  // Kuning
        'rgba(75, 192, 192, 1)',  // Biru Muda
        'rgba(153, 102, 255, 1)', // Ungu
        'rgba(255, 159, 64, 1)',  // Oranye
        'rgba(201, 203, 207, 1)', // Abu-abu Muda
        'rgba(255, 127, 80, 1)',  // Coral
        'rgba(60, 179, 113, 1)',  // Hijau Tua
        'rgba(139, 69, 19, 1)'    // Coklat
    ];

    // Jika chart sudah ada, hancurkan dulu sebelum membuat yang baru
    if (chartJualProduk) {
        chartJualProduk.destroy();
    }

    let kategori = Object.keys(data.totalProdukJual);
    let jumlahPenjualan = Object.values(data.totalProdukJual);

    // Jika data kosong atau semua nilainya nol, ganti dengan data default
    if (jumlahPenjualan.length === 0 || jumlahPenjualan.every(item => item === 0)) {
        jumlahPenjualan = [100]; // Chart utuh jika tidak ada data
        kategori = ['Tidak Ada Data']; // Label default untuk kategori
    }

    const ctx = document.getElementById('produkTerjual').getContext('2d');
    chartJualProduk = new Chart(ctx, {
        type: 'doughnut', // Tipe chart: donut
        data: {
            labels: kategori, // Data label diambil dari dataKategori
            datasets: [{
                data: jumlahPenjualan,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    font: {
                        size: 3
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || '';
                            return `${label}: ${value}`; // Menampilkan nilai tanpa persen
                        }
                    }
                }
            }
        }
    });
}

function createChartPenjualan(data) {
    let backgroundColor = [
        'rgba(255, 99, 132, 0.2)',  // Merah Muda
        'rgba(54, 162, 235, 0.2)',  // Biru
        'rgba(255, 206, 86, 0.2)',  // Kuning
        'rgba(75, 192, 192, 0.2)',  // Biru Muda
        'rgba(153, 102, 255, 0.2)', // Ungu
        'rgba(255, 159, 64, 0.2)',  // Oranye
        'rgba(201, 203, 207, 0.2)', // Abu-abu Muda
        'rgba(255, 127, 80, 0.2)',  // Coral
        'rgba(60, 179, 113, 0.2)',  // Hijau Tua
        'rgba(139, 69, 19, 0.2)'    // Coklat
    ];

    let borderColor = [
        'rgba(255, 99, 132, 1)',  // Merah Muda
        'rgba(54, 162, 235, 1)',  // Biru
        'rgba(255, 206, 86, 1)',  // Kuning
        'rgba(75, 192, 192, 1)',  // Biru Muda
        'rgba(153, 102, 255, 1)', // Ungu
        'rgba(255, 159, 64, 1)',  // Oranye
        'rgba(201, 203, 207, 1)', // Abu-abu Muda
        'rgba(255, 127, 80, 1)',  // Coral
        'rgba(60, 179, 113, 1)',  // Hijau Tua
        'rgba(139, 69, 19, 1)'    // Coklat
    ];

    // Jika chart sudah ada, hancurkan dulu sebelum membuat yang baru
    if (chartPenjualan) {
        chartPenjualan.destroy();
    }

    let kategori = Object.keys(data.totalPenjualanPerTgl);
    let jumlahPenjualan = Object.values(data.totalPenjualanPerTgl);

    // Jika data kosong atau semua nilainya nol, ganti dengan data default
    if (jumlahPenjualan.length === 0 || jumlahPenjualan.every(item => item === 0)) {
        jumlahPenjualan = [100]; // Chart utuh jika tidak ada data
        kategori = ['Tidak Ada Data']; // Label default untuk kategori
    }

    const ctx = document.getElementById('totalPenjualanChart').getContext('2d');
    chartPenjualan = new Chart(ctx, {
        type: 'bar', // Tipe chart: donut
        data: {
            labels: kategori, // Data label diambil dari dataKategori
            datasets: [{
                label: "Total Penjualan Per Tanggal",
                data: jumlahPenjualan,
                fill: false,
                tension: 0.1,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    font: {
                        size: 3
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || '';
                            return `${label}: ${value}`; // Menampilkan nilai tanpa persen
                        }
                    }
                }
            }
        }
    });
}
</script>
@endpush
