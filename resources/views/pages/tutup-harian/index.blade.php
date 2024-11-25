@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div id="inputNominalHarian" hidden>
        <form action="" id="formTutupHarian" class="row g-1">
            <div class="col-12 col-md-7">
                <div class="card" style="height: 100%">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <small class="d-block mb-1 text-muted">Tutup Harian : {{ now()->locale('id')->translatedFormat('l, Y-m-d') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @include('pages.tutup-harian.nominal')
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="card" style="height: 100%">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-0 fw-bold">Jumlah Terbilang : </p>
                                <div class="border text-center rounded px-3 py-3 bg-primary text-white">
                                    <small id="totalInputTerbilang" class="text-capitalize">Nol Rupiah</small>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div>
                                    <label for="user" class="form-label">Penanggung Jawab</label>
                                    <input type="text" class="form-control" id="user" name="user" autocomplete="off" @readonly(true) value="{{ auth()->user()->name }}" />
                                </div>
                                <div class="mb-3 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                </div>
                                <button class="btn btn-primary d-grid w-100" type="submit">Tutup Harian</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-1">
        <div class="col-12">
            <div class="card" style="height: 100%">
                <div class="card-header">
                    <small>Data Harian</small>
                </div>
                <div class="card-body table-responsive">
                    <table class="table text-nowrap" id="listDataHarian">
                        <thead>
                            <tr>
                                <th>no</th>
                                <th class="text-center">
                                    <i class="menu-icon tf-icons ti ti-settings"></i>
                                </th>
                                <th>id harian</th>
                                <th>tanggal harian</th>
                                <th>nominal harian</th>
                                <th>penanggung jawab</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(document).ready(function () {
    $('input[inputmode="numeric"]').each(function () {
        // Cari elemen input sebelumnya (yang memiliki value nominal)
        const nominalText = $(this).prev('input').val();

        // Ekstrak nilai nominal dari teks, misalnya "Rp. 100.000" menjadi 100000
        const nominalValue = nominalText
            .replace('Rp.', '')  // Hilangkan "Rp."
            .replace('.', '')   // Hilangkan titik
            .replace(',', '')   // Hilangkan koma
            .trim();            // Hilangkan spasi

        if (!nominalValue || isNaN(nominalValue)) {
            return; // Lewati elemen jika nominal kosong atau bukan angka
        }

        // Tambahkan atribut data-nominal pada elemen input
        $(this).attr('data-nominal', nominalValue);
    });

    $('input[inputmode="numeric"]').on('input', function () {
        let value = $(this).val();
        let numericValue = value.replace(/[^0-9]/g, '');
        $(this).val(numericValue);
    });

    $('input[inputmode="numeric"]').on('focus', function () {
        parseInt($(this).val()) === 0 ? $(this).val('') : $(this).val();
    });

    $('input[inputmode="numeric"]').on('blur', function () {
        $(this).val() === '' ? $(this).val(0) : $(this).val();
    });

    $('input[inputmode="numeric"]').on('input', function () {
        let total = 0; // Variabel untuk menyimpan total

        // Seleksi semua input dengan inputmode="numeric"
        const inputNominal = $('input[inputmode="numeric"]');

        inputNominal.each(function (index, element) {
            // Ambil nilai input dan data-nominal
            const inputValue = parseInt($(element).val()) || 0; // Nilai input, default ke 0 jika kosong
            const nominalValue = parseInt($(element).attr('data-nominal')) || 0; // Ambil data-nominal, default ke 0

            // Hitung total berdasarkan input value * data-nominal
            total += inputValue * nominalValue;
        });

        // Tampilkan total di konsol
        $('#totalInput').val(formatRupiah(total));
        $('#totalInputTerbilang').text(`${terbilangRupiah(total)} rupiah`);
    });

    chekTutupHarian();
    getDataHarian();
});

function getDataHarian(){
    $.get(`{{ route('data.tutupHarian') }}`)
    .done((response) => {
        const dataHarian = response.data.harian;
        createTableHarian(dataHarian);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function createTableHarian(data){
    const tableHarian = $('#listDataHarian').DataTable({
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
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-info" onclick="reprintStruk('${data.invno}')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Reprint struk harian">
                        <i class="ti ti-receipt d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{
                return data.invno;
            }},
            {data: (data) =>{
                return data.tanggal_harian;
            }},
            {data: (data) =>{
                return formatRupiah(data.rptotal);
            }},
            {data: (data) =>{
                return data.user;
            }},
        ]
    });

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}

function chekTutupHarian(){
    $.get(`{{ route('check.tutupHarian') }}`)
    .done((response) => {
        console.log(response.message);
        $('#inputNominalHarian').attr('hidden', false);
    })
    .fail((response) => {
        notification('info', response.responseJSON.message);
        $('#inputNominalHarian').remove();
    });
}

$('#formTutupHarian').on('submit', function(e){
    e.preventDefault();

    if(!$('#password').val()){
        notification('error', 'Password tidak boleh kosong');
        return;
    }

    Swal.fire({
        text: `Proses tutup harian dengan nominal ${$('#totalInput').val()} ?`,
        showCancelButton: true,
        confirmButtonText: 'Proses',
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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: `{{ route('create.tutupHarian') }}`,
                type: 'POST',
                data: new FormData($('#formTutupHarian')[0]),
                processData: false,
                contentType: false,
            })
            .done((response) => {
                notification('success', response.message);
                $('#formTutupHarian').remove();
                getDataHarian();
            })
            .fail((response) => {
                notification('error', response.responseJSON.message);
            })

        }
    });
})

</script>
@endpush
