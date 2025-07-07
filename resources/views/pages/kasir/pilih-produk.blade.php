<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="addDataModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Pilih produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="margin-top: -20px">

                        <div class="card-datatable table-responsive pt-0">
                            <table id="pilihProdukTable" class="table table-sm table-striped table-bordered text-nowrap">
                                <thead>
                                    <tr>
                                        <th>plu</th>
                                        <th>nama produk</th>
                                        <th>kategori</th>
                                        <th>Harga</th>
                                        <th></th>
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
@push('js')
<script>
async function downloadProdukJual() {
    try {
        const response = await $.get(`{{ route('kasir.produk') }}`);
        const produk = response?.data?.produk ?? [];

        localStorage.setItem('produkJual', JSON.stringify(produk));
        makeTableJual(produk);
    } catch (error) {
        console.error('Gagal mengambil produk:', error);
    }
}

function makeTableJual(data) {
    const tableId = '#pilihProdukTable';

    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().clear().rows.add(data).draw();
        return;
    }

    $(tableId).DataTable({
        processing: true,
        autoWidth: false,
        data: data,
        columns: [
            { data: 'plu' },
            { data: 'nama_produk' },
            { data: 'kategori' },
            { data: 'harga_jual', render: data => formatRupiah(data) },
            {
                data: 'plu',
                render: (data) => `
                    <button class="btn btn-xs btn-success d-block" onclick="addItemList('${data}')">
                        <i class="ti ti-circle-plus"></i>
                    </button>
                `
            },
        ],
        dom: 'ftp',
        ordering: false,
        searching: true,
    });
}

function addItemList(plu) {
    const id_transaksi = $('#id_transaksi').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.addItemList') }}`,
        data: {
            id_transaksi, plu
        },
        type: 'POST',
    })
    .done((response) =>{
        console.log(response);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

$(document).ready(() => {
    const produk = localStorage.getItem('produkJual');
    if (produk) {
        try {
            makeTableJual(JSON.parse(produk));
        } catch (e) {
            console.warn('Data produkJual korup, mengunduh ulang...');
            downloadProdukJual();
        }
    } else {
        downloadProdukJual();
    }
});
</script>
@endpush
