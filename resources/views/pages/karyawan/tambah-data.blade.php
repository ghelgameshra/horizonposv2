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
                        <input class="form-control" type="text" id="nama_lengkap" name="nama_lengkap" value="" placeholder="Johan fernandes" autofocus />
                    </div>

                    <div class="mb-2 col-12 d-none">
                        <label for="nik" class="form-label">NIK</label>
                        <input class="form-control" type="text" id="nik" name="nik" value="0" autofocus />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input class="form-control" type="text" id="tempat_lahir" name="tempat_lahir" value="" placeholder="Malang" />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input class="form-control" type="date" id="tanggal_lahir" name="tanggal_lahir" value="" />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="alamat_domisili" class="form-label">Alamat Domisili</label>
                        <textarea name="alamat_domisili" id="alamat_domisili" cols="30" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="telepone" class="form-label">Nomor Telepone</label>
                        <input class="form-control" type="text" id="telepone" name="telepone" value="" placeholder="081233445566" />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="jobdesk" class="form-label">Jobdesk</label>
                        <input class="form-control" type="text" id="jobdesk" name="jobdesk" value="" placeholder="admin umum" />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="ktp" class="form-label">NIK KTP</label>
                        <input class="form-control" type="text" id="ktp" name="ktp" value="" placeholder="35071234567890" />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="status_pernikahan" class="form-label">Status Nikah</label>
                        <select name="status_pernikahan" id="status_pernikahan" class="form-select select2">
                            <option value="">Pilih ... </option>
                            <option value="1">Sudah menikah</option>
                            <option value="0">Belum menikah</option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk Kerja</label>
                        <input class="form-control" type="date" id="tanggal_masuk" name="tanggal_masuk" value="" />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <select name="jabatan" id="jabatan" class="form-select select2">
                            <option value="">Pilih ... </option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="agama" class="form-label">Agama</label>
                        <select name="agama" id="agama" class="form-select select2">
                            <option value="">Pilih ... </option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                        <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-select select2">
                            <option value="">Pilih ... </option>
                        </select>
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
