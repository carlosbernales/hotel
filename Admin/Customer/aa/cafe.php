<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Reservation</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .package-section {
            margin-top: 80px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .carousel img{
            height: 300px;
        }
        .header-title {
            font-size: 1.5rem;
            color: #b6860a;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        .card img {
            height: 230px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        
        .custom-modal-width {
            max-width: 800px;
            margin: auto; /* Center the modal horizontally */
        }
        h4{
            font-size: 25px;
            margin-top: 17px;
            margin-left: 120px;
            color: #d4af37;
            font-weight: bold;
        }
        
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; /* Ensure the modal remains vertically centered */
        }
        .form-group {
            width: 100%;
        }
        .btn-warning {
            background-color: #b6860a;
            border-color: #b6860a;
        }
        .btn-advanced {
            float: right;
        }
        h6 {
            padding-left: 130px;
            font-size: 30px;
            font-weight: 900;
            color: #d4af37;
            margin-top: -20px;
        }
         ul {
            padding-left: 130px;
            font-size: 18px;
        }

        footer {
            background-color: #d4af37;
        }

        .carousel-item {
            height: 70vh;
            background-size: cover;
            background-position: center;
        }

        #fullScreenCarousel .carousel-item {
            height: 100vh;
        }

        #fullScreenCarousel .carousel-item img {
            height: 100vh;
            object-fit: cover;
        }
        .carousel-caption {
           animation-duration: 1.5s;
            animation-delay: 0.5s;
        }
    </style>
</head>
<body>
    <?php include_once 'nav.php'; ?>
    <section class="container-fluid p-0">
    <div id="fullScreenCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- First Slide -->
            <div class="carousel-item active">
                <img src="images/garden.jpg" class="d-block w-100" alt="The Garden">
                <div class="carousel-caption d-none d-md-block animate__animated animate__fadeIn">
                    <h5>The Garden</h5>
                    <p>Experience the beauty of nature with our stunning garden venue.</p>
                    <a href="#book" class="btn btn-warning mt-3">Book Now</a>
                </div>
            </div>
            <!-- Second Slide -->
            <div class="carousel-item">
                <img src="images/hall.jpg" class="d-block w-100" alt="Event Hall Interior">
                <div class="carousel-caption d-none d-md-block animate__animated animate__fadeIn">
                    <h5>Event Hall Interior</h5>
                    <p>Perfectly designed for elegant and intimate gatherings.</p>
                    <a href="#book" class="btn btn-warning mt-3">Book Now</a>
                </div>
            </div>
            <!-- Third Slide -->
            <div class="carousel-item">
                <img src="images/hall2.jpg" class="d-block w-100" alt="Garden Setup">
                <div class="carousel-caption d-none d-md-block animate__animated animate__fadeIn">
                    <h5>Hall Setup</h5>
                    <p>Celebrate under the open sky with our exceptional hall setup.</p>
                    <a href="#book" class="btn btn-warning mt-3">Book Now</a>
                </div>
            </div>
        </div>
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#fullScreenCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#fullScreenCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
    <div class="container">
        <!-- Table Package Section -->
        <div class="package-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="header-title">Table Package</h2>
                <button class="btn btn-warning btn-advanced">Advanced Order</button>
            </div>
            <div class="row">
                <!-- Couple Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="images/couple.jpg" class="card-img-top" alt="Couple Package">
                        <div class="card-body">
                            <h5 class="card-title">Couple</h5>
                            <p class="card-text">Capacity: 2</p>
                            <p class="card-text text-warning">₱199.00</p>
                            <button class="btn btn-warning" onclick="openModal('Couple', 199)">Reserve</button>
                        </div>
                    </div>
                </div>
                <!-- Friends Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="images/friends.jpg" class="card-img-top" alt="Friends Package">
                        <div class="card-body">
                            <h5 class="card-title">Friends</h5>
                            <p class="card-text">Capacity: 3-4</p>
                            <p class="card-text text-warning">₱299.00</p>
                            <button class="btn btn-warning" onclick="openModal('Friends', 299)">Reserve</button>
                        </div>
                    </div>
                </div>
                <!-- Family Card -->
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="images/family.jpg" class="card-img-top" alt="Family Package">
                        <div class="card-body">
                            <h5 class="card-title">Family</h5>
                            <p class="card-text">Capacity: 7-10</p>
                            <p class="card-text text-warning">₱599.00</p>
                            <button class="btn btn-warning" onclick="openModal('Family', 599)">Reserve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h4>Bring Your Event to Life with Us!</h4>
    <p>Reserve the perfect space for any occasion, from intimate gatherings to grand celebrations. Our dedicated team ensures every detail is flawlessly executed, allowing you to create unforgettable moments with ease. With flexible packages, elegant venues, and top-tier service, we're here to make your vision a reality. Book now and take the first step toward an extraordinary event experience!</p>
    <div class="container my-5">
        <!-- Event Packages Section -->
        <h2 class="text-center text-warning mb-4">Intimate Food Packages for 50 Pax</h2>
        <div class="row">
            <!-- Package A -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <img src="images/bg.jpg" class="card-img-top" alt="Package A">
                    <div class="card-body">
                        <h5 class="card-title">Package A</h5>
                        <p class="card-text">₱47,500.00</p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="selectPackage('Package A', 47500, '1 Appetizer, 2 Pasta, 2 Mains, Salad Bar, Rice, Drinks')">View Details</button>
                    </div>
                </div>
            </div>
            <!-- Package B -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <img src="images/bg.jpg" class="card-img-top" alt="Package B">
                    <div class="card-body">
                        <h5 class="card-title">Package B</h5>
                        <p class="card-text">₱55,000.00</p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="selectPackage('Package B', 55000, '2 Appetizers, 2 Pasta, 2 Mains, Salad Bar, Rice, 1 Dessert, Drinks')">View Details</button>
                    </div>
                </div>
            </div>
            <!-- Package C -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <img src="images/bg.jpg" class="card-img-top" alt="Package C">
                    <div class="card-body">
                        <h5 class="card-title">Package C</h5>
                        <p class="card-text">₱76,800.00</p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="selectPackage('Package C', 76800, '3 Appetizers, 2 Pasta, 2 Mains, Wagyu Steak Station**, Salad Bar, Rice, 2 Desserts, Drinks')">View Details</button>
                    </div>
                </div>
            </div>
            <!-- Venue Only -->
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <img src="images/hall.jpg" class="card-img-top" alt="Venue Only">
                    <div class="card-body">
                        <h5 class="card-title">Venue Only</h5>
                        <p class="card-text">₱20,000.00</p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="selectPackage('Venue Only', 20000, 'Exclusive use of air-conditioned tent area with tables and Tiffany chairs.')">View Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalLabel">Package Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Package:</strong> <span id="packageName"></span></p>
                    <p><strong>Price:</strong> ₱<span id="packagePrice"></span></p>
                    <p><strong>Menu:</strong> <span id="packageMenu"></span></p>
                    <hr>
                    <form>
                        <div class="mb-3">
                            <label for="eventDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="eventDate">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="startTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="startTime">
                            </div>
                            <div class="col-md-6">
                                <label for="endTime" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="endTime">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning">Reserve</button>
                </div>
            </div>
        </div>
    </div>
    <h6>Notes:</h6>
    <ul>
        <li>Exclusive use of air-conditioned tent area for 5 hours.</li>
        <li>Corkage fee charges apply on outside food, beverages, and alcoholic drinks.</li>
        <li>50% partial payment on selected package upon one-week reservation is non-refundable.</li>
        <li>Extension rate per hour is ₱2,000.00 (beyond 2:00 PM rate is ₱3,000.00).</li>
        <li>Venue rental only: ₱20,000.00 with tables and Tiffany chairs.</li>
    </ul>

    <!-- Footer -->
    <footer class="bg-warning text-center text-white py-2">
        <p class="mb-0">Casa Estela &copy; 2025. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPackage(name, price, menu) {
            document.getElementById('packageName').textContent = name;
            document.getElementById('packagePrice').textContent = price.toLocaleString();
            document.getElementById('packageMenu').textContent = menu;
        }
    </script>
