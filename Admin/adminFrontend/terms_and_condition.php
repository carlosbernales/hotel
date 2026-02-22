<?php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$offers = [];
$result = $conn->query("SELECT * FROM terms_and_conditions");
if ($result) {
    $offers = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/contact_management.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Rules Management</i>
        </div>
        <button class="btn btn-sm"
            style="background: var(--gold); color: #2c2c2c; border: none; padding: 8px 20px; border-radius: 5px; font-weight: 500;"
            data-bs-toggle="modal" data-bs-target="#addRule">
            + Add Offer
        </button>
    </div>

    <div class="info-card">
        <h4><i class="fas fa-tag" style="color: var(--gold); margin-right: 10px;"></i>List of Rules</h4>
        <div class="table-responsive">
            <table class="table table-hover" id="offersTable" style="vertical-align: middle;">
                <thead>
                    <tr>
                        <th style="background: var(--gold); color: #2c2c2c;">Hotel</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Title</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Rule</th>
                        <th style="background: var(--gold); color: #2c2c2c;">Order</th>

                        <th style="background: var(--gold); color: #2c2c2c;">Status</th>
                        <th style="background: var(--gold); color: #2c2c2c; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($offer['hotel_name']) ?></td>


                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($offer['title']) ?>
                            </td>


                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($offer['rule_text']) ?>
                            </td>

                            <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($offer['display_order']) ?>
                            </td>

                            <td>
                                <?php if ($offer['is_active']): ?>
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
                                        <i
                                            class="<?= $offer['is_active'] ? 'fas fa-toggle-on' : 'fas fa-toggle-off' ?>"></i>
                                    </button>

                                    <button class="contact-action-btn contact-btn-delete offer-delete" title="Delete"
                                        data-id="<?= $offer['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $offer['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel<?= $offer['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content package-modal">

                                    <form method="POST" action="../Admin/adminBackend/edit_rule.php?id=<?= $offer['id']; ?>"
                                        enctype="multipart/form-data">

                                        <div class="modal-header package-modal-header">
                                            <h5 class="modal-title fw-bold">Edit Rule</h5>
                                            <button type="button" class="btn-close package-modal-close"
                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">

                                            <div class="mb-3">
                                                <label class="form-label package-label">Hotel Name</label>
                                                <input type="text" name="hotel_name" class="form-control package-input"
                                                    value="<?= htmlspecialchars($offer['hotel_name']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Title</label>
                                                <input type="text" name="title" class="form-control package-input"
                                                    value="<?= htmlspecialchars($offer['title']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Display Order</label>
                                                <input type="text" name="display_order" class="form-control package-input"
                                                    value="<?= htmlspecialchars($offer['display_order']); ?>" required
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label package-label">Rule</label>
                                                <textarea name="rule_text" class="form-control package-input" rows="4"
                                                    required><?= htmlspecialchars($offer['rule_text']); ?></textarea>
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

<div class="modal fade" id="addRule" tabindex="-1" aria-labelledby="addRuleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addRuleLabel">Add Rule</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addRuleForm" method="POST" action="../Admin/adminBackend/add_rule.php"
                    enctype="multipart/form-data">


                    <div class="mb-3">
                        <label class="form-label package-label">Hotel Name</label>
                        <input type="text" name="hotel_name" class="form-control package-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label package-label">Title</label>
                        <input type="text" name="title" class="form-control package-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label package-label">Display Order</label>
                        <input type="text" name="display_order" class="form-control package-input" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="mb-3">
                        <label class="form-label package-label">Rule</label>
                        <textarea name="rule_text" class="form-control package-input" rows="4" required></textarea>
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
            const statusCell = this.closest('tr').querySelector('td:nth-child(5)');

            fetch('../Admin/adminBackend/termscond_status_update.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_active == 1) {
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

    function showSuccessModal(message) {
        const overlay = document.createElement('div');
        overlay.className = 'cea-modal-overlay';
        overlay.innerHTML = `
        <div class="cea-modal-dialog">
            <div class="cea-modal-body">
                <div class="cea-modal-icon-wrapper">
                    <svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="cea-modal-heading">Success</div>
                <div class="cea-modal-text">${message}</div>
                <div class="cea-modal-actions">
                    <button class="cea-modal-button cea-modal-button-primary" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    `;
        document.body.appendChild(overlay);

        document.getElementById('successOkBtn').addEventListener('click', function () {
            location.reload();
        });
    }

    document.getElementById('addRuleForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = this;

        CasaEstelaModal.confirm('Add Offer', 'Are you sure you want to add this offer?', () => {
            const formData = new FormData(form);
            fetch(form.action, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showSuccessModal(data.message);
                        form.reset();
                        form.closest('.modal').querySelector('.btn-close').click(); // close add modal
                    } else {
                        CasaEstelaAlert.show('error', 'Error', data.message, 5000);
                    }
                })
                .catch(err => CasaEstelaAlert.show('error', 'Error', err, 5000));
        });
    });

    document.querySelectorAll('form[action^="../Admin/adminBackend/edit_rule.php"]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const f = this;

            CasaEstelaModal.confirm('Edit Offer', 'Are you sure you want to save changes?', () => {
                const formData = new FormData(f);
                fetch(f.action, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showSuccessModal(data.message);
                            f.closest('.modal').querySelector('.btn-close').click(); // close edit modal
                        } else {
                            CasaEstelaAlert.show('error', 'Error', data.message, 5000);
                        }
                    })
                    .catch(err => CasaEstelaAlert.show('error', 'Error', err, 5000));
            });
        });
    });


    document.querySelectorAll('.offer-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const row = this.closest('tr');

            CasaEstelaModal.confirm(
                'Delete Rule',
                'Are you sure you want to delete this rule? This action cannot be undone.',
                () => {
                    fetch('../Admin/adminBackend/delete_rule.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + id
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                row.remove();
                                CasaEstelaAlert.show('success', 'Deleted', data.message, 4000);
                            } else {
                                CasaEstelaAlert.show('error', 'Error', data.message, 5000);
                            }
                        })
                        .catch(err => {
                            CasaEstelaAlert.show('error', 'Error', err, 5000);
                        });
                }
            );
        });
    });
