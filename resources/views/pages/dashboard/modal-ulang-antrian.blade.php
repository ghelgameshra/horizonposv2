<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalUlangAntrian" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUlangAntrianTitle">Panggil Antrian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" id="antrianUlangForm">
                            @csrf
                            <div class="input-group">
                                <select name="jenis_antrian" id="jenis_antrian" class="form-select">
                                    <option value="">Jenis antrian</option>
                                    <option value="ATPD">Desain</option>
                                    <option value="ATPS">Siap Cetak</option>
                                </select>
                                <select name="ganti_meja" id="ganti_meja" class="form-select">
                                    <option value="">Nomor meja</option>
                                </select>
                            </div>
                            <div class="col-12 btn-group mt-3 d-flex">
                                <button type="submit" class="btn btn-primary flex-fill">Pilih</button>
                                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
