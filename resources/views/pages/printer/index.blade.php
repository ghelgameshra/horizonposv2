@extends('pages.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('lib') }}/assets/vendor/libs/select2/select2.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Setting Printer</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-12 col-md-4">
                            <div class="input-group d-flex">
                                <button class="btn btn-sm flex-fill btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" id="tambahPrinter">
                                    + Printer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-8">
                            <div class="card-datatable table-responsive pt-0">
                                <small>Setting Printer Struk</small>
                                <table class="datatables-basic table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>no</th>
                                            <th>
                                                <i class="menu-icon tf-icons ti ti-settings"></i>
                                            </th>
                                            <th>status</th>
                                            <th>nama setting</th>
                                        </tr>
                                    </thead>
                                </table>

                                <small>List Printer</small>
                                <table class="table text-nowrap" id="listPrinterTable">
                                    <thead>
                                        <tr>
                                            <th>no</th>
                                            <th>nama printer</th>
                                            <th>jenis printer</th>
                                            <th>ip printer</th>
                                            <th class="text-center">default</th>
                                            <th class="text-center">
                                                <i class="menu-icon tf-icons ti ti-settings"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
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
@include('pages.printer.tambah-data')
@endsection

@push('js')
<script src="{{ asset('lib') }}/assets/vendor/libs/select2/select2.js"></script>
<script>
$(function(){
    getDataToko();

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '{{ route('getPrinterSetting') }}',
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
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.status ? 'checked' : '' } onclick="changeStatusSetting('${data.key}')" />
                        <input type="text" hidden id="preview_${data.key}" value="${data.status}">
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
            }},
            {data: (data) =>{
                return data.status ? `<span class="badge text-bg-success">AKTIF</span>` : `<span class="badge text-bg-danger">NON AKTIF</span>`;
            }},
            {data: (data) =>{
                return data.nama_setting
            }},
        ],
        dom: ''
    }).on('draw.dt', function(){
        setPreviewStruk();
    });

    var tablePrinter = $('#listPrinterTable').DataTable({
        processing: true,
        paging: true,
        ordering: false,
        ajax: {
            url: '{{ route('printer.get') }}',
        },
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) =>{return data.nama_printer}},
            {data: (data) =>{return data.jenis_printer}},
            {data: (data) =>{return data.ip_printer}},
            {data: (data) =>{
                return data.default_printer ? `<span class="badge text-bg-success">Y</span>` : `<span class="badge text-bg-danger">N</span>`;
            }},
            {data: (data) => {
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-primary" onclick="testPrint('${data.jenis_printer}')">
                        <i class="ti ti-printer d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-danger" onclick="deletePrinter(${data.id})">
                        <i class="ti ti-trash d-block"></i>
                    </button>
                </div>
                `;
            }}
        ],
        dom: ''
    });
})

function testPrint(printer){
    showLoading();
    $.get(`{{ route('printer.test') }}`, {
        'jenis_printer': printer
    })
    .done((res) =>{
        notification('success', res.pesan);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
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

function getPreviewStrukPesan(data){
    $('#pesan_struk').children().remove();
    data.forEach((el, index) => {
        $('#pesan_struk').append(`<p class="mb-0">${el.pesan}</p>`)
    });
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

function setPreviewStruk(){
    const previewIds = [
        { input: '#preview_LOGS', target: '#logo_struk' },
        { input: '#preview_HEDS', target: '#header_struk' },
        { input: '#preview_ISIS', target: '#isi_struk' },
        { input: '#preview_FOOS', target: '#footer_struk' },
        { input: '#preview_PESS', target: '#pesan_struk' },
        { input: '#preview_QRSK', target: '#footer_qrwa_struk' }
    ];

    previewIds.forEach(function(item) {
        ($(item.input).val() < 1)
            ? $(item.target).addClass('d-none')
            : $(item.target).removeClass('d-none');
    });
}

$('#addFormOffCanvas').on('submit', function(e){
    e.preventDefault();
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('printer.insert') }}',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.pesan);
        $('#addFormOffCanvas')[0].reset();
        $('.offcanvas-header button').click();
        reloadDataTable($('#listPrinterTable'));
    })
    .fail((err) =>{
        console.log(err.responseJSON['errors']);
        $.each(err.responseJSON['errors'], function(key, messages) {
            $(`#${key}`).addClass('border-danger');
        });
        notification('error', err.responseJSON.message);
    });
});

function changeStatusSetting(key){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('printer.changeStatus') }}',
        type: 'put',
        data: {
            'key': key
        },
    })
    .done((res) =>{
        notification('success', res.pesan);
        reloadDataTable($('.datatables-basic'));
        getDataToko();
    })
    .fail((err) =>{
        reloadDataTable($('.datatables-basic'));
        notification('error', err.responseJSON.message);
    });
}

function deletePrinter(id){
    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('printer.delete') }}',
        type: 'DELETE',
        data: {
            'id': id
        },
    })
    .done((res) =>{
        notification('success', res.pesan);
        reloadDataTable($('#listPrinterTable'));
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}
</script>
@endpush
