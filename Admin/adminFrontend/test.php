<div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1"
    aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">

            <form method="POST" enctype="multipart/form-data"
                action="../Admin/adminBackend/event_management_edit.php?id=<?php echo $row['id']; ?>">

                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold" id="editModalLabel<?php echo $row['id']; ?>">
                        Edit Table Type
                    </h5>
                    <button type="button" class="btn-close package-modal-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body package-modal-body">

                    <!-- NAME (full row) -->
                    <div class="mb-3">
                        <label class="form-label package-label">Table Name</label>
                        <input type="text" name="name" class="form-control package-input"
                            value="<?php echo htmlspecialchars($row['name']); ?>" required>
                    </div>

                    <!-- TWO COLUMNS -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control package-input"
                                value="<?php echo $row['price']; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Duration</label>
                            <input type="number" name="duration" class="form-control package-input"
                                value="<?php echo htmlspecialchars($row['duration']); ?>">
                        </div>
                    </div>

                    <!-- TWO COLUMNS -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Time Limit</label>
                            <input type="text" name="time_limit" class="form-control package-input"
                                value="<?php echo htmlspecialchars($row['time_limit']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Max Pax</label>
                            <input type="number" name="max_pax" class="form-control package-input"
                                value="<?php echo $row['max_pax']; ?>" required>
                        </div>
                    </div>

                    <!-- TWO COLUMNS -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Max Guests</label>
                            <input type="number" name="max_guests" class="form-control package-input"
                                value="<?php echo $row['max_guests']; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label package-label">Availability</label>
                            <select name="is_available" class="form-control package-input">
                                <option value="1" <?php echo ($row['is_available'] == 1 ? 'selected' : ''); ?>>Available</option>
                                <option value="0" <?php echo ($row['is_available'] == 0 ? 'selected' : ''); ?>>Not Available</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label package-label">Status</label>
                        <select name="status" class="form-control package-input">
                                <option value="Occupied" <?php echo ($row['status'] == 'Occupied' ? 'selected' : ''); ?>>Occupied</option>
                                <option value="Available" <?php echo ($row['status'] ==  'Available' ? 'selected' : ''); ?>>Available</option>
                            </select>
                    </div>

                    <!-- DESCRIPTION (full row) -->
                    <div class="mb-3">
                        <label class="form-label package-label">Description</label>
                        <textarea name="description" class="form-control package-input" rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                    </div>

                    <!-- NOTES (full row) -->
                    <div class="mb-3">
                        <label class="form-label package-label">Notes</label>
                        <textarea name="notes" class="form-control package-input" rows="2"><?php echo htmlspecialchars($row['notes']); ?></textarea>
                    </div>

                    <!-- IMAGE (ONE INPUT ONLY) -->
                    <div class="mb-3">
                        <label class="form-label package-label">Image</label>
                        <input type="file" name="image[]" class="form-control package-input" multiple accept="image/*">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn package-btn-save">Save Changes</button>
                </div>

            </form>

        </div>
    </div>
</div>