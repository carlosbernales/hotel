<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$table_type = [];
$result = $conn->query("SELECT * FROM table_packages");
if ($result) {
    $table_type = $result->fetch_all(MYSQLI_ASSOC);
}

$query = "SELECT * FROM event_packages";
$result = $conn->query($query);
?>

<style>
    /* Package Modal Styling */
    .package-modal {
        border: none;
        border-radius: 8px;
    }

    .package-modal-header {
        background-color: #C9A961;
        color: #2d2d2d;
        border-bottom: 2px solid #B8964F;
    }

    .package-modal-close {
        filter: brightness(0.3);
    }

    .package-modal-body {
        background-color: #f8f9fa;
        padding: 2rem;
    }

    .package-label {
        font-weight: 600;
        color: #2d2d2d;
    }

    .package-input {
        border: 1px solid #C9A961;
        border-radius: 4px;
        padding: 0.6rem;
    }

    .package-input:focus {
        border-color: #B8964F;
        box-shadow: 0 0 0 0.2rem rgba(201, 169, 97, 0.25);
    }

    .package-help-text {
        color: #6c757d;
    }

    .package-btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
    }

    .package-btn-cancel:hover {
        background-color: #5a6268;
        color: white;
    }

    .package-btn-save {
        background-color: #28a745;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .package-btn-save:hover {
        background-color: #218838;
        color: white;
    }
</style>
<style>
    :root {
        --primary-gold: #D4AF37;
        --text-dark: #333333;
        --text-light: #666666;
        --background-white: #ffffff;
        --border-light: #eeeeee;
    }

    /* --- General Layout and Components --- */
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

    .btn-primary-custom {
        background-color: var(--primary-gold);
        color: var(--background-white);
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9em;
        transition: background-color 0.2s;
    }

    .btn-primary-custom:hover {
        background-color: #C39D30;
    }

    /* --- General Card Styling (info-card) --- */
    .info-card {
        background-color: var(--background-white);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .info-card h5 {
        color: var(--text-dark);
    }

    /* --- Table Type Grid (Container) --- */
    .table-type-grid {
        display: grid;
        /* 3 columns on typical desktop, adapting for smaller screens */
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    /* --- Individual Table Type Card --- */
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

    /* --- Main Status Badge (Occupied/Available) --- */
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
        background-color: #28a745;
        /* Green for main 'Available' status */
    }

    .table-status-badge.occupied {
        background-color: #ffc107;
        /* Yellow/Orange for 'Occupied' status */
        color: var(--text-dark);
    }

    /* --- Card Actions --- */
    .table-actions {
        position: absolute;
        top: 10px;
        right: 10px;
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

    /* --- Card Body and Content --- */
    .table-card-body {
        padding: 15px;
    }

    .table-name {
        color: var(--text-dark);
        margin-bottom: 5px;
        font-size: 1.2em;
    }

    .table-price {
        color: var(--primary-gold);
        font-size: 1.1em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    /* --- New Details Grid (Duration, Max Pax, Time Limit, Max Guests) --- */
    .table-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        /* Two columns for details */
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

    /* --- Is Available Row (0/1 Status) --- */
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
        /* Prevent wrapping */
    }

    .is-available-status-text.available {
        color: #28a745;
        /* Dark Green text */
        background-color: #e5f4e7;
        /* Light Green background */
    }

    .is-available-status-text.unavailable {
        color: #dc3545;
        /* Dark Red text */
        background-color: #fce8e8;
        /* Light Red background */
    }

    /* --- Description and Notes --- */
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

    /* Utility Classes (Retained) */
    .text-success {
        color: #28a745;
    }

    .text-danger {
        color: #dc3545;
    }
</style>

<style>
    /* Add Table Button */
    .table-add-btn {
        background-color: #C9A961;
        color: #2d2d2d;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
    }

    .table-add-btn:hover {
        background-color: #B8964F;
        color: #2d2d2d;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .table-add-btn i {
        font-size: 1.1rem;
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
                    // Image paths setup (same as original)
                    $img1 = "../Admin/adminBackend/event_packages_images/" . $row['image_path'];
                    $img2 = "../Admin/adminBackend/event_packages_images/" . $row['image_path2'];
                    $img3 = "../Admin/adminBackend/event_packages_images/" . $row['image_path3'];

                    // Logic for is_available badge
                    // Assuming 'is_available' is a column with 0/1, and 'status' is a column with 'Occupied'/'Available'
                    $is_available_status = (intval($row['is_available']) > 0) ? 'Available' : 'Not Available';
                    $is_available_class = (intval($row['is_available']) > 0) ? 'available' : 'unavailable';

                    // Logic for main status badge (Occupied/Available)
                    $main_status_text = $row['status']; // Assuming this already holds 'Occupied' or 'Available'
                    $main_status_class = (strtolower($main_status_text) === 'available') ? 'available' : 'occupied';

                    // Format Duration and Time Limit for display (assuming they are in minutes or similar format)
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

                                <i class="fas fa-trash-alt"></i>
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


                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1"
    aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">

            <form method="POST" enctype="multipart/form-data"
                action="../Admin/adminBackend/edit_table_package.php?id=<?php echo $row['id']; ?>">

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
                            <input type="text" name="duration" class="form-control package-input"
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
                        <input type="file" name="image" class="form-control package-input">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn package-btn-save">Save Changes</button>
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


<div class="modal fade" id="addEvent" tabindex="-1" aria-labelledby="addEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addEventLabel">Add Package</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addPackageForm" method="POST" action="../Admin/adminBackend/event_package_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="name" class="form-label package-label">Package Name</label>
                        <input type="text" class="form-control package-input" id="name" name="name" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="price" class="form-label package-label">Price</label>
                            <input type="number" class="form-control package-input" id="price" name="price" step="0.01"
                                min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label for="max_guests" class="form-label package-label">Max Guests</label>
                            <input type="number" class="form-control package-input" id="max_guests" name="max_guests"
                                min="1" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="duration" class="form-label package-label">Duration (hours)</label>
                            <input type="number" class="form-control package-input" id="duration" name="duration"
                                min="1" required>
                        </div>

                        <div class="col-md-6">
                            <label for="time_limit" class="form-label package-label">Time Limit </label>
                            <input type="text" class="form-control package-input" id="time_limit" name="time_limit">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="max_pax" class="form-label package-label">Max Pax</label>
                            <input type="number" class="form-control package-input" id="max_pax" name="max_pax" min="1">
                        </div>

                        <div class="col-md-6">
                            <label for="is_available" class="form-label package-label">Available</label>
                            <select class="form-select package-input" id="is_available" name="is_available" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label package-label">Notes</label>
                        <textarea class="form-control package-input" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label package-label">Description</label>
                        <textarea class="form-control package-input" id="description" name="description" rows="3"
                            required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label package-label">Images (Max 3)</label>
                        <input type="file" class="form-control package-input" id="image" name="image[]" multiple
                            accept="image/*" required>
                        <small class="form-text package-help-text">You can upload up to 3 images.</small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label package-label">Status</label>
                        <select class="form-select package-input" id="status" name="status" required>
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                        </select>

                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">Save Package</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>





<?php include 'adminFrontend/footer.php'; ?>