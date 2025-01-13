<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-capitalize" id="modalUserLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="pelunasanForm">
                        <div class="modal-body row">

                            <div class="col-12 table-responsive">
                                <table class="datatables-basic table text-nowrap" id="table_permission">
                                    <thead class="text-capitalize">
                                        <tr>
                                            <th>no</th>
                                            <th>nama permission</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="mb-2 col-12">
                                <input type="hidden" id="roleId" name="roleId">
                                <label for="permission" class="form-label">Tambah User Permission</label>
                                <select name="permission" id="permission" class="form-select select2" multiple="multiple">
                                    <option value="{{ auth()->user()->email }}">Super User</option>
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <button type="button" class="btn btn-sm btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-sm btn-success" type="submit">Selesai</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
