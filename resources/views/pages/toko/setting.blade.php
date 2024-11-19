@extends('pages.layouts.app')

@section('css')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gap-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <small class="d-block mb-1 text-muted">Pengaturan Informasi Toko</small>
                    <div class="row row-gap-3 mt-5">
                        <div class="col-12 col-md-8">
                            @include('pages.toko.info-toko')
                        </div>
                        <div class="col-12 col-md-4">
                            @include('pages.toko.struk-priview')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <small class="d-block mb-1 text-muted">Daftar Nomor Rekening</small>
                    <form action="" id="rekeningForm">
                        <div class="d-md-flex">
                            <input name="nama_bank" type="text" class="form-control" placeholder="Nama Bank" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Input nama bank">
                            <input name="nomor_rekening" type="text" class="form-control" placeholder="Nomor Rekening" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="input nomor rekening">
                            <input name="nama_pemilik" type="text" class="form-control" placeholder="Nama Pemilik" autocomplete="off" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Input nama pemilik">
                            <select name="default" class="form-select" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Setting default rekening">
                                <option value="">Default ... </option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <button class="btn btn-md btn-outline-success" type="submit" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Tambah rekening">
                                <i class="ti ti-circle-plus d-block"></i>
                            </button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table text-nowrap" id="tabelRekening">
                            <thead>
                                <tr>
                                    <th>no</th>
                                    <th class="text-center">
                                        <i class="menu-icon tf-icons ti ti-settings"></i>
                                    </th>
                                    <th>setting default</th>
                                    <th>nama bank</th>
                                    <th>nomor rekening</th>
                                    <th>nama pemilik</th>
                                    <th>default rekening</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="reader" style="display: none;"></div> <!-- Elemen ini dibutuhkan oleh html5-qrcode -->

@endsection

@push('js')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
Dropzone.autoDiscover = false;

$(function(){
    const logoForm = document.querySelector('#dropzone-upload-logo');
    new Dropzone( logoForm, {
        acceptedFiles: 'image/*',
        paramName: 'file',
        maxFilesize: 5,
        maxFiles: 1,
        success: function(file){
            const res = JSON.parse(file.xhr.response);
            notification('success', res.pesan);
            getDataToko();
        },
        error: function(file){
            const res = JSON.parse(file.xhr.response);
            notification('error', res.message);
        },
        complete: function(file){
            setTimeout(() => {
                this.removeFile(file);
            }, 2000);
        }
    })

    const qrForm = document.querySelector('#dropzone-upload-qr');
    new Dropzone( qrForm, {
        acceptedFiles: 'image/*',
        paramName: 'file',
        maxFilesize: 5,
        maxFiles: 1,
        success: function(file){
            const res = JSON.parse(file.xhr.response);
            notification('success', res.pesan);
            getDataToko();
        },
        error: function(file){
            const res = JSON.parse(file.xhr.response);
            notification('error', res.message);
        },
        complete: function(file){
            // Mendapatkan file gambar dari Dropzone
            const imageFile = file; // File object langsung dari Dropzone

                // Inisialisasi html5-qrcode
                const html5QrCode = new Html5Qrcode("reader");
                html5QrCode.scanFile(imageFile, true) // Mengirim File object, bukan data URL
                .then(decodedText => {
                    // Menampilkan hasil scan ke console
                    $('#qr_wa_text').val(decodedText);
                    $('#infoToko').submit();
                })
                .catch(err => {
                    console.log("Error scanning file. ", err);
                });

            setTimeout(() => {
                this.removeFile(file);
            }, 2000);
        }
    })

    getDataToko();
    getDataRekening();
})

function getDataRekening(){
    showLoading();
    $.get(`{{ route('data.rekening') }}`)
    .done((response) => {
        loadDataRekening(response.data.rekening);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function loadDataRekening(rekening){
    $('#tabelRekening').DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: rekening,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            { data: (data) => {
                return `
                    <div class="btn-group">
                    <button class="btn btn-xs btn-outline-danger" onclick="hapusRekening('${data.nomor_rekening}')" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Hapus rekening">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `;
            }},
            {data: (data) =>{
                return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.default ? 'checked' : '' } onclick="changeStatusDefault('${data.nomor_rekening}')" />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
            }},
            { data: (data) => {
                return data.nama_bank;
            }},
            { data: (data) => {
                return data.nomor_rekening;
            }},
            { data: (data) => {
                return data.nama_pemilik;
            }},
            { data: (data) => {
                return data.default ? `<span class="badge text-bg-success w-100">Y</span>` : `<span class="badge text-bg-danger w-100">N</span>`;
            }},
        ]
    });

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}

function hapusRekening(rekening){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('delete.rekening') }}/${rekening}`,
        type: 'DELETE',
    })
    .done((response) => {
        notification('success', response.pesan);
        getDataRekening();
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

$('#rekeningForm').on('submit', function(e){
    showLoading();
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('create.rekening') }}`,
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((response) => {
        notification('success', response.pesan);
        $(this)[0].reset();
        getDataRekening();
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
})

function changeStatusDefault(rekening){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('create.rekening') }}/${rekening}`,
        type: 'PUT',
    })
    .done((response) => {
        notification('success', response.pesan);
        getDataRekening();
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    })
}

function getDataToko(){
    $.get(`{{ route('toko.get') }}`)
    .done((res) => {
        $('#nama_perusahaan').val(res.data['nama_perusahaan']);
        $('#email').val(res.data['email']);
        $('#telepone').val(res.data['telepone']);
        $('#whatsapp').val(res.data['whatsapp']);
        $('#instagram').val(res.data['instagram']);
        $('#facebook').val(res.data['facebook']);
        $('#tiktok').val(res.data['tiktok']);
        $('#web').val(res.data['web']);
        $('#alamat_lengkap').val(res.data['alamat_lengkap']);


        $('#logoPerusahaanActive').attr('src', `{{ url('/') }}/${res.data['logo']}`);
        $('#qrWaPerusahaanActive').attr('src', `{{ url('/') }}/${res.data['qr_wa']}`);

        getPreviewStruk(res.data);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });

    $.get('{{ route('getPesanStruk') }}')
    .done((res) => {
        getPreviewStrukPesan(res.data);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    })
}

function getPreviewStruk(data){
    $('#struk_nama_perusahaan').text(data['nama_perusahaan']);
    $('#struk_logo_perusahaan').attr('src', `{{ url('/') }}/${data.logo}`);
    $('#struk_alamat_perusahaan').text(data['alamat_lengkap']);
    $('#struk_email_perusahaan').text(`Email: ${data['email']}`);

    $('#struk_telepone_perusahaan').text(`Telp: ${data['telepone']}`);
    $('#struk_wa_perusahaan').text(`Wa: ${data['whatsapp']}`);

    $('#struk_qrwa').attr('src', `{{ url('/') }}/${data.qr_wa}`);
}

function getPreviewStrukPesan(data){
    data.forEach((el, index) => {
        $('#pesan_struk').append(`<p class="mb-0">${el.pesan}</p>`)
    });
}

$('#infoToko').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('toko.update') }}',
        type: 'PUT',
        data: $(this).serialize(),
    })
    .done((res) =>{
        notification('success', res.pesan);
        getDataToko();
    })
    .fail((err) =>{
        console.log(err.responseJSON['errors']);
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
        });
        notification('error', err.responseJSON.message);
    });
});
</script>
@endpush
