<?php
// Include database connection
require_once 'db.php';

// Get cashier information
$cashierName = 'Cashier';
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM userss WHERE user_type = 'cashier' LIMIT 1");
    $stmt->execute();
    $cashier = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cashier) {
        $cashierName = trim($cashier['first_name'] . ' ' . $cashier['last_name']);
    }
} catch (PDOException $e) {
    error_log("Error fetching cashier name: " . $e->getMessage());
    $cashierName = 'Cashier';
}
    
// Get processing orders count
$processingCount = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'processing'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $processingCount = $result['count'];
} catch (PDOException $e) {
    error_log("Error fetching processing orders count: " . $e->getMessage());
    $processingCount = 0;
}

// Get pending orders count
$pendingCount = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders_table WHERE status = 'pending'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pendingCount = $result['count'];
} catch (PDOException $e) {
    error_log("Error fetching pending orders count: " . $e->getMessage());
    $pendingCount = 0;
}

// Get occupied tables count
$tablesCount = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tables WHERE status = 'occupied'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $tablesCount = $result['count'];
} catch (PDOException $e) {
    error_log("Error fetching tables count: " . $e->getMessage());
    $tablesCount = 0;
}
?>
<!-- Modern Navigation Sidebar -->
<aside class="cashier-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <img src="img/logo.png" alt="Casa Estela Logo" class="brand-logo">
            </div>
            <div class="brand-text">
                <h4>E Akomoda</h4>
                <span>Cashier POS</span>
            </div>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
 
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="?page=pos" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'pos') || (!isset($_GET['page']) && (isset($_GET['pos']) || !isset($_GET['page']))) ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div class="nav-content">
                        <span class="nav-title">Point of Sale</span>
                        <span class="nav-subtitle"> Menus </span>
                    </div>
                </a>
            </li>
 
            <li class="nav-item">
                <a href="?page=pending_orders" class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] == 'pending_orders' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="nav-content">
                        <span class="nav-title">Pending Orders</span>
                        <span class="nav-subtitle">View pending</span>
                    </div>
                    <span class="nav-badge" id="pending-count"><?php echo $pendingCount; ?></span>
                </a>
            </li>
 
            <li class="nav-item">
                <a href="?page=ProcessingOrder" class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] == 'ProcessingOrder' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="nav-content">
                        <span class="nav-title">Processing</span>
                        <span class="nav-subtitle">Active orders</span>
                    </div>
                    <span class="nav-badge" id="processing-count"><?php echo $processingCount; ?></span>
                </a>
            </li>
 
            <li class="nav-item">
                <a href="?page=sales" class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] == 'sales' ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="nav-content">
                        <span class="nav-title">Sales Report</span>
                        <span class="nav-subtitle">View analytics</span>
                    </div>
                </a>
            </li>
        </ul>
 
    </nav>
 
    <div class="sidebar-footer">
        <div class="user-info-mini">
            <div class="user-avatar-mini">
                <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="user-details-mini">
                <div class="user-name-mini"><?php echo $_SESSION['fullname'] ?? 'Cashier'; ?></div>
                <div class="user-time-mini" id="current-time">Loading...</div>
            </div>
        </div>
 
        <div class="sidebar-actions">
            <button class="action-btn" onclick="toggleTheme()" title="Toggle Theme">
                <i class="fas fa-moon"></i>
            </button>
            <a href="../logout.php" class="action-btn danger" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>
 
<style>
/* Primary color variables */
:root {
    --primary-color: #b8860b;
    --primary-hover: #9a7209;
    --primary-light: rgba(184, 134, 11, 0.1);
    --primary-light-hover: rgba(184, 134, 11, 0.2);
}

/* Light theme variables */
:root {
    --bg-primary: #ffffff;
    --bg-secondary: #f8f9fa;
    --bg-tertiary: #e9ecef;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --text-muted: #adb5bd;
    --border-color: #dee2e6;
    --sidebar-bg: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    --sidebar-text: rgba(255,255,255,0.8);
    --sidebar-text-active: #ffffff;
    --card-bg: #ffffff;
    --card-shadow: 0 5px 15px rgba(0,0,0,0.08);
    --navbar-bg: #b8860b;
    --navbar-text: #ffffff;
}

