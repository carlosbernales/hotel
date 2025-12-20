<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$query = "SELECT * FROM event_packages";
$result = $conn->query($query);
?>

<style>
    :root {
        --primary-gold: #C9A961;
        --secondary-gold: #B8964F;
        --dark-gold: #A67C3F;
        --text-dark: #2d2d2d;
        --text-light: #666666;
        --text-muted: #858796;
        --background-white: #ffffff;
        --background-light: #f8f9fa;
        --border-light: #eeeeee;
        --border-color: #e3e6f0;
        --success-green: #28a745;
        --success-light: #e5f4e7;
        --danger-red: #dc3545;
        --danger-light: #fce8e8;
        --warning-yellow: #ffc107;
    }

    /* Breadcrumb and Header */
    .breadcrumb-custom {
        background-color: var(--background-white);
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        font-size: 1.1em;
        font-weight: 500;
        color: var(--text-dark);
    }

    .breadcrumb-custom i {
        color: var(--primary-gold);
        margin-right: 8px;
    }

    .table-add-btn {
        background-color: var(--primary-gold);
        color: var(--text-dark);
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
    }

    .table-add-btn:hover {
        background-color: var(--secondary-gold);
        color: var(--text-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .table-add-btn i {
        font-size: 1.1rem;
    }

    /* Info Card */
    .info-card {
        background-color: var(--background-white);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .info-card h5 {
        color: var(--text-dark);
    }

    /* Table Cards Grid */
    .table-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .table-type-card {
        border: 1px solid var(--border-light);
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.3s;
        position: relative;
        background-color: var(--background-white);
    }

    .table-type-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table-card-header {
        position: relative;
    }

    .table-type-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .table-status-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8em;
        font-weight: bold;
        color: var(--background-white);
        z-index: 10;
    }

    .table-status-badge.available {
        background-color: var(--success-green);
    }

    .table-status-badge.occupied {
        background-color: var(--warning-yellow);
        color: var(--text-dark);
    }

    .table-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }

    .table-actions i {
        background-color: rgba(0, 0, 0, 0.6);
        color: var(--background-white);
        padding: 8px;
        border-radius: 50%;
        cursor: pointer;
        margin-left: 5px;
        transition: background-color 0.2s;
    }

    .table-actions i:hover {
        background-color: var(--primary-gold);
    }

    .table-card-body {
        padding: 15px;
    }

    .table-name {
        color: var(--text-dark);
        margin-bottom: 5px;
        font-size: 1.2em;
        font-weight: 600;
    }

    .table-price {
        color: var(--primary-gold);
        font-size: 1.1em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .table-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 10px;
        font-size: 0.9em;
        padding: 10px 0;
        margin-bottom: 10px;
        border-top: 1px dashed var(--border-light);
        border-bottom: 1px dashed var(--border-light);
    }

    .detail-item {
        margin: 0;
        color: var(--text-light);
    }

    .detail-label {
        font-weight: bold;
        color: var(--text-dark);
        margin-right: 5px;
    }

    .is-available-row {
        font-size: 0.9em;
        margin-bottom: 10px;
        color: var(--text-light);
    }

    .is-available-status-text {
        font-weight: bold;
        padding: 2px 5px;
        border-radius: 3px;
        white-space: nowrap;
    }

    .is-available-status-text.available {
        color: var(--success-green);
        background-color: var(--success-light);
    }

    .is-available-status-text.unavailable {
        color: var(--danger-red);
        background-color: var(--danger-light);
    }

    .table-description-area {
        padding-bottom: 10px;
    }

    .table-description {
        font-size: 0.9em;
        color: var(--text-light);
        line-height: 1.4;
        margin-bottom: 0;
    }

    .table-notes {
        margin-top: 10px;
        border-top: 1px dashed var(--border-light);
        padding-top: 10px;
        font-style: italic;
        font-size: 0.85em;
        color: var(--text-light);
    }

    /* Modal Styling */
    .package-modal .modal-content {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.15);
    }

    .package-modal-header {
        background-color: var(--primary-gold);
        color: var(--text-dark);
        border-bottom: 2px solid var(--secondary-gold);
        padding: 1.5rem;
    }

    .package-modal-header .modal-title {
        color: var(--text-dark);
        font-weight: 600;
    }

    .package-modal-close,
    .btn-close-white {
        filter: brightness(0.3);
    }

    .package-modal-body {
        background-color: var(--background-light);
        padding: 2rem;
    }

    .package-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .package-input {
        border: 1px solid var(--primary-gold);
        border-radius: 4px;
        padding: 0.6rem;
        transition: all 0.2s ease;
    }

    .package-input:focus {
        border-color: var(--secondary-gold);
        box-shadow: 0 0 0 0.2rem rgba(201, 169, 97, 0.25);
        outline: none;
    }

    .menu-item-card {
        background: var(--background-white);
        border-left: 4px solid var(--primary-gold);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .package-help-text {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-style: italic;
    }

    .package-btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        border: none;
        transition: background-color 0.2s;
    }

    .package-btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }

    .package-btn-save,
    .btn-save {
        background-color: var(--success-green);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
        border: none;
        transition: all 0.2s ease;
    }

    .package-btn-save:hover,
    .btn-save:hover {
        background-color: #218838;
        color: white;
        transform: translateY(-1px);
    }

    /* Image Upload Area */
    .border-dashed {
        border: 2px dashed var(--border-color);
        transition: border-color 0.3s;
    }

    .border-dashed:hover {
        border-color: var(--primary-gold);
    }

    .image-preview-slot {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 4px;
        border: 2px solid var(--border-light);
    }

    /* Utility Classes */
    .text-success {
        color: var(--success-green);
    }

    .text-danger {
        color: var(--danger-red);
    }

    .animate__fadeIn {
        animation: fadeIn 0.4s ease-in-out;
    }
