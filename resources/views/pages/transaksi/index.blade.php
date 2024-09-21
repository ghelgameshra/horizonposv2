@extends('pages.layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <small class="d-block mb-1 text-muted">Reprint Transaksi</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatables-basic table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>no</th>
                                        <th></th>
                                        <th>invno</th>
                                        <th>nama customer</th>
                                        <th>tanggal trx</th>
                                        <th>lunas</th>
                                        <th>status order</th>
                                        <th>kasir</th>
                                    </tr>
                                </thead>
                            </table>
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
$(function(){

    var table = $('.datatables-basic').DataTable({
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('transaksi.list') }}',
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
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-primary" onclick="showDetail('${data.invno}')">
                        <i class="ti ti-eye d-block"></i>
                    </button>
                    <button class="btn btn-xs btn-outline-info" onclick="reprintStruk('${data.invno}')">
                        <i class="ti ti-receipt d-block"></i>
                    </button>
                </div>
                `
            }},
            {data: (data) =>{return data.invno}},
            {data: (data) =>{return data.nama_customer}},
            {data: (data) =>{return data.tanggal_transaksi}},
            {data: (data) =>{
                return data.terima > 0 ? `<span class="badge text-bg-success">LUNAS</span>` : `<span class="badge text-bg-danger">BELUM</span>`;
            }},
            {data: (data) =>{
                return data.status_order === "DALAM ANTRIAN" ? `<span class="badge text-bg-warning">${data.status_order}</span>` : `<span class="badge text-bg-success">data.status_order</span>`;
            }},
            {data: (data) =>{return data.kasir.name}},
        ],
    });
})

function reprintStruk(invno){
    $.get(`{{ route('reprint-transaksi.print')}}/${invno}`)
    .done((response) => {
        notification('success', response.pesan);
    })
    .fail((response) => {
        notification('error', response.responseJSON.message);
    });
}
</script>
@endpush
