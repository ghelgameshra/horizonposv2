<div class="col-12">
    <div class="mt-3">
        <!-- Modal -->
        <div class="modal fade" id="modalUserRole" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-capitalize" id="modalUserRoleLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" id="pelunasanForm">
                        <div class="modal-body row">

                            <div class="mb-2 col-12">
                                <input type="hidden" id="userId" name="userId">
                                <label for="userRole" class="form-label">Ubah Role User</label>
                                <select name="userRole" id="userRole" class="form-select select2">
                                    <option value="">Pilih Role</option>
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
