<div class="col-lg-3 col-md-6">
    <div class="mt-3">
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasEndLabel" class="offcanvas-title">Tambah Printer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <hr>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form action="" id="addFormOffCanvas" method="POST" style="margin-top: -20px" class="needs-validation">
                    <div class="mb-2 col-12">
                        <label for="nama_printer" class="form-label">Nama Printer | Protocol</label>
                        <small class="text-danger d-block" style="font-size: 0.7em">*Nama printer harus sama dengan nama printer yang dishare</small>
                        <div class="input-group">
                            <input class="form-control" type="text" id="nama_printer" name="nama_printer" value="" placeholder="pos-58" autocomplete="off" autofocus />
                            <input class="form-control" type="text" id="protocol_printer" name="protocol_printer" value="" placeholder="SMB" autocomplete="off" />
                        </div>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="jenis_printer" class="form-label">Jenis Printer | Default</label>
                        <div class="input-group">
                            <select name="jenis_printer" id="jenis_printer" class="form-select">
                                <option value="">Jenis ... </option>
                                <option value="ANTRIAN">ANTRIAN</option>
                                <option value="STRUK">STRUK</option>
                                <option value="LABEL">LABEL</option>
                                <option value="INK">INK</option>
                            </select>
                            <select name="default_printer" id="default_printer" class="form-select">
                                <option value="">Default Printer ... </option>
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2 col-12">
                        <label for="ip_printer" class="form-label">IP Printer | Port Printer</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="ip_printer" name="ip_printer" value="" placeholder="Enter IP Address" autocomplete="off" />
                            <input class="form-control" type="text" id="port_printer" name="port_printer" value="" placeholder="Enter Port Number" autocomplete="off" />
                        </div>
                    </div>

                    <div class="mb-2 col-12">
                        <label for="username_printer" class="form-label">Username Printer</label>
                        <input class="form-control" type="text" id="username_printer" name="username_printer" value="" placeholder="Enter Username" autocomplete="off" />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="password_printer" class="form-label">Password Printer</label>
                        <input class="form-control" type="password" id="password_printer" name="password_printer" value="" placeholder="Enter Password" autocomplete="off" />
                    </div>

                    <div class="mb-2 col-12">
                        <label for="keterangan_printer" class="form-label">Keterangan Printer</label>
                        <input class="form-control" type="text" id="keterangan_printer" name="keterangan_printer" value="" placeholder="Enter Description" autocomplete="off" />
                    </div>

                    <button class="btn btn-sm btn-primary w-100" type="submit">Tambah</button>
                </form>
            </div>
        </div>
    </div>
</div>