</script>


<script>
    // ---------------- CASA ESTELA ALERT SYSTEM ----------------
    const CasaEstelaAlert = {
        show: function (type, title, message, duration = 5000) {
            const icons = {
                success: '<svg class="cea-icon-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="cea-icon-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                warning: '<svg class="cea-icon-warning" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="cea-icon-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            const alert = document.createElement('div');
            alert.className = `cea-inline-alert cea-inline-alert-${type}`;
            alert.innerHTML = `
                <div class="cea-inline-alert-icon">${icons[type]}</div>
                <div class="cea-inline-alert-content">
                    <div class="cea-inline-alert-title">${title}</div>
                    <div class="cea-inline-alert-message">${message}</div>
                </div>
                <button class="cea-inline-alert-close" onclick="this.parentElement.classList.add('cea-inline-alert-closing'); setTimeout(() => this.parentElement.remove(), 300)">×</button>
            `;

            document.body.appendChild(alert);

            if (duration > 0) {
                setTimeout(() => {
                    alert.classList.add('cea-inline-alert-closing');
                    setTimeout(() => alert.remove(), 300);
                }, duration);
            }
        }
    };

    // ---------------- CASA ESTELA MODAL SYSTEM ----------------
    const CasaEstelaModal = {
        confirm: function (title, message, onConfirm, onCancel = null) {
            const overlay = document.createElement('div');
            overlay.className = 'cea-modal-overlay';
            overlay.innerHTML = `
                <div class="cea-modal-dialog cea-modal-confirm">
                    <div class="cea-modal-body">
                        <div class="cea-modal-icon-wrapper">
                            <svg class="cea-icon-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="cea-modal-heading">${title}</div>
                        <div class="cea-modal-text">${message}</div>
                        <div class="cea-modal-actions">
                            <button class="cea-modal-button cea-modal-button-secondary" onclick="CasaEstelaModal.handleCancel(this)">Cancel</button>
                            <button class="cea-modal-button cea-modal-button-primary" onclick="CasaEstelaModal.handleConfirm(this)">Confirm</button>
                        </div>
                    </div>
                </div>
            `;
            overlay.querySelector('.cea-modal-button-primary').ceConfirmCallback = onConfirm;
            overlay.querySelector('.cea-modal-button-secondary').ceCancelCallback = onCancel;
            document.body.appendChild(overlay);
        },

        handleConfirm: function (btn) {
            if (btn.ceConfirmCallback && typeof btn.ceConfirmCallback === 'function') {
                btn.ceConfirmCallback();
            }
            this.close(btn);
        },

        handleCancel: function (btn) {
            if (btn.ceCancelCallback && typeof btn.ceCancelCallback === 'function') {
                btn.ceCancelCallback();
            }
            this.close(btn);
        },

        close: function (element) {
            const overlay = element.closest ? element.closest('.cea-modal-overlay') : element;
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 200);
            }
        }
    };
</script>