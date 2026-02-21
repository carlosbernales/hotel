<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom CSS -->
    <style>
/* Sidebar styling */
.sidebar {
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    padding: 10px;
}

.sidebar .nav-link {
    color: #000;
    font-weight: bold;
    transition: background-color 0.3s, color 0.3s;
}

.sidebar .nav-link.active {
    background-color: #ffc107;
    color: #fff;
}

/* Hover effect for sidebar links */
.sidebar .nav-link:hover {
    background-color: #ffc107; /* Change to your desired hover color */
    color: #fff;
}

/* Card styling */
.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Styling for navigation icons */
.nav-icons a {
    color: #333;
    font-size: 1.5rem;
    transition: color 0.3s;
}

/* Hover effect for navigation icons */
.nav-icons a:hover {
    color: #ffc107; /* Change to your desired hover color */
}

/* Styling for the search input */
input[type="text"] {
    border-radius: 25px;
    border: 1px solid #ccc;
    transition: border-color 0.3s;
}

/* Hover and focus effect for search input */
input[type="text"]:hover,
input[type="text"]:focus {
    border-color: #ffc107; /* Change to your desired hover color */
}

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4 class="mt-3 text-center">CASA ESTELA</h4>
                    <ul class="nav flex-column mt-4">
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-person"></i> Users Info</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-calendar-check"></i> Reservation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-box-seam"></i> Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-credit-card"></i> Payment</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-graph-up"></i> Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-boxes"></i> Inventory</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-chat-dots"></i> Feedback</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-info-circle"></i> About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <!-- Search Bar and Navigation Icons -->
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <input type="text" class="form-control w-50" placeholder="Search here...">
                    <div class="nav-icons">
                        <a href="#" class="mx-2"><i class="bi bi-bell"></i></a>
                        <a href="#" class="mx-2"><i class="bi bi-envelope"></i></a>
                        <a href="#" class="mx-2"><i class="bi bi-gear"></i></a>
                    </div>
                </div>

                <!-- Dashboard Overview Section -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>Current Booking and Reservation</h5>
                            <h3>7</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>Total Sales of Hotel</h5>
                            <h3>60,000</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>Total Sales of Cafe</h5>
                            <h3>123,000</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>New Orders</h5>
                            <h3>11</h3>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>Number of Users</h5>
                            <h3>35</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center p-3">
                            <h5>Total Orders</h5>
                            <h3>150</h3>
                        </div>
                    </div>
                </div>

                <!-- Charts and Graphs -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5>Hotel Revenue Over Time</h5>
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3">
                            <h5>Cafe Monthly Sales Orders</h5>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Initialize chart.js for demo purposes
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Revenue',
                    data: [5000, 10000, 15000, 20000, 25000, 30000, 35000],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            }
        });

        const ctx2 = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Sales',
                    data: [200, 400, 300, 500, 700, 600, 800],
                    backgroundColor: 'rgba(255, 205, 86, 0.8)'
                }]
            }
        });
    </script>
</body>
</html>
