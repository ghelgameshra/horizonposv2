<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalPelunasan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Pelunasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="pelunasanForm">
                        <div class="modal-body row">

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nama_customer">Nama Customer</label>
                                <input type="text" id="nama_customer" name="nama_customer" class="form-control" @readonly(true) />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nama_kasir">Nama Kasir</label>
                                <input type="text" id="nama_kasir" name="nama_kasir" class="form-control" @readonly(true) />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="invno">Nomor Invoice</label>
                                <input type="text" id="invno" name="invno" class="form-control" @readonly(true) />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="status_order">Status Order</label>
                                <input type="text" id="status_order" name="status_order" class="form-control" @readonly(true) />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="nomor_telepone">Nomor Telepone</label>
                                <input type="text" id="nomor_telepone" name="nomor_telepone" class="form-control" @readonly(true) />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="tanggal_transaksi">Tanggal Transaksi | Tipe Bayar</label>
                                <div class="input-group">
                                    <input type="text" id="tanggal_transaksi" name="tanggal_transaksi" class="form-control" @readonly(true) />
                                    <input type="text" id="tipe_bayar" name="tipe_bayar" class="form-control" @readonly(true) />
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="subtotal">Subtotal | Diskon</label>
                                <div class="input-group">
                                    <input type="text" id="subtotal" name="subtotal" class="form-control" @readonly(true) />
                                    <input type="text" id="diskon" name="diskon" class="form-control" @readonly(true) />
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="total">Uang Muka | Total</label>
                                <div class="input-group">
                                    <input type="text" id="uang_muka" name="uang_muka" class="form-control" @readonly(true) />
                                    <input type="text" id="total" name="total" class="form-control" @readonly(true) />
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="kembali">Kurang Bayar | Kembali</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-primary text-white" name="kurang_bayar" id="kurang_bayar" autocomplete="off" @readonly(true) data-kurang-bayar="">
                                    <input type="text" class="form-control bg-primary text-white" name="preview_kembali" id="preview_kembali" autocomplete="off" @readonly(true)>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="terima">Terima</label>
                                <div class="input-group">
                                    <input type="text" inputmode="numeric" class="form-control" name="terima" id="terima" autocomplete="off">
                                    <input type="text" inputmode="numeric" class="form-control" name="preview_terima" id="preview_terima" autocomplete="off" @readonly(true)>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="tipe_bayar_pelunasan">Tipe Bayar Pelunasan</label>
                                <div class="input-group">
                                    <select name="tipe_bayar_pelunasan" id="tipe_bayar_pelunasan" class="form-select">
                                        <option value="CSH">CASH</option>
                                        <option value="TRF">TRF</option>
                                    </select>
                                    <button class="btn btn-success" type="submit">Selesai</button>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table text-nowrap" id="transaksiDetailTable">
                                    <thead>
                                        <tr>
                                            <th>no</th>
                                            <th>nama file</th>
                                            <th>plu</th>
                                            <th>nama produk</th>
                                            <th>ukuran</th>
                                            <th>harga</th>
                                            <th>jumlah</th>
                                            <th>total</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
