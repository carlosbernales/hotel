<div class="modal fade" id="editModal<?php echo $table_types['id']; ?>" tabindex="-1"
    aria-labelledby="editModalLabel<?php echo $table_types['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <form method="POST" enctype="multipart/form-data"
                action="../Admin/adminBackend/edit_table_package.php?id=<?php echo $table_types['id']; ?>">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="editModalLabel<?php echo $table_types['id']; ?>">
                        Edit Package
                    </h5>
                    <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body package-modal-body">
                    <!-- Package Name (single row) -->
                    <div class="mb-3">
                        <label class="form-label package-label">Package Name</label>
                        <input type="text" name="package_name" class="form-control package-input"
                            value="<?php echo htmlspecialchars($table_types['package_name']); ?>" required>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>