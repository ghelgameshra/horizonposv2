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
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input class="form-control" type="text" id="nama_produk" name="nama_produk" value="" placeholder="Nama produk" autofocus />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="id_kategori" class="form-label">Kategori</label>
                        <select name="id_kategori" id="id_kategori" class="form-select select2">
                            <option value="">kategori produk</option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <div class="input-group">
                            <input class="form-control " type="text" id="harga_beli" name="harga_beli" value="0" placeholder="Nama produk" />
                            <input type="text" class="form-control" id="preview_harga_beli" @disabled(true) value="Rp. 0">
                        </div>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="harga_jual" name="harga_jual" value="0" placeholder="Nama produk" />
                            <input type="text" class="form-control" id="preview_harga_jual" @disabled(true) value="Rp. 0">
                        </div>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="merk" class="form-label">Merk</label>
                        <input class="form-control" type="text" id="merk" name="merk" value="" placeholder="Merk produk" />
                    </div>
                    <div class="mb-2 col-12">
                        <label for="jenis_ukuran" class="form-label">Jenis Ukuran</label>
                        <select name="jenis_ukuran" id="jenis_ukuran" class="form-select select2">
                            <option value="">Jenis ukuran produk</option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="satuan" class="form-label">Satuan Jual</label>
                        <select name="satuan" id="satuan" class="form-select select2">
                            <option value="">Satuan jual produk</option>
                        </select>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="kode_supplier" class="form-label">Kode Supplier</label>
                        <select name="kode_supplier" id="kode_supplier" class="form-select select2">
                            <option value="">Kode supplier produk</option>
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
