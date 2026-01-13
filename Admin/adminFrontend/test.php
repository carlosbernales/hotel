<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Reviews</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">

</head>

<body>
    <div class="main-content" id="mainContent">
        <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-home"></i> Reviews
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <h3>127</h3>
                <p>Total Reviews</p>
            </div>
            <div class="stat-card">
                <h3>4.6</h3>
                <p>Average Rating</p>
            </div>
            <div class="stat-card">
                <h3>89%</h3>
                <p>5-Star Reviews</p>
            </div>
        </div>

        <div class="reviews-container">
            <div class="reviews-header">
                <h4><i class="fas fa-star" style="color: var(--gold);"></i> Guest Reviews</h4>
                <button class="btn btn-sm" style="background: var(--gold); color: #2c2c2c; border: none;">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>

            <div class="filter-section">
                <select id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
                <select id="roomFilter">
                    <option value="">All Rooms</option>
                    <option value="Deluxe Ocean View">Deluxe Ocean View</option>
                    <option value="Presidential Suite">Presidential Suite</option>
                    <option value="Garden Villa">Garden Villa</option>
                    <option value="Executive Room">Executive Room</option>
                </select>
                <input type="text" id="searchGuest" placeholder="Search guest name...">
            </div>

            <div id="reviewsList">
                <!-- Reviews will be dynamically loaded here -->
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

</body>

</html>