/* Dark theme variables */
body.dark-theme {
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --bg-tertiary: #404040;
    --text-primary: #e9ecef;
    --text-secondary: #adb5bd;
    --text-muted: #6c757d;
    --border-color: #495057;
    --sidebar-bg: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
    --sidebar-text: rgba(255,255,255,0.7);
    --sidebar-text-active: #ffffff;
    --card-bg: #2d2d2d;
    --card-shadow: 0 5px 15px rgba(0,0,0,0.3);
    --navbar-bg: #8b6914;
    --navbar-text: #ffffff;
}

/* Modern Sidebar Styles */
.cashier-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: var(--sidebar-bg);
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 1000;
    transition: transform 0.3s ease;
    overflow-y: auto;
    overflow-x: hidden;
}
 
.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
 
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}
 
.brand-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    overflow: hidden;
}

.brand-logo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}
 
.brand-text h4 {
    margin: 0;
    color: white;
    font-size: 16px;
    font-weight: 600;
}
 
.brand-text span {
    color: rgba(255,255,255,0.7);
    font-size: 12px;
}
 
.sidebar-close {
    background: none;
    border: none;
    color: rgba(255,255,255,0.7);
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 5px;
    transition: all 0.3s ease;
}
 
.sidebar-close:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}
 
.sidebar-nav {
    padding: 20px 0;
}
 
.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
 
.nav-item {
    margin-bottom: 5px;
}
 
.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    border-left: 3px solid transparent;
}
 
.nav-link:hover {
    background: rgba(255,255,255,0.05);
    color: white;
}
 
.nav-link.active {
    background: var(--primary-light);
    color: white;
    border-left-color: var(--primary-color);
}

.nav-icon {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.nav-link:hover .nav-icon,
.nav-link.active .nav-icon {
    background: var(--primary-light-hover);
}
 
.nav-content {
    flex: 1;
}
 
.nav-title {
    display: block;
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 2px;
}
 
.nav-subtitle {
    display: block;
    font-size: 11px;
    opacity: 0.7;
}
 
.nav-badge {
    background: #e74c3c;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
}
 
.nav-arrow {
    color: rgba(255,255,255,0.5);
    font-size: 12px;
    transition: transform 0.3s ease;
}
 
.nav-link.expanded .nav-arrow {
    transform: rotate(180deg);
}
 
.sidebar-divider {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 20px 20px;
}
 
.nav-submenu {
    list-style: none;
    padding: 0;
    margin: 5px 0 0 0;
    background: rgba(0,0,0,0.2);
    border-radius: 8px;
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s ease;
}
 
.nav-submenu.show {
    max-height: 300px;
}
 
.nav-submenu li a {
    display: flex;
    align-items: center;
    padding: 10px 20px 10px 72px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s ease;
}
 
.nav-submenu li a:hover {
    background: rgba(255,255,255,0.05);
    color: white;
    padding-left: 77px;
}
 
.nav-submenu li a i {
    width: 20px;
    margin-right: 10px;
    font-size: 12px;
}
 
.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: auto;
}
 
.user-info-mini {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 12px;
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
}
 
.user-avatar-mini {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    margin-right: 10px;
}
 
.user-details-mini {
    flex: 1;
}
 
.user-name-mini {
    color: white;
    font-weight: 500;
    font-size: 13px;
    margin-bottom: 2px;
}
 
.user-time-mini {
    color: rgba(255,255,255,0.6);
    font-size: 11px;
}
 
.sidebar-actions {
    display: flex;
    gap: 8px;
}
 
.action-btn {
    width: 35px;
    height: 35px;
    background: rgba(255,255,255,0.1);
    border: none;
    border-radius: 8px;
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
}
 
.action-btn:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    transform: translateY(-2px);
}
 
.action-btn.danger:hover {
    background: #e74c3c;
}
 
/* Scrollbar Styling */
.cashier-sidebar::-webkit-scrollbar {
    width: 6px;
}
 
