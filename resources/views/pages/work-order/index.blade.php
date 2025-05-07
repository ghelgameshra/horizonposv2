@extends('pages.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Work Order Progress</h5>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">Interval</span>
                        <select name="intervalRefresh" id="intervalRefresh" class="form-select">
                            <option value="0.5">30 Detik</option>
                            <option value="1">1 Menit</option>
                            <option value="5">5 Menit</option>
                            <option value="10">10 Menit</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="accordion" id="table_maker">
            </div>
        </div>
    </div>
</div>
@include('pages.work-order.modal-show')
@endsection

@push('js')
<script>
$(function(){
    let urlParams = new URLSearchParams(window.location.search);
    let categoryName = urlParams.get('name');

    $('#workOrderName').text(`Work Order ${categoryName}`);

    let intervalRefresh = $('#intervalRefresh').val() * 60000;

    getStatusOrder();
    setInterval(() => {
        getStatusOrder();
    }, intervalRefresh);
})

function getStatusOrder(){
    const statusOrder = localStorage.getItem('status_order');
    if(!statusOrder){
        showLoading();
        $.get(`{{ route('statusOrder') }}`)
        .done((response) => {
            makeTableView(response.data.statusOrder);
            const data = JSON.stringify(response.data.statusOrder);
            localStorage.setItem("status_order", data);
        })
        .fail((response) => {
            notification('error', response.responseJSON.message);
        })
    }

    makeTableView(JSON.parse(statusOrder));
}

function makeTableView(statusOrder) {
    $('#table_maker').children().remove();

    statusOrder.forEach((element, index) => {
        let tempTableName = `table_${(element.nama_status.replace(/\s+/g, '_')).toLowerCase()}`;

        // Set class 'show' dan aria-expanded 'true' pada item pertama
        let isFirst = index === 0 ? 'show' : '';
        let ariaExpanded = index === 0 ? 'true' : 'false';

        $('#table_maker').append(`
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingAccordion${tempTableName}">
                    <button type="button" class="accordion-button ${isFirst ? '' : 'collapsed'}"
                            data-bs-toggle="collapse"
                            data-bs-target="#accordion_${tempTableName}"
                            aria-expanded="${ariaExpanded}"
                            aria-controls="accordion_${tempTableName}">
                        # ${element.nama_status}
                    </button>
                </h2>
                <div id="accordion_${tempTableName}" class="accordion-collapse collapse ${isFirst}"
                    aria-labelledby="headingAccordion${tempTableName}"
                    data-bs-parent="#table_maker">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="datatables-basic table text-nowrap" id="${tempTableName}">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th class="text-center">
                                            <i class="menu-icon tf-icons ti ti-settings"></i>
                                        </th>
                                        <th>Nama File</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah order</th>
                                        <th>Ukuran (CM)</th>
                                        <th>Worker</th>
                                        <th>Status Order</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>`);
    });

    getDataOrder(statusOrder);
}

function getDataOrder(statusOrder){
    showLoading();
    let urlParams = new URLSearchParams(window.location.search);
    let categoryId = urlParams.get('category');

    statusOrder.forEach(element => {
        $.get(`{{ route('workOrder.data') }}/${categoryId}/${element.nama_status}`)
        .done((response) => {
            setDataOrder(response.data, element.nama_status);
        })
        .fail((response) => {
            notification('error', response.responseJSON.message);
        })
    });
}

function setDataOrder(data, nama_status){
    const badgeClasses = {
        "DALAM ANTRIAN": "text-bg-warning",
        "CETAK": "text-bg-primary",
        "FINISHING": "text-bg-info",
        "PACKING": "text-bg-success",
        "SELESAI": "text-bg-secondary",
    };

    let namaProgess = nama_status.replace(/\s+/g, '_');
    $(`#table_${namaProgess.toLowerCase()}`).DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: data.order,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) => {
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-primary" onclick="updateProgress('${data.id}', '${data.status_order}')">
                        <i class="ti ti-player-track-next d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{
                return data.namafile.split(' ').slice(0, 2).join(' ');
            }},
            {data: (data) =>{
                return data.nama_produk;
            }},
            {data: (data) =>{
                return data.kategori;
            }},
            {data: (data) =>{
                return data.jumlah;
            }},
            {data: (data) =>{
                return data.ukuran;
            }},
            {data: (data) =>{
                return (data.worker) ? data.worker : '-';
            }},
            {data: (data) =>{
                const badgeClass = badgeClasses[data.status_order] || "text-bg-success";
                return `<span class="badge ${badgeClass} w-100">${data.status_order}</span>`;
            }},
        ]
    });
}

function updateProgress(id, statusOrder){
    const progress = {
        "DALAM ANTRIAN": "CETAK",
        "CETAK": "FINISHING",
        "FINISHING": "PACKING",
        "PACKING": "SELESAI",
    };

    let nextProgress = progress[statusOrder] || 'SELESAI';

    Swal.fire({
        text: "Update Progress Pesanan ?",
        showCancelButton: true,
        confirmButtonText: 'Lanjut',
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light',
            cancelButton: 'btn btn-label-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.value) {
            updateOrderProgress(id, nextProgress);
        }
    });
}

function updateOrderProgress(id, nextProgress){
    // showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('workOrder.updateProgress') }}/${id}/${nextProgress}`,
        type: 'PUT'
    })
    .done((res) =>{
        notification('success', res.pesan);
        getStatusOrder();
    })
    .fail((error) => {
        notification('error', error.responseJSON.message);
    })
}

</script>
@endpush
