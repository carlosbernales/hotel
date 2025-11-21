<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div class="sidebar">
    <div class="sidebar-header" title="Toggle Sidebar">
        <div class="logo-container">
            <img src="../img/Casa.jfif" class="logo" alt="Casa Estela Logo">
            <span class="logo-text">CASHIER</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php?POS" class="nav-link" title="POS">
                    <i class="fas fa-cash-register"></i>
                    <span class="nav-text">POS</span>
                </a>
            </li>

            <li class="nav-item has-submenu">
                <a href="#" class="nav-link menu-toggle" title="Orders">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-text">Orders</span>
                    <i class="fas fa-chevron-right submenu-icon"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="index.php?Order" title="All Orders">
                            <i class="fas fa-circle"></i>
                            <span class="nav-text">All Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?ProcessingOrder" title="Processing Orders">
                            <i class="fas fa-circle"></i>
                            <span class="nav-text">Processing</span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php?OccupiedTables" title="View Occupied Tables">
                            <i class="fas fa-circle"></i>
                            <span class="nav-text"> Occupied Tables</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="index.php?sales" class="nav-link" title="Sales">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Sales</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Add toggle button at the bottom of sidebar -->
    <div class="sidebar-toggle" title="Toggle Sidebar">
        <i class="fas fa-chevron-left"></i>
    </div>
</div>

<style>
.main-content {
    margin-left: 60px;
    padding: 20px;
    transition: margin-left 0.3s ease;
}

.sidebar.expanded + .main-content {
    margin-left: 200px;
}

.sidebar {
    width: 60px;
    height: calc(100vh - 50px);
    background: #1a2035;
    position: fixed;
    left: 0;
    top: 50px;
    color: #fff;
    transition: width 0.3s ease;
    z-index: 999;
    overflow: hidden;
}

.sidebar.expanded {
    width: 200px;
}

.sidebar-header {
    background: #DAA520;
    padding: 12px;
    text-align: center;
    height: 50px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
    cursor: pointer;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid #fff;
    object-fit: cover;
}

.logo-text {
    color: #1a2035;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sidebar.expanded .logo-text {
    opacity: 1;
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 5px 10px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.nav-link i {
    width: 20px;
    font-size: 16px;
    color: #DAA520;
}

.nav-text {
    margin-left: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sidebar.expanded .nav-text {
    opacity: 1;
}

.submenu-icon {
    margin-left: auto;
    font-size: 0.8rem;
    opacity: 0;
    transition: all 0.3s ease;
}

.sidebar.expanded .submenu-icon {
    opacity: 1;
}

/* Update Submenu Styling */
.submenu {
    list-style: none;
    padding: 0;
    margin: 5px 0;
    display: none;
}

.has-submenu.active .submenu {
    display: block;
}

.submenu li {
    margin: 5px 10px;
}

.submenu li a {
    display: flex;
    align-items: center;
    padding: 12px;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    white-space: nowrap;
    font-size: 14px;
}

.submenu li a i {
    width: 20px;
    font-size: 12px;
    color: #DAA520;
}

.submenu li a:hover {
    background: linear-gradient(45deg, #DAA520, #B8860B);
}

.submenu li a:hover i {
    color: #fff;
}

.submenu li.active a {
    background: linear-gradient(45deg, #DAA520, #B8860B);
}

/* Remove the border-left from submenu */
.submenu {
    border-left: none;
}

/* Hover Effects */
.nav-link:hover {
    background: linear-gradient(45deg, #DAA520, #B8860B);
}

.nav-link:hover i {
    color: #fff;
}

/* Active States */
.nav-item.active > .nav-link {
    background: linear-gradient(45deg, #DAA520, #B8860B);
    color: #fff;
}

/* Custom Scrollbar */
.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: #DAA520;
    border-radius: 3px;
}

/* Toggle button styling */
.sidebar-toggle {
    position: absolute;
    bottom: 20px;
    left: 0;
    right: 0;
    text-align: center;
    cursor: pointer;
    padding: 10px;
}

.sidebar-toggle i {
    color: #DAA520;
    transition: transform 0.3s ease;
}

.sidebar.expanded .sidebar-toggle i {
    transform: rotate(180deg);
}

/* Custom tooltip styling */
[title]:not([title=""]):hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
    margin-left: 10px;
}

/* Hide tooltips when sidebar is expanded */
.sidebar.expanded [title]:hover::after {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar toggle
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    
    // Check if sidebar state is stored in localStorage
    const sidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
    if (sidebarExpanded) {
        sidebar.classList.add('expanded');
    }
    
    // Toggle sidebar on click
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        // Store sidebar state in localStorage
        localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
    });
    
    // Make sidebar header also toggle the sidebar
    const sidebarHeader = document.querySelector('.sidebar-header');
    sidebarHeader.addEventListener('click', () => {
        sidebar.classList.toggle('expanded');
        localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
    });

    // Handle submenu toggles
    const menuToggles = document.querySelectorAll('.menu-toggle');
    
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = toggle.closest('.has-submenu');
            parent.classList.toggle('active');
        });
    });

    // Set active state based on current URL
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.nav-link, .submenu a');

    navLinks.forEach(link => {
        if (currentUrl.includes(link.getAttribute('href'))) {
            // Add active class to the link's parent li
            link.closest('li').classList.add('active');
            
            // If it's a submenu item, also activate parent menu
            const submenuParent = link.closest('.has-submenu');
            if (submenuParent) {
                submenuParent.classList.add('active');
            }
        }
    });

    // Wrap all content after sidebar in a main-content div
    let content = document.createElement('div');
    content.className = 'main-content';
    
    // Move all elements after sidebar into the new content div
    let currentElement = sidebar.nextElementSibling;
    while(currentElement) {
        let nextElement = currentElement.nextElementSibling;
        content.appendChild(currentElement);
        currentElement = nextElement;
    }
    
    // Insert the content wrapper after the sidebar
    sidebar.parentNode.insertBefore(content, sidebar.nextSibling);
});
</script>
