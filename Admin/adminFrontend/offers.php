<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

// Fetch offers
$offers = [];
$result = $conn->query("SELECT * FROM offers");
if ($result) {
    $offers = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/contact_management.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Offers Management</i>
        </div>
        <button class="btn btn-sm"
            style="background: var(--gold); color: #2c2c2c; border: none; padding: 8px 20px; border-radius: 5px; font-weight: 500;"
            data-bs-toggle="modal" data-bs-target="#addOffer">
            + Add Offer
        </button>
    </div>

    <div class="info-card">
        <h4><i class="fas fa-tag" style="color: var(--gold); margin-right: 10px;"></i>Offers List</h4>
        <div class="table-responsive">
            <table class="table table-hover" id="offersTable" style="vertical-align: middle;">
                <thead>
                    <tr>
                        <th style="background: var(--gold); color: #2c2c2c;">Title</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Preview</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Description</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Status</th>
                        <th style="background: var(--gold); color: #2c2c2c; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($offer['title']) ?></td>
                            <td>
                                <?php if (!empty($offer['image'])): ?>
                                    <img src="../Admin/adminBackend/offers_images/<?= htmlspecialchars($offer['image']) ?>"
                                        alt="<?= htmlspecialchars($offer['title']) ?>"
                                        class="rounded shadow-sm offer-preview-img"
                                        data-img="../Admin/adminBackend/offers_images/<?= htmlspecialchars($offer['image']) ?>"
                                        style="width: 80px; height: 50px; object-fit: cover; border: 1px solid #dee2e6; cursor: pointer;">

                                <?php else: ?>
                                    <span class="text-muted small">No Image</span>
                                <?php endif; ?>
                            </td>

                            <div class="modal fade" id="imagePreviewModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content bg-transparent border-0">
                                        <div class="modal-body text-center p-0">
                                            <img id="previewImage" src="" class="img-fluid rounded shadow">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($offer['description']) ?>
                            </td>
                            <td>
                                <?php if ($offer['active']): ?>
                                    <span class="badge-verified"><i class="fas fa-check-circle"></i> Active</span>
                                <?php else: ?>
                                    <span class="badge-pending"><i class="fas fa-pause-circle"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div class="contact-actions">
                                    <button class="contact-action-btn contact-btn-edit" title="Edit" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?php echo $offer['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="contact-action-btn contact-btn-toggle offer-toggle-status"
                                        title="Toggle Status" data-id="<?= $offer['id'] ?>">
                                        <i class="<?= $offer['active'] ? 'fas fa-toggle-on' : 'fas fa-toggle-off' ?>"></i>
                                    </button>

                                    <form method="POST"
                                        action="../Admin/adminBackend/offer_delete.php?id=<?php echo $offer['id']; ?>"
                                        style="display:inline-block;">
                                        <button type="submit" class="contact-action-btn contact-btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this offer?')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $offer['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?= $offer['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST"
                                        action="../Admin/adminBackend/offer_edit.php?id=<?= $offer['id']; ?>"
                                        enctype="multipart/form-data">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">Edit Offer</h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <div class="mb-3">
                                                <label class="form-label package-label">Title</label>
                                                <input type="text" name="title" class="form-control package-input"
                                                    value="<?= htmlspecialchars($offer['title']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Change Image (optional)</label>
                                                <input type="file" name="image" class="form-control package-input"
                                                    accept="image/*">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Description</label>
                                                <textarea name="description" class="form-control package-input" rows="4"
                                                    required><?= htmlspecialchars($offer['description']); ?></textarea>
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

<div class="modal fade" id="addOffer" tabindex="-1" aria-labelledby="addOfferLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addOfferLabel">Add Offer</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addOfferForm" method="POST" action="../Admin/adminBackend/offer_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="title" class="form-label package-label">Title</label>
                        <input type="text" class="form-control package-input" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label package-label">Image</label>
                        <input type="file" class="form-control package-input" id="image" name="image" accept="image/*"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label package-label">Description</label>
                        <textarea class="form-control package-input" id="description" name="description" rows="4"
                            required></textarea>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">Add Offer</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'adminFrontend/footer.php'; ?>

<script>
    document.querySelectorAll('.offer-toggle-status').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const icon = this.querySelector('i');
            const statusCell = this.closest('tr').querySelector('td:nth-child(4)');

            fetch('../Admin/adminBackend/offer_status_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.active == 1) {
                            icon.classList.replace('fa-toggle-off', 'fa-toggle-on');
                            statusCell.innerHTML = '<span class="badge-verified"><i class="fas fa-check-circle"></i> Active</span>';
                        } else {
                            icon.classList.replace('fa-toggle-on', 'fa-toggle-off');
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

<script>
    document.querySelectorAll('.offer-preview-img').forEach(img => {
        img.addEventListener('click', function () {
            document.getElementById('previewImage').src = this.dataset.img;
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        });
    });
</script>