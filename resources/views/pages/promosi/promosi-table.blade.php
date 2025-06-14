<div class="card-datatable table-responsive pt-0">
    <table class="datatables-basic table text-nowrap">
    </table>
</div>

@push('js')
<script>
var table = $('.datatables-basic').DataTable({
    processing: true,
    paging: true,
    ajax: {
        url: '{{ route('promo.get') }}',
        dataSrc: 'data.promosi' // ambil array dari response
    },
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            },
            title: 'No'
        },
        {
            data: null,
            render: function (data, type, row, meta) {
                return `
                    <div class="btn-group">
                        <button class="btn btn-xs btn-outline-warning" onclick="tambahEdit('edit', '${data.kode_promo}')">
                            <i class="ti ti-eye d-block"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger" onclick="hapusPromo('${data.kode_promo}')">
                            <i class="ti ti-trash d-block"></i>
                        </button>
                    </div>
                `;
            },
            orderable: false,
            searchable: false
        },
        { data: 'kode_promo', title: 'Kode Promo' },
        {
            data: 'status_promo',
            title: 'Status',
            render: function (data, type, row) {
                return `
                    <label class="switch switch-square">
                        <input type="checkbox" class="switch-input"
                            ${data ? 'checked' : ''}
                            onclick="setStatus('${row.kode_promo}')"
                        />
                        <span class="switch-toggle-slider">
                            <span class="switch-on"></span>
                            <span class="switch-off"></span>
                        </span>
                    </label>
                `;
            }
        },
        {
            data: 'promo_member',
            title: 'Member',
            render: function (data) {
                return data ? `<span class="badge text-bg-success">Y</span>` : '<span class="badge text-bg-danger">N</span>';
            }
        },
        { data: 'nama_promo', title: 'nama promo' },
        {
            data: 'tanggal_mulai',
            title: 'Periode Awal',
            render: function (data) {
                const date = new Date(data);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        },
        {
            data: 'tanggal_selesai',
            title: 'Periode Akhir',
            render: function (data) {
                const date = new Date(data);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        },
        { data: 'total_penggunaan', title: 'total penggunaan' },
    ]
});

const hapusPromo = (kodePromo) => {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: `{{ route('promo.delete') }}/${kodePromo}`,
        type: 'DELETE',
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.message);
        reloadDataTable($('.datatables-basic'));
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}

const setStatus = (kodePromo) => {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: `{{ route('promo.setStatus') }}/${kodePromo}`,
        type: 'PUT',
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.message);
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
}
</script>
@endpush
