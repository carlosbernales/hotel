<?php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
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

<link rel="stylesheet" href="../Admin/adminFrontend/css/cafe_management.css">

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
                                    <select name="availability" class="form-control package-input" required>
                                        <option value="1" <?= $item['availability'] == 1 ? 'selected' : '' ?>>
                                            Available
                                        </option>
                                        <option value="0" <?= $item['availability'] == 0 ? 'selected' : '' ?>>
                                            Unavailable
                                        </option>
                                    </select>
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
                        <select class="form-control package-input" id="availability" name="availability" required>
                            <option value="" disabled selected>Select availability</option>
                            <option value="1">Available</option>
                            <option value="0">Unavailable</option>
                        </select>
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