</style>

<div class="main-content" id="mainContent">
    <div class="table-management-container">
        <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-home"></i>
                <span>Table Management</span>
            </div>
            <a class="btn table-add-btn" data-bs-toggle="modal" data-bs-target="#addEvent">
                Add Table
            </a>
        </div>

        <div class="info-card" style="margin-bottom: 40px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Table Types</h5>
            </div>

            <div class="table-type-grid">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $img1 = "../Admin/adminBackend/event_packages_images/" . $row['image_path'];
                    $img2 = "../Admin/adminBackend/event_packages_images/" . $row['image_path2'];
                    $img3 = "../Admin/adminBackend/event_packages_images/" . $row['image_path3'];

                    $is_available_status = (intval($row['is_available']) > 0) ? 'Available' : 'Not Available';
                    $is_available_class = (intval($row['is_available']) > 0) ? 'available' : 'unavailable';

                    $main_status_text = $row['status'];
                    $main_status_class = (strtolower($main_status_text) === 'available') ? 'available' : 'occupied';

                    $duration_display = !empty($row['duration']) ? $row['duration'] . '' : 'N/A';
                    $time_limit_display = !empty($row['time_limit']) ? $row['time_limit'] . '' : 'N/A';
                    ?>

                    <div class="table-type-card">
                        <div id="carousel-<?php echo $row['id']; ?>" class="carousel slide table-card-header"
                            data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="<?php echo $img1; ?>" class="table-type-img" alt="Image 1">
                                </div>

                                <?php if (!empty($row['image_path2'])): ?>
                                    <div class="carousel-item">
                                        <img src="<?php echo $img2; ?>" class="table-type-img" alt="Image 2">
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($row['image_path3'])): ?>
                                    <div class="carousel-item">
                                        <img src="<?php echo $img3; ?>" class="table-type-img" alt="Image 3">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button class="carousel-control-prev" type="button"
                                data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>

                            <button class="carousel-control-next" type="button"
                                data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>

                            <div class="table-status-badge <?php echo $main_status_class; ?>">
                                <?php echo $main_status_text; ?>
                            </div>

                            <div class="table-actions">
                                <i class="fas fa-edit" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $row['id']; ?>"></i>
                                <a href="../Admin/adminBackend/event_management_delete.php?id=<?php echo $row['id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this event?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="table-card-body">
                            <h6 class="table-name"><?php echo $row['name']; ?></h6>
                            <p class="table-price">₱<?php echo number_format($row['price'], 2); ?></p>

                            <div class="table-details-grid mb-3">
                                <p class="detail-item">
                                    <span class="detail-label">Duration:</span> <?php echo $duration_display; ?>
                                </p>
                                <p class="detail-item">
                                    <span class="detail-label">Max Pax:</span> <?php echo $row['max_pax']; ?>
                                </p>
                                <p class="detail-item">
                                    <span class="detail-label">Time Limit:</span> <?php echo $time_limit_display; ?>
                                </p>
                                <p class="detail-item">
                                    <span class="detail-label">Max Guests:</span> <?php echo $row['max_guests']; ?>
                                </p>
                            </div>

                            <p class="is-available-row">
                                <span class="detail-label">Availability:</span>
                                <span class="is-available-status-text <?php echo $is_available_class; ?>">
                                    <?php echo $is_available_status; ?>
                                </span>
                            </p>

                            <div class="table-description-area">
                                <p class="table-description">
                                    <span class="detail-label">Description: </span>
                                    <?php echo $row['description']; ?>
                                </p>
                            </div>

                            <p class="table-notes">
                                <span class="detail-label">Notes:</span> <?php echo $row['notes']; ?>
                            </p>
                        </div>
                    </div>

                    <?php
                    $existingMenuItems = [];
                    if (!empty($row['menu_items'])) {
                        $existingMenuItems = array_map('trim', explode(',', $row['menu_items']));
                    }
                    $menuItemsCount = count($existingMenuItems);
                    ?>

                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content package-modal">
                                <form method="POST" enctype="multipart/form-data"
                                    action="../Admin/adminBackend/event_management_edit.php?id=<?php echo $row['id']; ?>">
                                    <div class="modal-header package-modal-header">
                                        <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Update:
                                            <?php echo htmlspecialchars($row['name']); ?>
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body package-modal-body">
                                        <div class="mb-3">
                                            <label class="package-label">Package Name</label>
                                            <input type="text" name="name" class="form-control package-input"
                                                placeholder="e.g., Grand Wedding Package"
                                                value="<?php echo $row['name']; ?>" required>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="package-label">Price (₱)</label>
                                                <input type="number" step="0.01" name="price"
                                                    class="form-control package-input" value="<?php echo $row['price']; ?>"
                                                    required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="package-label">Max Guests</label>
                                                <input type="number" name="max_guests"
                                                    value="<?php echo $row['max_guests']; ?>"
                                                    class="form-control package-input" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="package-label">Max Pax</label>
                                                <input type="number" name="max_pax" value="<?php echo $row['max_pax']; ?>"
                                                    class="form-control package-input">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="package-label">Duration (Hrs)</label>
                                                <input type="text" name="duration" value="<?php echo $row['duration']; ?>"
                                                    class="form-control package-input">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="package-label">Status</label>
                                                <select name="status" class="form-select package-input">
                                                    <option value="available" <?php echo ($row['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                                    <option value="unavailable" <?php echo ($row['status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="package-label">Description</label>
                                            <textarea name="description" class="form-control package-input" rows="2"
                                                required><?php echo $row['description']; ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="package-label">Notes</label>
                                            <textarea name="notes" class="form-control package-input" rows="2"
                                                required> <?php echo $row['notes']; ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label package-label">Images (up to 3)</label>
                                            <input type="file" name="images[]" class="form-control package-input"
                                                accept="image/*" multiple>
                                        </div>

                                        <div class="p-3 bg-white rounded border">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="fw-bold mb-0">Manage Menu</h6>
                                                <input type="number" class="form-control form-control-sm menu-counter"
                                                    data-target="menuItemsContainer-<?php echo $row['id']; ?>"
                                                    style="width: 70px;" value="<?php echo count($existingMenuItems); ?>"
                                                    min="0">
                                            </div>
                                            <div id="menuItemsContainer-<?php echo $row['id']; ?>">
                                                <?php foreach ($existingMenuItems as $index => $item): ?>
                                                    <div class="menu-item-card">
                                                        <label class="package-label">Item <?php echo $index + 1; ?></label>
                                                        <input type="text" name="menu_items[]"
                                                            class="form-control package-input"
                                                            value="<?php echo htmlspecialchars($item); ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="submit" class="btn btn-save">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEvent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <form id="addPackageForm" method="POST" action="../Admin/adminBackend/event_package_add.php"
                enctype="multipart/form-data">
                <div class="modal-header package-modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>New Event Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body package-modal-body">
                    <div class="mb-3">
                        <label class="package-label">Package Name</label>
                        <input type="text" name="name" class="form-control package-input"
                            placeholder="e.g., Grand Wedding Package" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="package-label">Price (₱)</label>
                            <input type="number" step="0.01" name="price" class="form-control package-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="package-label">Max Guests</label>
                            <input type="number" name="max_guests" class="form-control package-input" required>
                        </div>
                        <div class="col-md-4">
                            <label class="package-label">Max Pax</label>
                            <input type="number" name="max_pax" class="form-control package-input">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="package-label">Duration (Hrs)</label>
                            <input type="text" name="duration" class="form-control package-input">
                        </div>
                        <div class="col-md-6">
                            <label class="package-label">Status</label>
                            <select name="status" class="form-select package-input">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="package-label">Description</label>
                        <textarea name="description" class="form-control package-input" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="package-label">Notes</label>
                        <textarea name="notes" class="form-control package-input" rows="2" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="package-label">Upload Images (Max 3)</label>
                        <input type="file" name="images[]" class="form-control package-input" multiple accept="image/*"
                            onchange="previewImages(this, 'addPreview')">
                        <div id="addPreview" class="d-flex gap-2 mt-2"></div>
                    </div>

                    <div class="p-3 bg-white rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Menu Items</h6>
                            <div class="d-flex align-items-center">
                                <span class="me-2 small fw-bold text-muted">Items:</span>
                                <input type="number" id="menuCountAdd" class="form-control form-control-sm"
                                    style="width: 70px;" min="0" value="0">
                            </div>
                        </div>
                        <div id="menuItemsContainerAdd"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn package-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-save">Create Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'adminFrontend/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuCountAdd = document.getElementById('menuCountAdd');
        const menuContainerAdd = document.getElementById('menuItemsContainerAdd');

        if (menuCountAdd) {
            menuCountAdd.addEventListener('input', () => {
                updateMenuInputs(menuCountAdd.value, menuContainerAdd);
            });
        }

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('menu-counter')) {
                const containerId = e.target.getAttribute('data-target');
                const container = document.getElementById(containerId);
                updateMenuInputs(e.target.value, container);
            }
        });

        function updateMenuInputs(count, container) {
            const currentInputs = Array.from(container.querySelectorAll('input'));
            const existingValues = currentInputs.map(input => input.value);

            // Count non-empty inputs to enforce minimum
            const minCount = existingValues.filter(val => val.trim() !== '').length;

            if (parseInt(count) < minCount) {
                alert(`You cannot reduce menu items below ${minCount} because some inputs are not empty.`);
                count = minCount;
            }

            count = parseInt(count);

            while (currentInputs.length < count) {
                const index = currentInputs.length;
                const div = document.createElement('div');
                div.className = 'menu-item-card';
                div.innerHTML = `
            <label class="package-label">Item ${index + 1}</label>
            <input type="text" name="menu_items[]" class="form-control package-input" placeholder="Enter menu item name..." required>
        `;
                container.appendChild(div);
                currentInputs.push(div.querySelector('input'));
            }

            while (currentInputs.length > count) {
                const lastInput = currentInputs[currentInputs.length - 1];
                if (lastInput.value.trim() !== '') break; // stop if last input has value
                lastInput.parentElement.remove();
                currentInputs.pop();
            }

            currentInputs.forEach((input, i) => {
                input.previousElementSibling.textContent = `Item ${i + 1}`;
            });
        }

    });

    function previewImages(input, targetId) {
        const preview = document.getElementById(targetId);
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview-slot';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>