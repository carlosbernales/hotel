<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';
$table_type = [];
$result = $conn->query("SELECT * FROM contact_info");
if ($result) {
    $table_type = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<link rel="stylesheet" href="../Admin/adminFrontend/css/contact_management.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Contact Information</i>
        </div>
        <button class="btn btn-sm"
            style="background: var(--gold); color: #2c2c2c; border: none; padding: 8px 20px; border-radius: 5px; font-weight: 500;"
            data-bs-toggle="modal" data-bs-target="#addContact">
            + Add Contact
        </button>

    </div>

    <div class="info-card">
        <h4><i class="fas fa-address-book" style="color: var(--gold); margin-right: 10px;"></i>Contact Links</h4>

        <div class="table-responsive">
            <table class="table table-hover" id="contactTable">
                <thead>
                    <tr>
                        <th style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px;">Display Text
                        </th>
                        <th style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px;">Link</th>
                        <th style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px;">Type</th>
                        <th style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px;">Order</th>
                        <th style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px;">Status</th>
                        <th
                            style="background: var(--gold); color: #2c2c2c; border: none; padding: 12px; text-align: center;">
                            Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($table_type as $contact): ?>
                        <tr style="transition: background 0.2s;">
                            <td style="padding: 15px; vertical-align: middle;">
                                <i class="<?= htmlspecialchars($contact['icon_class']) ?>"
                                    style="color: var(--gold); margin-right: 8px;"></i>
                                <strong><?= htmlspecialchars($contact['display_text']) ?></strong>
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                <a href="<?= htmlspecialchars($contact['link']) ?>" <?= $contact['is_external'] ? 'target="_blank"' : '' ?> style="color: #007bff; text-decoration: none;">
                                    <?= htmlspecialchars($contact['link']) ?>
                                    <?php if ($contact['is_external']): ?>
                                        <i class="fas fa-external-link-alt"
                                            style="font-size: 0.8rem; margin-left: 5px; opacity: 0.7;"></i>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td>
                                <span
                                    class="contact-badge <?= $contact['is_external'] ? 'contact-badge-external' : 'contact-badge-internal' ?>">
                                    <i
                                        class="<?= $contact['is_external'] ? 'fas fa-external-link-alt' : 'fas fa-link' ?>"></i>
                                    <?= $contact['is_external'] ? 'External' : 'Internal' ?>
                                </span>
                            </td>
                            <td style="padding: 15px; vertical-align: middle;">
                                <span
                                    style="background: #e9ecef; padding: 5px 12px; border-radius: 5px; font-weight: 500;"><?= intval($contact['display_order']) ?></span>
                            </td>
                            <td>
                                <?php if ($contact['active']): ?>
                                    <span class="badge-verified"><i class="fas fa-check-circle"></i> Active</span>
                                <?php else: ?>
                                    <span class="badge-pending"><i class="fas fa-pause-circle"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                <div class="contact-actions">
                                    <button class="contact-action-btn contact-btn-edit" title="Edit" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?php echo $contact['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="contact-action-btn contact-btn-toggle" title="Toggle Status"
                                        data-id="<?= $contact['id'] ?>">
                                        <i class="<?= $contact['active'] ? 'fas fa-toggle-on' : 'fas fa-toggle-off' ?>"></i>
                                    </button>

                                    <form method="POST"
                                        action="../Admin/adminBackend/contact_delete.php?id=<?php echo $contact['id']; ?>"
                                        style="display:inline-block;">
                                        <button type="submit" class="contact-action-btn contact-btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this contact?')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>


                        <div class="modal fade" id="editModal<?php echo $contact['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?php echo $contact['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/contact_edit.php?id=<?php echo $contact['id']; ?>">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">
                                                Edit Contact
                                            </h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <div class="mb-3">
                                                <label class="form-label package-label">Display Text</label>
                                                <input type="text" name="display_text" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($contact['display_text']); ?>"
                                                    required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Link</label>
                                                <input type="text" name="link" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($contact['link']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Display Order</label>
                                                <input type="text" name="display_order" class="form-control package-input"
                                                    value="<?php echo htmlspecialchars($contact['display_order']); ?>"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label package-label">External</label>
                                                <select name="is_external" class="form-control package-input" required>
                                                    <option value="1" <?php echo ($contact['is_external'] == 1) ? 'selected' : ''; ?>>External</option>
                                                    <option value="0" <?php echo ($contact['is_external'] == 0) ? 'selected' : ''; ?>>Not External</option>
                                                </select>
                                            </div>


                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn package-btn-save">Save Changes</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="modal fade" id="addContact" tabindex="-1" aria-labelledby="addContactLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addContactLabel">Add Contact</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addPackageForm" method="POST" action="../Admin/adminBackend/contact_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="display_text" class="form-label package-label">Display Text</label>
                        <input type="text" class="form-control package-input" id="display_text" name="display_text"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label package-label">Link</label>
                        <input type="text" class="form-control package-input" id="link" name="link" required>
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label package-label">Display Order</label>
                        <input type="text" name="display_order" class="form-control package-input"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    <div class="mb-3">
                        <label for="active" class="form-label package-label">Status</label>
                        <select name="active" class="form-control package-input" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="is_external" class="form-label package-label">External</label>
                        <select name="is_external" class="form-control package-input" required>
                            <option value="1">External</option>
                            <option value="0">Not External</option>
                        </select>
                    </div>


                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Add Contact
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<?php include 'adminFrontend/footer.php'; ?>

<script>
    document.querySelectorAll('.contact-btn-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const icon = this.querySelector('i');
            const statusCell = this.closest('tr').querySelector('td:nth-child(5)');

            fetch('../Admin/adminBackend/contact_status_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.active == 1) {
                            icon.classList.remove('fa-toggle-off');
                            icon.classList.add('fa-toggle-on');
                            statusCell.innerHTML = '<span class="badge-verified"><i class="fas fa-check-circle"></i> Active</span>';
                        } else {
                            icon.classList.remove('fa-toggle-on');
                            icon.classList.add('fa-toggle-off');
                            statusCell.innerHTML = '<span class="badge-pending"><i class="fas fa-pause-circle"></i> Inactive</span>';
                        }
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(err => console.error(err));
        });
    });
</script>