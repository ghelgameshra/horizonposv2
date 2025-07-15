<table class="datatables-basic table table-sm table-bordered table-striped text-nowrap" id="order-table">
    <thead>
        <tr>
            <th>no</th>
            <th>nama produk</th>
            <th class="text-center">nama file | ukuran</th>
            <th>Harga</th>
            <th>qty</th>
            <th>subtotal</th>
            <th>gross</th>
            <th class="text-center">
                <i class="menu-icon tf-icons ti ti-table-minus"></i>
            </th>
        </tr>
    </thead>
</table>


@push('js')
<script>
function addItemList(plu) {
    $('#addDataModal').modal('hide');
    showLoading();

    const id_transaksi = $('#id_transaksi').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.addItemList') }}`,
        data: {
            'id_transaksi': id_transaksi,
            'plu': plu
        },
        type: 'POST',
    })
    .done((response) =>{
        createTableOrder(response.data);
        hideLoading();
        hideOffcanvas();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

function removeItem(id){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.removeItemOrder') }}/${id}`,
        type: 'DELETE',
    })
    .done((response) =>{
        createTableOrder(response.data);

    })
    .fail((err) =>{
        notification('error', err.responseJSON?.message || 'Gagal hapus item');
    });
}

function updateQty(id, action) {
    const $input = $(`#jumlah_id_${id}`);
    if ($input.length === 0) return;

    const current = parseInt(($input.val() || '').toString().trim(), 10);
    const original = parseInt(($input.data(`jumlah-id-${id}`) || '').toString().trim(), 10);

    if (isNaN(current) || isNaN(original)) {
        notification('error', 'Jumlah tidak valid');
        return;
    }

    if (current === 0) {
        removeItem(id);
        return;
    }

    const diff = action === 'add' ? current - original : original - current;
    const qty = diff === 0 ? 1 : diff;

    const url = action === 'add'
        ? `{{ route('kasir.addItemQty') }}/${id}`
        : `{{ route('kasir.reduceItemQty') }}/${id}`;

    showLoading();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        type: 'POST',
        data: { qty }
    })
    .done((response) => {
        hideLoading();
        createTableOrder(response.data);
    })
    .fail((err) => {
        notification('error', err.responseJSON?.message || 'Gagal memperbarui qty');
    });
}

function addItem(id) {
    updateQty(id, 'add');
}

function recudeItem(id) {
    updateQty(id, 'reduce');
}


function setFileName(id) {
    const $input = $(`#namafile_${id}`);
    const current = $input.val().trim();
    const original = $input.data('namafile-old').trim();

    if (current === original) {
        return;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.setFileName') }}/${id}`,
        data: { filename: current },
        type: 'POST',
    })
    .done((response) => {
        console.log(response.message);
        // Update data-namafile-old agar perubahan selanjutnya bisa terdeteksi lagi
        $input.data('namafile-old', current);
    })
    .fail((err) => {
        notification('error', err.responseJSON?.message || 'Gagal memperbarui nama file');
    });
}

function setSize(id) {
    const pattern = /^\d{1,4}[xX]\d{1,4}$/;
    const $input = $(`#size_${id}`);
    const current = $input.val().trim();
    const original = $input.data('size-old').trim();

    if (current === original) {
        return;
    }

    if(!pattern.test($input.val().trim())) {
        notification('error', 'Input ukuran tidak valid PXL');
        return;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('kasir.setSize') }}/${id}`,
        data: { size: current },
        type: 'POST',
    })
    .done((response) => {
        console.log(response.message);
        // Update data-size-old agar perubahan selanjutnya bisa terdeteksi lagi
        $input.data('size-old', current);
        createTableOrder(response.data);
    })
    .fail((err) => {
        notification('error', err.responseJSON?.message || 'Gagal memperbarui nama file');
    });
}
</script>
@endpush
