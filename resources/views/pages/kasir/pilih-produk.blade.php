{{-- <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addDataOffcanvas">
    Pilih Produk
</button> --}}

<!-- Offcanvas Bottom -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="addDataOffcanvas" aria-labelledby="addDataOffcanvasLabel" style="height: 90vh;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addDataOffcanvasLabel">Pilih produk</h5>
        <button type="button" class="btn-close text-reset" id="addDataOffcanvasClose" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" style="margin-top: -20px; overflow-y: auto;">
        <input type="text" id="customSearchInput" class="form-control mb-2" placeholder="Cari produk..." autocomplete="off">
        <div class="card-datatable table-responsive pt-0">
            <table id="pilihProdukTable" class="table table-sm table-striped table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th>PLU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
$('#cariBarang').on('focus', function () {
    showOffcanvas();
});

// Tambahkan trigger dengan Ctrl + Enter
$(document).on('keydown', function (e) {
    if (e.ctrlKey && e.key === 'Enter') {
        showOffcanvas();
        $('#cariBarang').focus(); // optional: fokuskan ke input
    }
});

// Fungsi pembuka offcanvas
function showOffcanvas() {
    const offcanvasElement = document.getElementById('addDataOffcanvas');
    const bsOffcanvas = new bootstrap.Offcanvas(offcanvasElement);
    bsOffcanvas.show();
}

function hideOffcanvas() {
    $('#addDataOffcanvasClose').click();
}

function forceRemoveOffcanvasBackdrop() {
    document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
    document.body.classList.remove('offcanvas-backdrop', 'show', 'modal-open');
}


$('#addDataOffcanvasClose').on('click', function() {
    setTimeout(forceRemoveOffcanvasBackdrop, 200);
})


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
                    <button class="btn btn-xs btn-success btn-custom-darken d-block" onclick="addItemList('${data}')">
                        <i class="ti ti-circle-plus"></i>
                    </button>
                `
            },
        ],
        dom: 'tp',
        ordering: false,
        paging: true,
        pageLength: 15,
    });

    // Hubungkan search input custom
    $('#customSearchInput').on('keyup', function () {
        $(tableId).DataTable().search(this.value).draw();
    });
}

document.getElementById('addDataOffcanvas').addEventListener('shown.bs.offcanvas', function () {
    setTimeout(() => {
        $('#customSearchInput').focus();
    }, 200);
});


function debounce(fn, delay = 300) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), delay);
    };
}

$('#customSearchInput').on('keyup', debounce(function () {
    $('#pilihProdukTable').DataTable().search(this.value).draw();
}, 300));


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
