@extends('pages.layouts.app')

@section('css')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
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
    </div>
</div>
@endsection

@push('js')
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
            setTimeout(() => {
                this.removeFile(file);
            }, 2000);
        }
    })

    getDataToko();

})

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
