<div class="col-lg-3 col-md-6">
    <div class="mt-3">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEndKategori" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Kategori</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <hr>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form action="" id="addKategori" method="POST" style="margin-top: -20px" class="needs-validation">
                    <div class="mb-2 col-12">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input class="form-control" type="text" id="nama_kategori" name="nama_kategori" value="" placeholder="Nama kategori"
                            autofocus />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <input class="form-control" type="text" id="deskripsi" name="deskripsi" value="" placeholder="Deskripsi" />
                    </div>
                    <div class="btn-group d-flex">
                        <button type="submit" class="btn btn-sm flex-fill btn-primary d-grid w-100">+ Tambah</button>
                        <button type="button" class="btn btn-sm flex-fill btn-label-secondary d-grid w-100" data-bs-dismiss="offcanvas">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
