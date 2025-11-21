<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

////////////////////////
$menu_category = [];
$result = $conn->query("SELECT * FROM menu_categories");
if ($result) {
    $menu_category = $result->fetch_all(MYSQLI_ASSOC);
}

////////////////////////
$menu_item = [];
$result = $conn->query("
    SELECT mi.*, mc.display_name 
    FROM menu_items mi 
    JOIN menu_categories mc ON mi.category_id = mc.id
");
if ($result) {
    $menu_item = $result->fetch_all(MYSQLI_ASSOC);
}

////////////////////////
$addonsResult = $conn->query("SELECT * FROM menu_items_addons");
$addons = [];

while ($row = $addonsResult->fetch_assoc()) {
    $addons[$row['menu_item_id']][] = $row;
}
?>

<style>
    :root {
        --gold: #D4AF37;
        --dark-content: #2c2c2c;
        --light-bg: #f8f9fa;
        --card-bg: white;
        --text-muted: #666;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--light-bg);
        color: var(--dark-content);
    }

    /* Breadcrumb Styling */
    .breadcrumb-custom {
        background: var(--card-bg);
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .breadcrumb-custom i {
        color: var(--gold);
        margin-right: 8px;
    }

    .breadcrumb-custom span {
        color: var(--dark-content);
        font-weight: 500;
    }

    .table-add-btn {
        /* Base styling for the add buttons in the breadcrumb */
        font-size: 14px;
        font-weight: 600;
    }

    /* Category Tabs/Filter Styling */
    .category-tabs {
        background: var(--card-bg);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .category-tabs h5 {
        color: var(--dark-content);
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }

    .category-tabs h5 i {
        color: var(--gold);
        margin-right: 10px;
    }

    .category-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .category-btn {
        padding: 10px 24px;
        border: 2px solid var(--gold);
        background: var(--card-bg);
        color: var(--gold);
        border-radius: 25px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
        /* Prevent button text wrapping */
    }

    .category-btn:hover {
        background: rgba(212, 175, 55, 0.1);
        transform: translateY(-2px);
    }

    .category-btn.active {
        background: var(--gold);
        color: #fff;
        /* Changed to white for better contrast on gold */
    }

    /* Products Grid Layout */
    .products-grid {
        display: grid;
        /* Adjusted minmax for slightly smaller cards on large screens */
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    /* Product Card Styling */
    .product-card {
        background: var(--card-bg);
        border-radius: 8px;
        padding: 0;
        /* Removed padding to let image fill top */
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        display: none;
        /* Controlled by JS show class */
    }

    .product-card.show {
        display: block;
        animation: fadeIn 0.4s ease;
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

    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }

    /* Image Styling */
    .product-image-container {
        width: 100%;
        height: 200px;
        /* Fixed height for consistency */
        overflow: hidden;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
        /* Slight zoom on hover */
    }

    /* Content Area Padding */
    .product-content {
        padding: 20px;
    }

    /* Header (Name & Price) */
    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark-content);
        margin-bottom: 8px;
    }

    .product-price {
        font-size: 24px;
        font-weight: 700;
        color: var(--gold);
    }

    /* Description Styling */
    .product-description {
        color: var(--text-muted);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        max-height: 60px;
        /* Limit description height */
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        /* Limit to 3 lines */
        -webkit-box-orient: vertical;
    }

    /* Footer (Status & Actions) */
    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #eee;
        padding: 15px 20px 20px 20px;
        /* Match content padding */
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.available {
        background: #d4edda;
        /* Light Green */
        color: #155724;
        /* Dark Green */
    }

    .status-badge.unavailable {
        background: #f8d7da;
        /* Light Red */
        color: #721c24;
        /* Dark Red */
    }

    /* Action Buttons */
    .product-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .edit-btn {
        background: #e3f2fd;
        /* Light Blue */
        color: #1976d2;
        /* Medium Blue */
    }

    .edit-btn:hover {
        background: #1976d2;
        color: #fff;
    }

    .delete-btn {
        background: #ffebee;
        /* Light Red */
        color: #c62828;
        /* Dark Red */
    }

    .delete-btn:hover {
        background: #c62828;
        color: #fff;
    }

    .view-btn {
        background: #f3e5f5;
        /* Light Purple */
        color: #7b1fa2;
        /* Dark Purple */
    }

    .view-btn:hover {
        background: #7b1fa2;
        color: #fff;
    }

    /* No Items Message */
    .no-items {
        text-align: center;
        padding: 60px 20px;
        color: #999;
        grid-column: 1 / -1;
        /* Span across all columns in the grid */
    }

    .no-items i {
        font-size: 64px;
        margin-bottom: 20px;
        color: #ddd;
    }

    .no-items h3 {
        color: #999;
        font-weight: 400;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: 1fr;
        }

        .category-buttons {
            justify-content: center;
        }

        .breadcrumb-custom {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .breadcrumb-custom>div:last-child {
            margin-top: 10px;
            width: 100%;
            display: flex;
            justify-content: space-around;
        }
    }

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

    /* Adds On modal */

    :root {
        --theme-gold: #c69c3a;
        --theme-dark: #343a40;
    }

    .modal-header.theme-bg-dark {
        background-color: var(--theme-dark);
        border-bottom: 1px solid var(--theme-gold);
    }

    .text-theme-gold {
        color: var(--theme-gold) !important;
    }

    .delete-btn {
        transition: background-color 0.2s;
    }

    .addon-item:hover {
        background-color: #f8f9fa;
        border-color: var(--theme-gold) !important;
    }

    .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%);
        opacity: 0.8;
    }

    .btn-close-white:hover {
        opacity: 1;
    }

    .package-modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-home"></i>
            <span class="ms-2">Cafe Management</span>
        </div>
        <div class="d-flex gap-2"> <a class="btn table-add-btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#addCafeCategory">
                Add Category
            </a>
            <a class="btn table-add-btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                Add Menu Item
            </a>
            <a class="btn table-add-btn btn-sm btn-primary" data-bs-toggle="modal"
                data-bs-target="#categoryManageModal">
                Categories
            </a>
        </div>
    </div>

    <div class="category-tabs">
        <h5><i class="fas fa-filter"></i> Filter by Category</h5>
        <div class="category-buttons">
            <button class="category-btn active" data-category="all">All Items</button>
            <?php foreach ($menu_category as $category): ?>
                <button class="category-btn" data-category="<?= htmlspecialchars($category['display_name']) ?>">
                    <?= htmlspecialchars($category['display_name']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="products-grid" id="productsGrid">
        <?php foreach ($menu_item as $item): ?>
            <div class="product-card show" data-category="<?= htmlspecialchars($item['display_name']) ?>">

                <div class="product-image-container">
                    <img src="../Admin/adminBackend/menu_item_images/<?= htmlspecialchars($item['image_path'] ?? 'default.jpg') ?>"
                        alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                </div>

                <div class="product-content">
                    <div class="product-header">
                        <div class="product-info">
                            <div class="product-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="product-price">₱<?= number_format($item['price'], 2) ?></div>
                        </div>
                    </div>

                    <div class="product-description"><?= htmlspecialchars($item['description']) ?></div>
                </div>

                <div class="product-footer">
                    <span class="status-badge <?= $item['availability'] != 0 ? 'available' : 'unavailable' ?>">
                        <?= $item['availability'] != 0 ? 'Available' : 'Unavailable' ?>
                    </span>

                    <div class="product-actions">

                        <button class="action-btn view-btn" data-bs-toggle="modal"
                            data-bs-target="#addsOnModal<?= $item['id'] ?>" title="View">
                            <i class="fas fa-eye"></i>
                        </button>

                        <button class="action-btn view-btn" data-bs-toggle="modal"
                            data-bs-target="#addModal<?= $item['id'] ?>" title="Add">
                            <i class="fas fa-plus"></i>
                        </button>

                        <button class="action-btn edit-btn" data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $item['id'] ?>" title="Edit Item">
                            <i class="fas fa-pencil-alt"></i>
                        </button>

                        <form method="POST" action="../Admin/adminBackend/menu_item_delete.php?id=<?= $item['id'] ?>">
                            <button type="submit" class="action-btn delete-btn"
                                onclick="return confirm('Are you sure you want to delete this room type?')"
                                title="Delete Item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="addsOnModal<?= $item['id'] ?>" tabindex="-1"
                aria-labelledby="addsOnModalLabel<?= $item['id'] ?>" aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header theme-bg-dark">
                            <h5 class="modal-title text-white" id="addsOnModalLabel<?= $item['id'] ?>">
                                <i class="fas fa-plus-circle me-2"></i> Manage Add-ons
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body package-modal-body">

                            <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Existing Add-ons</h6>

                            <?php if (!empty($addons[$item['id']])): ?>
                                <div class="add-ons-list">
                                    <?php foreach ($addons[$item['id']] as $ad): ?>
                                        <div
                                            class="d-flex justify-content-between align-items-center addon-item p-3 mb-2 rounded shadow-sm border">

                                            <div class="addon-details">
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($ad['name']) ?></div>
                                                <div class="text-theme-gold fw-bold">₱<?= number_format($ad['price'], 2) ?></div>
                                            </div>

                                            <form method="POST"
                                                action="../Admin/adminBackend/adds_on_delete.php?id=<?= $ad['id'] ?>"
                                                onsubmit="return confirm('Are you sure you want to delete this add-on: <?= htmlspecialchars($ad['name']) ?>?');">

                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="fas fa-info-circle me-1"></i> No add-ons have been added for this item yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Add Item Modal -->
            <div class="modal fade" id="addModal<?= $item['id'] ?>" tabindex="-1"
                aria-labelledby="addModalLabel<?= $item['id'] ?>" aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content package-modal">
                        <form method="POST" enctype="multipart/form-data"
                            action="../Admin/adminBackend/adds_on_add.php?id=<?php echo $item['id']; ?>">

                            <div class="modal-header package-modal-header">
                                <h5 class="modal-title fw-bold" id="addModalLabel<?= $item['id'] ?>">Adds On</h5>
                                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body package-modal-body">
                                <div class="mb-3">
                                    <label class="form-label package-label">Name</label>
                                    <input type="text" name="name" class="form-control package-input" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Price</label>
                                    <input type="number" step="0.01" name="price" class="form-control package-input"
                                        required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn package-btn-save">Insert</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Item Modal (your existing modal, adjusted for $item instead of $room_type) -->
            <div class="modal fade" id="editModal<?= $item['id'] ?>" tabindex="-1"
                aria-labelledby="editModalLabel<?= $item['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content package-modal">
                        <form method="POST" enctype="multipart/form-data"
                            action="../Admin/adminBackend/item_menu_edit.php?id=<?php echo $item['id']; ?>">

                            <div class="modal-header package-modal-header">
                                <h5 class="modal-title fw-bold" id="editModalLabel<?= $item['id'] ?>">Edit Item</h5>
                                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body package-modal-body">

                                <div class="mb-3">
                                    <select class="form-control package-input" id="categoryId" name="category_id" required>
                                        <option value="" disabled <?= empty($item['category_id']) ? 'selected' : '' ?>>Select
                                            a category</option>
                                        <?php foreach ($menu_category as $category): ?>
                                            <option value="<?= $category['id'] ?>" <?= ($item['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['display_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Name</label>
                                    <input type="text" name="name" class="form-control package-input"
                                        value="<?= htmlspecialchars($item['name']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Price</label>
                                    <input type="number" step="0.01" name="price" class="form-control package-input"
                                        value="<?= htmlspecialchars($item['price']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Description</label>
                                    <textarea name="description"
                                        class="form-control package-input"><?= htmlspecialchars($item['description']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Availability</label>
                                    <input type="number" step="0.01" name="availability" class="form-control package-input"
                                        value="<?= htmlspecialchars($item['availability']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label package-label">Image</label>
                                    <input type="file" name="image" class="form-control package-input">
                                    <small class="package-help-text">Upload new image to replace existing one.</small>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn package-btn-save">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

</div>



<div class="modal fade" id="addCafeCategory" tabindex="-1" aria-labelledby="addCafeCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addCafeCategoryLabel">Add Category</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addCategoryForm" method="POST" action="../Admin/adminBackend/menu_category_add.php">

                    <div class="mb-3">
                        <label for="display_name" class="form-label package-label">Category Name</label>
                        <input type="text" class="form-control package-input" id="display_name" name="display_name"
                            required>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Insert Category
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="categoryManageModal" tabindex="-1" aria-labelledby="categoryManageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header theme-bg-dark">
                <h5 class="modal-title text-white" id="categoryManageModalLabel">
                    <i class="fas fa-list me-2"></i> Manage Categories
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body package-modal-body">

                <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2">Existing Categories</h6>

                <?php if (!empty($menu_category)): ?>
                    <div class="categories-list">
                        <?php foreach ($menu_category as $cat): ?>
                            <div
                                class="d-flex justify-content-between align-items-center category-item p-3 mb-2 rounded shadow-sm border">

                                <div class="category-details">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($cat['display_name']) ?></div>
                                </div>

                                <div class="category-actions d-flex gap-2">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(<?= $cat['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Delete Form -->
                                    <form method="POST"
                                        action="../Admin/adminBackend/menu_category_delete.php?id=<?php echo $cat['id']; ?>"
                                        onsubmit="return confirm('Are you sure you want to delete this category: <?= htmlspecialchars($cat['display_name']) ?>?');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Category Modal -->
                            <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1"
                                aria-labelledby="editCategoryModalLabel<?= $cat['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content package-modal">

                                        <div class="modal-header theme-bg-dark">
                                            <h5 class="modal-title text-white" id="editCategoryModalLabel<?= $cat['id'] ?>">
                                                Edit Category
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body package-modal-body">
                                            <form method="POST"
                                                action="../Admin/adminBackend/menu_category_edit.php?id=<?php echo $cat['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="display_name_<?= $cat['id'] ?>"
                                                        class="form-label package-label">Category Name</label>
                                                    <input type="text" class="form-control package-input"
                                                        id="display_name_<?= $cat['id'] ?>" name="display_name"
                                                        value="<?= htmlspecialchars($cat['display_name']) ?>" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn package-btn-save">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle me-1"></i> No categories added yet.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content package-modal">
            <div class="modal-header package-modal-header">
                <h5 class="modal-title fw-bold" id="addMenuModalLabel">Add Room Number</h5>
                <button type="button" class="btn-close package-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body package-modal-body">
                <form id="addRoomForm" method="POST" action="../Admin/adminBackend/menu_items_add.php"
                    enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="categoryId" class="form-label package-label">Category</label>
                        <select class="form-control package-input" id="categoryId" name="category_id" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($menu_category as $category): ?>
                                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['display_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="name" class="form-label package-label">Name</label>
                        <input type="text" class="form-control package-input" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label package-label">Price</label>
                        <input type="number" step="0.01" class="form-control package-input" id="price" name="price"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="availability" class="form-label package-label">Availability</label>
                        <input type="text" class="form-control package-input" id="availability" name="availability"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label package-label">Description</label>
                        <textarea class="form-control package-input" id="description" name="description"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagePath" class="form-label package-label">Image</label>
                        <input type="file" class="form-control package-input" id="imagePath" name="image_path"
                            accept="image/*">
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn package-btn-save">
                                Save Room
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
    const categoryButtons = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    const productsGrid = document.getElementById('productsGrid');

    function updateNoItemsMessage() {
        const visibleCards = document.querySelectorAll('.product-card.show');
        let noItems = document.querySelector('.no-items');

        if (visibleCards.length === 0) {
            if (!noItems) {
                const div = document.createElement('div');
                div.className = 'no-items';
                div.innerHTML = '<i class="fas fa-coffee"></i><h3>No items found in this category</h3><p>Try selecting a different filter.</p>';
                productsGrid.appendChild(div);
            }
        } else {
            if (noItems) {
                noItems.remove();
            }
        }
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const category = button.dataset.category;

            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            productCards.forEach(card => card.classList.remove('show'));

            if (category === 'all') {
                productCards.forEach(card => card.classList.add('show'));
            } else {
                productCards.forEach(card => {
                    if (card.dataset.category === category) {
                        card.classList.add('show');
                    }
                });
            }

            updateNoItemsMessage();
        });
    });
    /////////////////////////////

    document.addEventListener('DOMContentLoaded', updateNoItemsMessage);

    function openEditModal(id) {
        const parentModal = new bootstrap.Modal(document.getElementById('categoryManageModal'));
        parentModal.hide();

        setTimeout(() => {
            const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal' + id));
            editModal.show();
        });
    }
</script>