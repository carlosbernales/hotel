<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

?>
<style>
    :root {
        --gold: #d4af37;
        --dark-bg: #2c2c2c;
        --sidebar-width: 250px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
        background: #f8f9fa;
    }

    .main-content {
        padding: 30px;
        min-height: 100vh;
    }

    .breadcrumb-custom {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .breadcrumb-custom i {
        color: var(--gold);
        margin-right: 8px;
    }

    /* Dashboard Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--gold);
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    .stat-icon.room {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-icon.cafe {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-icon.event {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, var(--gold) 0%, #b8941f 100%);
    }

    .stat-info h3 {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2c2c2c;
        line-height: 1;
    }

    .stat-change {
        display: inline-block;
        margin-top: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .stat-change.positive {
        background: #d4edda;
        color: #155724;
    }

    .stat-change.negative {
        background: #f8d7da;
        color: #721c24;
    }

    /* Charts Section */
    .charts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .chart-card h4 {
        color: #2c2c2c;
        margin-bottom: 20px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
    }

    .chart-card h4 i {
        color: var(--gold);
        margin-right: 10px;
    }

    /* Recent Bookings Table */
    .recent-bookings {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .recent-bookings h4 {
        color: #2c2c2c;
        margin-bottom: 20px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
    }

    .recent-bookings h4 i {
        color: var(--gold);
        margin-right: 10px;
    }

    .bookings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bookings-table thead {
        background: var(--gold);
    }

    .bookings-table thead th {
        padding: 12px;
        text-align: left;
        color: #2c2c2c;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .bookings-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        color: #495057;
        font-size: 0.9rem;
    }

    .bookings-table tbody tr:hover {
        background: rgba(212, 175, 55, 0.05);
    }

    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge.confirmed {
        background: #d4edda;
        color: #155724;
    }

    .badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge.cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .action-btn {
        background: white;
        border: 2px solid var(--gold);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        color: #2c2c2c;
    }

    .action-btn:hover {
        background: var(--gold);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }

    .action-btn i {
        font-size: 2rem;
        color: var(--gold);
        margin-bottom: 10px;
        display: block;
        transition: color 0.3s;
    }

    .action-btn:hover i {
        color: #2c2c2c;
    }

    .action-btn span {
        display: block;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .charts-container {
            grid-template-columns: 1fr;
        }

        .bookings-table {
            font-size: 0.8rem;
        }

        .bookings-table thead th,
        .bookings-table tbody td {
            padding: 8px;
        }
    }

    /* Simple Bar Chart */
    .simple-chart {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: 200px;
        padding: 20px 0;
    }

    .chart-bar {
        flex: 1;
        margin: 0 5px;
        background: linear-gradient(to top, var(--gold), #f4e7c3);
        border-radius: 4px 4px 0 0;
        position: relative;
        transition: transform 0.3s;
    }

    .chart-bar:hover {
        transform: scaleY(1.05);
    }

    .chart-bar-label {
        position: absolute;
        bottom: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.8rem;
        color: #6c757d;
        white-space: nowrap;
    }

    .chart-bar-value {
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.85rem;
        font-weight: 600;
        color: #2c2c2c;
    }
</style>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Dashboard</i>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="#" class="action-btn">
            <i class="fas fa-bed"></i>
            <span>New Room Booking</span>
        </a>
        <a href="#" class="action-btn">
            <i class="fas fa-utensils"></i>
            <span>New Cafe Booking</span>
        </a>
        <a href="#" class="action-btn">
            <i class="fas fa-calendar-alt"></i>
            <span>New Event Booking</span>
        </a>
        <a href="#" class="action-btn">
            <i class="fas fa-users"></i>
            <span>View Customers</span>
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Room Bookings</h3>
                    <div class="stat-number">142</div>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 12% this month
                    </span>
                </div>
                <div class="stat-icon room">
                    <i class="fas fa-bed"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Cafe Bookings</h3>
                    <div class="stat-number">89</div>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 8% this month
                    </span>
                </div>
                <div class="stat-icon cafe">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Event Bookings</h3>
                    <div class="stat-number">34</div>
                    <span class="stat-change negative">
                        <i class="fas fa-arrow-down"></i> 3% this month
                    </span>
                </div>
                <div class="stat-icon event">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-info">
                    <h3>Total Revenue</h3>
                    <div class="stat-number">₱428K</div>
                    <span class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> 15% this month
                    </span>
                </div>
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-container">
        <div class="chart-card">
            <h4><i class="fas fa-chart-bar"></i> Weekly Bookings Overview</h4>
            <div class="simple-chart">
                <div class="chart-bar" style="height: 65%;">
                    <span class="chart-bar-value">52</span>
                    <span class="chart-bar-label">Mon</span>
                </div>
                <div class="chart-bar" style="height: 80%;">
                    <span class="chart-bar-value">64</span>
                    <span class="chart-bar-label">Tue</span>
                </div>
                <div class="chart-bar" style="height: 55%;">
                    <span class="chart-bar-value">44</span>
                    <span class="chart-bar-label">Wed</span>
                </div>
                <div class="chart-bar" style="height: 90%;">
                    <span class="chart-bar-value">72</span>
                    <span class="chart-bar-label">Thu</span>
                </div>
                <div class="chart-bar" style="height: 100%;">
                    <span class="chart-bar-value">80</span>
                    <span class="chart-bar-label">Fri</span>
                </div>
                <div class="chart-bar" style="height: 95%;">
                    <span class="chart-bar-value">76</span>
                    <span class="chart-bar-label">Sat</span>
                </div>
                <div class="chart-bar" style="height: 70%;">
                    <span class="chart-bar-value">56</span>
                    <span class="chart-bar-label">Sun</span>
                </div>
            </div>
        </div>

        <div class="chart-card">
            <h4><i class="fas fa-chart-line"></i> Booking Distribution</h4>
            <div class="simple-chart">
                <div class="chart-bar" style="height: 100%; background: linear-gradient(to top, #667eea, #b3bef5);">
                    <span class="chart-bar-value">142</span>
                    <span class="chart-bar-label">Rooms</span>
                </div>
                <div class="chart-bar" style="height: 63%; background: linear-gradient(to top, #f5576c, #fcb4bd);">
                    <span class="chart-bar-value">89</span>
                    <span class="chart-bar-label">Cafe</span>
                </div>
                <div class="chart-bar" style="height: 24%; background: linear-gradient(to top, #00f2fe, #99f8fe);">
                    <span class="chart-bar-value">34</span>
                    <span class="chart-bar-label">Events</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="recent-bookings">
        <h4><i class="fas fa-clock"></i> Recent Bookings</h4>
        <div style="overflow-x: auto;">
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#BK-1245</td>
                        <td>Juan Dela Cruz</td>
                        <td><i class="fas fa-bed"></i> Room</td>
                        <td>Dec 28, 2025</td>
                        <td><span class="badge confirmed">Confirmed</span></td>
                        <td>₱3,500</td>
                    </tr>
                    <tr>
                        <td>#BK-1244</td>
                        <td>Maria Santos</td>
                        <td><i class="fas fa-utensils"></i> Cafe</td>
                        <td>Dec 28, 2025</td>
                        <td><span class="badge pending">Pending</span></td>
                        <td>₱1,200</td>
                    </tr>
                    <tr>
                        <td>#BK-1243</td>
                        <td>Pedro Garcia</td>
                        <td><i class="fas fa-calendar-alt"></i> Event</td>
                        <td>Dec 29, 2025</td>
                        <td><span class="badge confirmed">Confirmed</span></td>
                        <td>₱15,000</td>
                    </tr>
                    <tr>
                        <td>#BK-1242</td>
                        <td>Ana Reyes</td>
                        <td><i class="fas fa-bed"></i> Room</td>
                        <td>Dec 27, 2025</td>
                        <td><span class="badge confirmed">Confirmed</span></td>
                        <td>₱4,200</td>
                    </tr>
                    <tr>
                        <td>#BK-1241</td>
                        <td>Carlos Mendoza</td>
                        <td><i class="fas fa-utensils"></i> Cafe</td>
                        <td>Dec 27, 2025</td>
                        <td><span class="badge cancelled">Cancelled</span></td>
                        <td>₱850</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'adminFrontend/footer.php'; ?>