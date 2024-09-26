<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalListPengeluaran" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Tambah list pengeluaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="listPengeluaranForm">
                        <div class="modal-body row">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="jenis_pengeluaran">Jenis Pengeluaran</label>
                                <select name="jenis_pengeluaran" id="jenis_pengeluaran" class="form-select"></select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="tanggal_pengeluaran">Tanggal Pengeluaran</label>
                                <input type="text" @readonly(true) id="tanggal_pengeluaran" name="tanggal_pengeluaran" class="form-control" value="{{ now()->format('Y-m-d') }}" />
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="total">Total</label>
                                <div class="input-group">
                                    <input type="text" inputmode="numeric" id="total" name="total"
                                        class="form-control" autocomplete="off" value="0" />
                                    <input type="text" @readonly(true) class="form-control" value="Rp. 0" id="preview_total">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="tipe_bayar">Total</label>
                                <select name="tipe_bayar" id="tipe_bayar" class="form-select">
                                    <option value="">Tipe bayar ... </option>
                                    <option value="CSH">Cash</option>
                                    <option value="TRF">Transfer</option>
                                </select>
                            </div>

                            <div class="col-12 text-center mt-5">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
