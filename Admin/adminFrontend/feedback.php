<?php
if (!isset($_SESSION['user_type']) || 
    ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'frontdesk')) {
    header("Location: /Admin/Customer/aa/login.php");
    exit;
}
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$reviews = [];
$sql = "
    SELECT 
        rr.rating,
        rr.review,
        rr.created_at,
        rt.room_type AS roomName,
        CONCAT(u.first_name, ' ', u.last_name) AS guestName
    FROM room_reviews rr
    INNER JOIN room_types rt ON rr.room_type_id = rt.room_type_id
    INNER JOIN userss u ON rr.user_id = u.id
    ORDER BY rr.created_at DESC
";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            'guestName' => $row['guestName'],
            'roomName' => $row['roomName'],
            'rating' => (int) $row['rating'],
            'review' => $row['review'],
            'createdAt' => $row['created_at']
        ];
    }
}

$totalReviews = count($reviews);
$avgRating = $totalReviews
    ? round(array_sum(array_column($reviews, 'rating')) / $totalReviews, 1)
    : 0;

$fiveStarPercent = $totalReviews
    ? round((count(array_filter($reviews, fn($r) => $r['rating'] == 5)) / $totalReviews) * 100)
    : 0;
?>

<link rel="stylesheet" href="../Admin/adminFrontend/css/feedback.css">

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Room Reviews</i>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <h3>
                <?= $totalReviews ?>
            </h3>
            <p>Total Reviews</p>
        </div>
        <div class="stat-card">
            <h3>
                <?= $avgRating ?>
            </h3>
            <p>Average Rating</p>
        </div>
        <div class="stat-card">
            <h3>
                <?= $fiveStarPercent ?>%
            </h3>
            <p>5-Star Reviews</p>
        </div>
    </div>

    <div class="reviews-container">
        <div class="reviews-header">
            <h4><i class="fas fa-star" style="color: var(--gold);"></i> Guest Reviews</h4>
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
                <?php
                $rooms = $conn->query("SELECT room_type FROM room_types");
                while ($r = $rooms->fetch_assoc()) {
                    echo "<option value='{$r['room_type']}'>{$r['room_type']}</option>";
                }
                ?>
            </select>

            <input type="text" id="searchGuest" placeholder="Search guest name...">
        </div>

        <div id="reviewsList"></div>
    </div>
</div>



<?php include 'adminFrontend/footer.php'; ?>

<script>
    const reviews = <?= json_encode($reviews, JSON_UNESCAPED_UNICODE); ?>;

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    }

    function renderStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating
                ? '<i class="fas fa-star"></i>'
                : '<i class="far fa-star"></i>';
        }
        return stars;
    }

    function formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    function renderReviews(list) {
        const container = document.getElementById('reviewsList');

        if (!list.length) {
            container.innerHTML = `
                <p style="text-align:center;color:#6c757d;padding:40px;">
                    No reviews found.
                </p>`;
            return;
        }

        container.innerHTML = list.map(r => `
            <div class="review-card">
                <div class="review-header">
                    <div class="guest-info">
                        <div class="guest-avatar">${getInitials(r.guestName)}</div>
                        <div class="guest-details">
                            <h6>${r.guestName}</h6>
                            <p class="room-name">
                                <i class="fas fa-door-open"></i> ${r.roomName}
                            </p>
                        </div>
                    </div>
                    <div class="rating-stars">${renderStars(r.rating)}</div>
                </div>

                <div class="review-text">${r.review}</div>

                <div class="review-footer">
                    <i class="far fa-calendar"></i> ${formatDate(r.createdAt)}
                </div>
            </div>
        `).join('');
    }

    function filterReviews() {
        const rating = document.getElementById('ratingFilter').value;
        const room = document.getElementById('roomFilter').value;
        const guest = document.getElementById('searchGuest').value.toLowerCase();

        let filtered = reviews;

        if (rating) filtered = filtered.filter(r => r.rating == rating);
        if (room) filtered = filtered.filter(r => r.roomName === room);
        if (guest) filtered = filtered.filter(r =>
            r.guestName.toLowerCase().includes(guest)
        );

        renderReviews(filtered);
    }

    renderReviews(reviews);

    document.getElementById('ratingFilter').addEventListener('change', filterReviews);
    document.getElementById('roomFilter').addEventListener('change', filterReviews);
    document.getElementById('searchGuest').addEventListener('input', filterReviews);
</script>