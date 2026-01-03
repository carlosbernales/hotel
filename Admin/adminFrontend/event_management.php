<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$query = "SELECT * FROM event_packages";
$result = $conn->query($query);
?>
<link rel="stylesheet" href="../Admin/adminFrontend/css/event_management.css">


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

                            <!-- <p class="is-available-row">
                                <span class="detail-label">Availability:</span>
                                <span class="is-available-status-text <?php echo $is_available_class; ?>">
                                    <?php echo $is_available_status; ?>
                                </span>
                            </p> -->

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
                                            <div class="col-md-4">
                                               <label class="package-label">Duration (Hrs)</label>
                                                <input type="text" name="duration" value="<?php echo $row['duration']; ?>"
                                                    class="form-control package-input">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="package-label">Status</label>
                                                <select name="status" class="form-select package-input">
                                                    <option value="available" <?php echo ($row['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                                                    <option value="unavailable" <?php echo ($row['status'] == 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                               <label class="package-label">Place</label>
                                                <select name="place" class="form-select package-input">
                                                    <option value="cafe" <?php echo ($row['place'] == 'cafe') ? 'selected' : ''; ?>>Cafe</option>
                                                    <option value="garden" <?php echo ($row['place'] == 'garden') ? 'selected' : ''; ?>>Garden</option>
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
                        <div class="col-md-4">
                            <label class="package-label">Duration (Hrs)</label>
                            <input type="text" name="duration" class="form-control package-input">
                        </div>
                        <div class="col-md-4">
                            <label class="package-label">Status</label>
                            <select name="status" class="form-select package-input">
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="package-label">Place</label>
                            <select name="status" class="form-select package-input">
                                <option value="cafe">Cafe</option>
                                <option value="garden">Garden</option>
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