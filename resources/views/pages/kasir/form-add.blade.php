<div class="col-12" style="z-index: 99; position: relative; top: -20px">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 text-center mb-1">
                    <div class="row g-1">
                        <div class="col-md-4">
                            <input type="text" class="form-control bg-primary text-center fw-bold text-white py-4" style="font-size: 2em" value="Rp. 0" disabled id="total_view">
                        </div>
                        <div class="col-md-8">
                            <form action="" id="formKasir" method="POST">
                                <input type="text" hidden name="id_transaksi" id="id_transaksi">

                                <div class="row g-0">
                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text">
                                                <i class="menu-icon tf-icons ti ti-user"></i>
                                            </span>
                                            <input type="text" class="form-control" value="" id="nama_customer" name="nama_customer" placeholder="Nama customer" type="button" autocomplete="off">

                                            <span class="input-group-text">
                                                <i class="menu-icon tf-icons ti ti-phone"></i>
                                            </span>
                                            <input type="text" class="form-control" value="" id="nomor_telepone" name="nomor_telepone" placeholder="Nomor telepone customer">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text" style="width: 13%">Diskon</span>
                                            <input type="text" class="form-control" value="Rp. 0" disabled id="diskon_view">

                                            <span class="input-group-text" style="width: 13%">Total Bayar</span>
                                            <input type="text" class="form-control" value="Rp. 0" disabled id="total_bayar_view">
                                            <input type="text" class="form-control" value="Rp. 0" disabled id="total_bayar" hidden>

                                            <span class="input-group-text" style="width: 13%">Tipe Bayar</span>
                                            <select name="tipe_bayar" id="tipe_bayar" class="form-select">
                                                <option value="CSH">CASH</option>
                                                <option value="DPCSH">DPCSH</option>
                                                <option value="DPTRF">DPTRF</option>
                                                <option value="TRF">TRF</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-1">
                                            <span class="input-group-text" style="width: 13%">Bayar</span>
                                            <input type="text" class="form-control" value="0" id="terima" placeholder="23000" autocomplete="off" name="terima" inputmode="numeric">
                                            <input type="text" class="form-control" value="Rp. 0" id="terima_view" disabled>

                                            <span class="input-group-text" style="width: 13%">Kembali</span>
                                            <input type="text" class="form-control" value="Rp. 0" disabled id="kembali_view">
                                            <button class="btn btn-xs btn-success" type="submit">Proses Pesanan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card-datatable table-responsive pt-0">
                        <div class="input-group input-group-sm mb-1 mt-3">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDataModal">
                                <i class="menu-icon tf-icons ti ti-search"></i>
                            </button>
                            <input type="text" class="form-control border" placeholder="cari barang ... (ctrl+enter)">
                            <input type="text" class="form-control border-0 border-start text-end" value="" id="nameTag" @readonly(true)>
                        </div>
                        <table class="datatables-basic table table-sm table-bordered table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>no</th>
                                    <th>nama produk</th>
                                    <th>plu</th>
                                    <th class="text-center">nama file | ukuran</th>
                                    <th>Harga</th>
                                    <th style="width: 13%">qty</th>
                                    <th>subtotal</th>
                                    <th class="text-center">
                                        <i class="menu-icon tf-icons ti ti-table-minus"></i>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
