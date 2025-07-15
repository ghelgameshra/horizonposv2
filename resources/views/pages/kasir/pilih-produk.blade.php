{{-- <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addDataOffcanvas">
    Pilih Produk
</button> --}}
@section('css')
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>

@endsection
<!-- Button Refresh Produk -->
<button id="refreshBtn"
    class="btn btn-primary rounded-circle shadow"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Refresh Produk Jual"
    style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1055; width: 50px; height: 50px;">
    <i id="refreshIcon" class="ti ti-refresh" style="font-size: 1.2rem;"></i>
</button>

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
// Inisialisasi tooltip (cukup sekali)
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
});

const refreshBtn = document.getElementById('refreshBtn');
const refreshIcon = document.getElementById('refreshIcon');

refreshBtn.addEventListener('click', () => {
    startLoadingAnimation();

    downloadProdukJual().finally(() => {
        stopLoadingAnimation();
    });
});

// Animasi loading (rotasi)
function startLoadingAnimation() {
    refreshIcon.classList.remove('ti-refresh');
    refreshIcon.classList.add('ti-loader', 'spin');
    refreshBtn.disabled = true;
}

// Kembali ke ikon semula
function stopLoadingAnimation() {
    refreshIcon.classList.remove('ti-loader', 'spin');
    refreshIcon.classList.add('ti-refresh');
    refreshBtn.disabled = false;
}


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
