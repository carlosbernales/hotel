<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <div class="profile-sidebar">
        <div class="profile-userpic">
            <img src="img/Casa.jfif" class="img-responsive" alt="">
        </div>
        <div class="profile-usertitle">
            <div class="profile-usertitle-status">Cashier</div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="divider"></div>
    <ul class="nav menu">
        <li><a href="index.php?POS" class="nav-link"><em class="fa fa-dashboard">&nbsp;</em>POS</a></li>
        <li class="parent">
            <a class="menu-toggle nav-link" href="#">
                <em class="fa fa-list">&nbsp;</em> Orders 
                <span class="icon"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-1">
                <li><a class="nav-link" href="index.php?Order">
                    <span class="fa fa-arrow-right">&nbsp;</span> All Orders
                </a></li>
                <li><a class="nav-link" href="index.php?ProcessingOrder">
                    <span class="fa fa-arrow-right">&nbsp;</span> Processing Orders
                </a></li>
            </ul>
        </li>
        <li>
            <a class="nav-link" href="index.php?sales">
                <em class="fa fa-money">&nbsp;</em> Sales
            </a>
        </li>
    </ul>
</div>

<style>
    /* Sidebar base styling */
    .sidebar {
        background: #ffffff;
        color: #ffffff;
        min-height: 100vh;
        box-shadow: 3px 0 15px rgba(0,0,0,0.2);
        padding: 0;
        border-right: 1px solid rgba(255,255,255,0.1);
    }

    /* Profile section styling */
    .profile-sidebar {
        padding: 25px 15px;
        text-align: center;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .profile-userpic img {
        border-radius: 50%;
        width: 90px;
        height: 90px;
        object-fit: cover;
        border: 4px solid #DAA520;
        margin: 0 auto 15px;
        box-shadow: 0 0 20px rgba(218,165,32,0.3);
        transition: transform 0.3s ease;
    }

    .profile-userpic img:hover {
        transform: scale(1.05);
    }

    .profile-usertitle-status {
        font-size: 1.4em;
        color:rgb(66, 57, 57);
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 0;
    }

    /* Menu styling */
    .nav.menu {
        padding: 20px 0;
    }

    .nav.menu li {
        margin: 8px 0;
    }

    .nav.menu li a {
        color: #ffffff;
        padding: 12px 25px;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 0 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 500;
        font-size: 1.1em;
        letter-spacing: 0.3px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .nav.menu li a em:first-child {
        margin-right: 12px;
        width: 22px;
        text-align: center;
        font-size: 1.2em;
        color: #DAA520;
    }

    /* Active and hover states */
    .nav.menu li.active a,
    .nav.menu li a:hover {
        background: linear-gradient(45deg, #DAA520, #B8860B);
        color: #ffffff;
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(218,165,32,0.3);
        font-weight: 600;
    }

    /* Submenu styling */
    .children {
        background: rgba(0,0,0,0.2);
        margin: 5px 15px;
        border-radius: 8px;
        overflow: hidden;
    }

    .children li a {
        padding: 10px 15px 10px 50px !important;
        font-size: 1em;
        margin: 0 !important;
        border-radius: 0 !important;
        border-left: 3px solid transparent;
        color: rgba(255,255,255,0.9);
    }

    .children li a:hover {
        border-left: 3px solid #DAA520;
        background: rgba(218,165,32,0.2) !important;
        transform: translateX(0) !important;
        box-shadow: none !important;
        color: #ffffff;
    }

    /* Additional text enhancements */
    .nav.menu li a,
    .children li a {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Improve contrast for better readability */
    .nav.menu li a:hover em:first-child,
    .nav.menu li.active a em:first-child {
        color: #ffffff;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .menu-toggle {
        position: relative;
    }

    .icon {
        transition: all 0.3s ease;
    }

    /* Animation for submenu toggle */
    .menu-toggle[aria-expanded="true"] .icon {
        transform: rotate(180deg);
    }

    /* Smooth transitions */
    .children.collapse {
        transition: all 0.3s ease;
    }

    .children.in {
        padding: 5px 0;
    }

    /* Additional hover effects */
    .nav.menu li a:active {
        transform: scale(0.98);
    }

    /* Custom scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.1);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #DAA520;
        border-radius: 3px;
    }
</style>

<script>
    // Handle submenu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const submenu = document.getElementById('sub-item-1');
    const icon = menuToggle.querySelector('.icon em');

    function toggleSubmenu(show) {
        if (show) {
            submenu.classList.add('in');
            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');
        } else {
            submenu.classList.remove('in');
            icon.classList.remove('fa-minus');
            icon.classList.add('fa-plus');
        }
    }

    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSubmenu(!submenu.classList.contains('in'));
    });

    // Store submenu state in localStorage
    function saveSubmenuState(isOpen) {
        localStorage.setItem('submenuState', isOpen ? 'open' : 'closed');
    }

    function getSubmenuState() {
        return localStorage.getItem('submenuState') === 'open';
    }

    // Handle navigation links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't close submenu automatically
            const isSubmenuOpen = submenu.classList.contains('in');
            saveSubmenuState(isSubmenuOpen);
        });
    });

    // On page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.href;
        let hasActiveSubmenuItem = false;

        // Check submenu items first
        document.querySelectorAll('.children .nav-link').forEach(link => {
            if (currentUrl.includes(link.getAttribute('href'))) {
                link.parentNode.classList.add('active');
                toggleSubmenu(true);
                saveSubmenuState(true);
                hasActiveSubmenuItem = true;
            }
        });

        // If no submenu item is active, check other menu items
        if (!hasActiveSubmenuItem) {
            document.querySelectorAll('.nav.menu > li > a.nav-link').forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.parentNode.classList.add('active');
                    // Restore previous submenu state instead of closing
                    toggleSubmenu(getSubmenuState());
                }
            });
        }
    });
</script>
