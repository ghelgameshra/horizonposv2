<div class="modal fade" id="satuanJualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Setting Satuan Jual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="margin-top: -20px">

                <div class="table-responsive pt-0">
                    <div class="input-group flex-nowrap">
                        <form id="tambahSatuanForm" class="col-4">
                            <div class="input-group flex-nowrap">
                                <input type="text" class="form-control" placeholder="Nama Satuan" name="nama_satuan" id="nama_satuan" autocomplete="off">
                                <button class="btn btn-primary" type="submit">Tambah Status</button>
                            </div>
                        </form>
                    </div>
                    <table id="satuanJualTable" class="table table-sm table-striped table-bordered text-nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>nama satuan</th>
                                <th>namafile</th>
                                <th>ukuran</th>
                                <th>deskripsi</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$('#openSatuanJual').click(async function () {
    await loadSatuanJual(); // pastikan data sudah dimuat
    $('#satuanJualModal').modal('show'); // baru tampilkan modal
});


async function loadSatuanJual() {
    try {
        const response = await $.get(`{{ route('satuanJual.data') }}`);

        if (!response || !response.data) {
            notification("error", "Gagal ambil data satuan jual");
            return;
        }

        const dataSatuan = response.data.satuan;
        const table = $('#satuanJualTable');

        // Hancurkan instance DataTable sebelumnya jika sudah ada
        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().clear().destroy();
        }

        table.DataTable({
            processing: true,
            paging: true,
            data: dataSatuan,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    title: 'No'
                },
                {data: (data) =>{return data.nama_satuan}},
                {data: (data) =>{
                    return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.input_namafile ? 'checked' : '' } onclick="changeStatusSatuan('${data.id}', 'input_namafile')" />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
                }},
                {data: (data) =>{
                    return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input" ${ data.input_ukuran ? 'checked' : '' } onclick="changeStatusSatuan('${data.id}', 'input_ukuran')" />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
                }},
                {data: (data) =>{ return data.deskripsi }},
                {data: (data) =>{
                    return `<button class="btn btn-xs btn-outline-danger" onclick="deleteSatuan(${data.id})">
                        <i class="ti ti-trash d-block"></i>
                    </button>`;
                }}
            ]
        });

    } catch (error) {
        console.error(error);
        notification("error", "Terjadi kesalahan saat mengambil data.");
    }
}

function changeStatusSatuan(id, config) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('satuanJual.updateConfig') }}/${id}/${config}`,
        type: 'PUT',
    })
    .done((res) =>{
        notification('success', res.message);
        loadSatuanJual();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

$('#tambahSatuanForm').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('satuanJual.insert') }}`,
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.message);
        $('#tambahSatuanForm')[0].reset();
        loadSatuanJual();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
})

function deleteSatuan(id) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('satuanJual.destroy') }}/${id}`,
        type: 'DELETE',
    })
    .done((res) =>{
        notification('success', res.message);
        loadSatuanJual();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}
</script>
@endpush