<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content custom-modal-width">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Reserve Package</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Carousel -->
                <div id="packageCarousel" class="carousel slide mb-4" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/bg.jpg" class="d-block w-100" alt="Slide 1">
                        </div>
                        <div class="carousel-item">
                            <img src="images/bg.jpg" class="d-block w-100" alt="Slide 2">
                        </div>
                        <div class="carousel-item">
                            <img src="images/bg.jpg" class="d-block w-100" alt="Slide 3">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#packageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#packageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

                <!-- Reservation Form -->
                <form id="reservationForm">
                    <div class="form-group">
                        <label for="packageName">Package:</label>
                        <input type="text" id="packageName" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="packagePrice">Price:</label>
                        <input type="text" id="packagePrice" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="reservationDate">Date:</label>
                        <input type="date" id="reservationDate" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="reservationStartTime">Start Time:</label>
                            <input type="time" id="reservationStartTime" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="reservationEndTime">End Time:</label>
                            <input type="time" id="reservationEndTime" class="form-control" required>
                        </div>
                    </div>
                    <!-- Payment Selection -->
                    <div class="form-group">
                        <label for="paymentMethod">Payment Method:</label>
                        <select id="paymentMethod" class="form-control" required>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning btn-block">Confirm Reservation</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- JavaScript -->
    <script>
        // Function to open modal and set package details
        function openModal(packageName, packagePrice) {
            document.getElementById("packageName").value = packageName;
            document.getElementById("packagePrice").value = `₱${packagePrice}.00`;
            $("#reservationModal").modal("show");
        }

        // Handle form submission
        document.getElementById("reservationForm").addEventListener("submit", function (e) {
            e.preventDefault();
            alert("Reservation confirmed!");
            $("#reservationModal").modal("hide");
        });
    </script>
</body>
</html>