.cashier-sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
}
 
.cashier-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}
 
.cashier-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
}
 
/* Responsive adjustments */
@media (max-width: 992px) {
    .cashier-sidebar {
        transform: translateX(-100%);
    }
 
    .cashier-sidebar.active {
        transform: translateX(0);
    }
}
</style>
 
<script>
// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle submenu toggles
    const quickActions = document.getElementById('quickActions');
    const quickActionsSubmenu = document.getElementById('quickActionsSubmenu');
    const toolsMenu = document.getElementById('toolsMenu');
    const toolsSubmenu = document.getElementById('toolsSubmenu');
 
    if (quickActions) {
        quickActions.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('expanded');
            quickActionsSubmenu.classList.toggle('show');
 
            // Close other submenu
            toolsMenu.classList.remove('expanded');
            toolsSubmenu.classList.remove('show');
        });
    }
 
    if (toolsMenu) {
        toolsMenu.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('expanded');
            toolsSubmenu.classList.toggle('show');
 
            // Close other submenu
            quickActions.classList.remove('expanded');
            quickActionsSubmenu.classList.remove('show');
        });
    }
 
    // Close sidebar on mobile
    const sidebarClose = document.getElementById('sidebarClose');
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            document.querySelector('.cashier-sidebar').classList.remove('active');
        });
    }
});
 
// Quick action functions
function openQuickAdd() {
    // Implement quick add functionality
    showAlert('Quick Add feature coming soon!', 'info');
}
 
function openDiscountModal() {
    // Implement discount modal
    showAlert('Discount feature coming soon!', 'info');
}
 
function openTableTransfer() {
    // Implement table transfer
    showAlert('Table Transfer feature coming soon!', 'info');
}
 
function printReceipt() {
    // Implement print receipt
    showAlert('Print Receipt feature coming soon!', 'info');
}
 
function openCashDrawer() {
    // Implement cash drawer
    showAlert('Cash Drawer feature coming soon!', 'info');
}
 
function endShift() {
    showConfirm('Are you sure you want to end your shift?', 'End Shift').then(function(result) {
        if (result) {
            showAlert('Shift ended successfully!', 'success');
        }
    });
}
 
function viewReports() {
    showAlert('Reports feature coming soon!', 'info');
}
 
function systemSettings() {
    showAlert('Settings feature coming soon!', 'info');
}
 
function toggleTheme() {
    const body = document.body;
    const themeIcon = document.querySelector('.action-btn i');
    
    // Toggle theme class
    body.classList.toggle('dark-theme');
    
    // Update icon
    if (body.classList.contains('dark-theme')) {
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        localStorage.setItem('theme', 'dark');
        showAlert('Dark theme enabled!', 'success');
    } else {
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        localStorage.setItem('theme', 'light');
        showAlert('Light theme enabled!', 'success');
    }
}

// Apply saved theme on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    const themeIcon = document.querySelector('.action-btn i');
    
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
        if (themeIcon) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }
    
    // Existing sidebar functionality
    const quickActions = document.getElementById('quickActions');
    const quickActionsSubmenu = document.getElementById('quickActionsSubmenu');
    const toolsMenu = document.getElementById('toolsMenu');
    const toolsSubmenu = document.getElementById('toolsSubmenu');
 
    if (quickActions) {
        quickActions.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('expanded');
            quickActionsSubmenu.classList.toggle('show');
 
            // Close other submenu
            toolsMenu.classList.remove('expanded');
            toolsSubmenu.classList.remove('show');
        });
    }
 
    if (toolsMenu) {
        toolsMenu.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('expanded');
            toolsSubmenu.classList.toggle('show');
 
            // Close other submenu
            quickActions.classList.remove('expanded');
            quickActionsSubmenu.classList.remove('show');
        });
    }
 
    // Close sidebar on mobile
    const sidebarClose = document.getElementById('sidebarClose');
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            document.querySelector('.cashier-sidebar').classList.remove('active');
        });
    }
});
 
function showHelp() {
    showAlert('Help documentation coming soon!', 'info');
}
</script>