<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalPromosi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPromosiTitle">Modal Promosi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="promosiForm" method="POST">
                        <div class="modal-body row g-1">

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nama_promo">Nama Promo</label>
                                <input type="text" id="nama_promo" name="nama_promo" class="form-control" />
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label" for="tipe_promo">Tipe Promo</label>
                                <select name="tipe_promo" id="tipe_promo" class="form-select">
                                    <option value="">Pilih...</option>
                                    <option value="PRODUK">PRODUK</option>
                                    <option value="MEMBER">MEMBER</option>
                                    <option value="TOTAL">TOTAL BELANJA</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label" for="tipe_potongan">Tipe Potongan</label>
                                <select name="tipe_potongan" id="tipe_potongan" class="form-select">
                                    <option value="">Pilih...</option>
                                    <option value="%">PERSENTASE %</option>
                                    <option value="$">FIX NOMINAL</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="detail_promo">Detail Promosi</label>
                                <textarea name="detail_promo" id="detail_promo" class="form-control" ></textarea>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="nilai_potongan">Nilai Potongan</label>
                                <input type="text" id="nilai_potongan" name="nilai_potongan" class="form-control" value="0" />
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="nominal_min_pembelian">Minimal Pembelian</label>
                                <input type="text" id="nominal_min_pembelian" name="nominal_min_pembelian" class="form-control" value="0" />
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="nominal_maks_pembelian">Maksimal Pembelian</label>
                                <input type="text" id="nominal_maks_pembelian" name="nominal_maks_pembelian" class="form-control" value="0" />
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="tanggal_mulai">Periode Awal</label>
                                <input type="text" name="tanggal_mulai" id="tanggal_mulai" placeholder="MM/DD/YYYY" class="form-control" autocomplete="off" />
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label" for="tanggal_selesai">Periode Akhir</label>
                                <input type="text" name="tanggal_selesai" id="tanggal_selesai" placeholder="MM/DD/YYYY" class="form-control" autocomplete="off" />
                            </div>

                            <button class="btn btn-md btn-primary mt-3" type="submit">Simpan</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
$('#tambahPromosi').click(function(){
    $('input').attr('autocomplete', 'off');
    tambahEdit();
})
$(() => {
    var tanggalMulai = $('#tanggal_mulai'),
        tanggalSelesai = $('#tanggal_selesai')
    ;
    tanggalMulai.datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        orientation: isRtl ? 'auto right' : 'auto left'
    });

    tanggalSelesai.datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        orientation: isRtl ? 'auto right' : 'auto left'
    });
})

const tambahEdit = (action = 'add', kodePromo = '') => {
    if(action === 'add') {
        $('#modalPromosiTitle').text('TAMBAH DATA PROMOSI');
        $('#promosiForm').attr('action', `{{ route('promo.create') }}`);
        $('#modalPromosi').modal('show');
    }

    if(action === 'edit') {
        $.get(`{{ route('promo.detail') }}/${kodePromo}`)
        .done((response) => {
            const data = response.data.promosi;

            $('#kode_promo').val(data.kode_promo);
            $('#nama_promo').val(data.nama_promo);
            $('#detail_promo').val(data.detail_promo);
            $('#promo_member').val(data.promo_member);
            $('#tipe_promo').val(data.tipe_promo);
            $('#tipe_potongan').val(data.tipe_potongan);
            $('#nilai_potongan').val(data.nilai_potongan);
            $('#nominal_min_pembelian').val(data.nominal_min_pembelian);
            $('#nominal_maks_pembelian').val(data.nominal_maks_pembelian);
            $('#tanggal_mulai').val(data.tanggal_mulai);
            $('#tanggal_selesai').val(data.tanggal_selesai);

            $('#modalPromosiTitle').text(`EDIT DATA PROMOSI #${data.kode_promo}`);
            $('#promosiForm').attr('action', `{{ route('promo.update') }}/${data.kode_promo}`);
        })

        $('#modalPromosi').modal('show');
        $('#promosiForm button').removeClass('btn-primary');
        $('#promosiForm button').addClass('btn-warning');
    }
}

$('#promosiForm').on('submit', function(e){
    e.preventDefault();

    if ($('#promosiForm').attr('action')?.includes(`{{ route('promo.update') }}`)) {
        editPromo();
        return;
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: $(this).attr('action'),
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
    })
    .done((res) =>{
        notification('success', res.message);
        reloadDataTable($('.datatables-basic'));
        $('#modalPromosi').modal('hide');
        $('#promosiForm')[0].reset();
    })
    .fail((err) =>{
        notification('error', err.responseJSON.message);
    });
})

const editPromo = () => {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': `{{ csrf_token() }}`
        },
        url: $('#promosiForm').attr('action'),
        type: 'PUT', // tetap POST karena Laravel pakai spoofing
        data: $('#promosiForm').serialize(),
    })
    .done((res) => {
        notification('success', res.message);
        reloadDataTable($('.datatables-basic'));
        $('#modalPromosi').modal('hide');
        $('#promosiForm')[0].reset();
    })
    .fail((err) => {
        notification('error', err.responseJSON.message);
    });
}
</script>
@endpush
