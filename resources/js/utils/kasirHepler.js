function generateInputGroup(data) {
    const configSatuan = JSON.parse(localStorage.getItem("configSatuan") || "[]");
    const config = configSatuan.find(c => c.nama_satuan === data.satuan);

    const inputNamaFileEnabled = config?.input_namafile === 1;
    const inputUkuranEnabled = config?.input_ukuran === 1;

    const isSingleInput = inputNamaFileEnabled !== inputUkuranEnabled;

    const inputNamaFile = `
        <input type="text"
            class="form-control text-uppercase
                ${isSingleInput ? 'rounded' : inputNamaFileEnabled ? 'rounded-start' : ''}"
            placeholder="nama file"
            name="namafile_${data.id}"
            id="namafile_${data.id}"
            value="${data.namafile || ''}" autocomplete="off"
            data-namafile-old="${data.namafile || ''}"
            ${inputNamaFileEnabled ? 'required' : 'hidden'}
            onblur="setFileName('${data.id}')">
    `;

    const inputUkuran = `
        <input type="text"
            class="form-control text-uppercase
                ${isSingleInput ? 'rounded' : inputUkuranEnabled ? 'rounded-end' : ''}"
            placeholder="ukuran"
            name="size_${data.id}"
            id="size_${data.id}"
            value="${data.ukuran || ''}" autocomplete="off"
            data-size-old="${data.ukuran || ''}"
            ${inputUkuranEnabled ? 'required' : 'hidden'}
            onblur="setSize('${data.id}')">
    `;

    return `
        <div class="input-group input-group-sm text-center">
            ${inputNamaFileEnabled ? inputNamaFile : ''}
            ${inputUkuranEnabled ? inputUkuran : ''}
        </div>
    `;
}

function createTableOrder(data) {
    const order = data.order.order;
    const orderList = data.order.orderLists;
    $('#total_bayar').val(order.subtotal);
    $('#total_bayar_view').val(formatRupiah(order.subtotal));
    $('#subtotal_view').val(formatRupiah(order.total));
    $('#diskon_view').val(formatRupiah(order.diskon));

    let potonganProduk = 0;
    orderList.forEach(element => {
        potonganProduk += element.potongan;
    });
    $('#diskon_view').val(formatRupiah(potonganProduk))

    const table = $('#order-table');
    table.DataTable({
        dom: '',
        ordering: false,
        destroy: true,
        data: orderList,
        columns: [
            {
                data: null, // Tidak ada data yang terkait
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Menambahkan 1 untuk nomor urut (index mulai dari 0)
                },
                title: 'No'
            },
            {data: (data) =>{
                return data.nama_produk;
            }},
            {data: (data) =>{
                return generateInputGroup(data);
            }},
            {data: (data) =>{return formatRupiah(data.harga_jual)}},
            {data: (data) =>{
                return `
                <div class="input-group input-group-sm justify-content-center">
                    <button class="btn btn-primary btn-sm" type="button" onclick="recudeItem('${data.id}')">-</button>
                    <input type="text" class="form-control text-center" name="jumlah_id_${data.id}" id="jumlah_id_${data.id}" value="${data.jumlah}" data-jumlah-id-${data.id}="${data.jumlah}" style="max-width: 60px;" autocomplete="off">
                    <button class="btn btn-primary btn-sm" type="button" onclick="addItem('${data.id}')">+</button>
                </div>
                `
            }},
            {data: (data) =>{return formatRupiah(data.total)}},
            {data: (data) =>{return formatRupiah(data.gross)}},
            {data: (data) =>{
                return `
                <div class="btn-group">
                    <button class="btn btn-xs btn-outline-danger" onclick="removeItem('${data.id}')">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                `
            }},
        ],
    });
}

export {
    generateInputGroup, createTableOrder
}
