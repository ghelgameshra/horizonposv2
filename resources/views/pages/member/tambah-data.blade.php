<div class="col-lg-3 col-md-6">
    <div class="mt-3">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasEndLabel" class="offcanvas-title">Offcanvas End</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <hr>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form action="" id="addFormOffCanvas" method="POST" style="margin-top: -20px" class="needs-validation">
                    <div class="mb-2 col-12">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" value="" placeholder="Nama lengkap" autofocus />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" cols="30" rows="4" class="form-control" placeholder="Alamat lengkap"></textarea>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="telepone" class="form-label">Nomor Telepone</label>
                        <input class="form-control" type="text" id="telepone" name="telepone" value="" placeholder="081233445566" />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="email" class="form-label">Email</label>
                        <input class="form-control" type="text" id="email" name="email" value="" placeholder="custome@email.com" />
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
