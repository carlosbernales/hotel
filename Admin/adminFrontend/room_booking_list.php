<style>
    /* Modal Professional Styling */
    #addAmenitiesModal .modal-dialog {
        max-width: 900px;
    }

    #addAmenitiesModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    #addAmenitiesModal .modal-header {
        background: linear-gradient(135deg, #d4a574 0%, #c89858 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-bottom: none;
    }

    #addAmenitiesModal .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    #addAmenitiesModal .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    #addAmenitiesModal .btn-close:hover {
        opacity: 1;
    }

    #addAmenitiesModal .modal-body {
        padding: 2rem;
        background-color: #f8f9fa;
    }

    /* Booking Info Card */
    #addAmenitiesModal .mb-3 p {
        background: white;
        padding: 1.25rem;
        border-radius: 8px;
        border-left: 4px solid #d4a574;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        line-height: 1.8;
    }

    #addAmenitiesModal .mb-3 p strong {
        color: #2c3e50;
        min-width: 100px;
        display: inline-block;
    }

    #addAmenitiesModal .mb-3 p span {
        color: #555;
    }

    /* Form Styling */
    #addAmenitiesModal .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    #addAmenitiesModal .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: white;
    }

    #addAmenitiesModal .form-select:focus {
        border-color: #d4a574;
        box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
        outline: none;
    }

    /* Table Styling */
    #addAmenitiesModal #selectedAmenitiesTable {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border: none;
        margin-top: 1.5rem;
    }

    #addAmenitiesModal #selectedAmenitiesTable thead {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }

    #addAmenitiesModal #selectedAmenitiesTable thead th {
        padding: 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #2c3e50;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody tr:last-child td {
        border-bottom: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable tbody tr:hover {
        background-color: #f8f9fa;
    }

    #addAmenitiesModal #selectedAmenitiesTable tfoot {
        background: #f8f9fa;
        border-top: 2px solid #d4a574;
    }

    #addAmenitiesModal #selectedAmenitiesTable tfoot th {
        padding: 1.25rem 1rem;
        font-size: 1.1rem;
        color: #2c3e50;
        border: none;
    }

    #addAmenitiesModal #selectedAmenitiesTable #subtotal {
        color: #d4a574;
        font-weight: 700;
        font-size: 1.2rem;
    }

    /* Quantity Input */
    #addAmenitiesModal .quantity {
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 0.5rem;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #addAmenitiesModal .quantity:focus {
        border-color: #d4a574;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(212, 165, 116, 0.25);
    }

    /* Remove Button */
    #addAmenitiesModal .remove-amenity {
        background: #e74c3c;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    #addAmenitiesModal .remove-amenity:hover {
        background: #c0392b;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
    }

    /* No Amenities Message */
    #addAmenitiesModal #noAmenitiesMessage {
        padding: 3rem 1rem;
        background: white;
        border-radius: 8px;
        color: #95a5a6;
        font-size: 1.1rem;
        font-style: italic;
    }

    /* Modal Footer */
    #addAmenitiesModal .modal-footer {
        padding: 1.5rem 2rem;
        background: white;
        border-top: 1px solid #e0e0e0;
    }

    #addAmenitiesModal .modal-footer .btn {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    #addAmenitiesModal .modal-footer .btn-secondary {
        background: #95a5a6;
        border: none;
    }

    #addAmenitiesModal .modal-footer .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(149, 165, 166, 0.3);
    }

    #addAmenitiesModal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #d4a574 0%, #c89858 100%);
        border: none;
    }

    #addAmenitiesModal .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #c89858 0%, #b8874a 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(212, 165, 116, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #addAmenitiesModal .modal-dialog {
            margin: 0.5rem;
        }

        #addAmenitiesModal .modal-body {
            padding: 1.25rem;
        }

        #addAmenitiesModal #selectedAmenitiesTable {
            font-size: 0.85rem;
        }

        #addAmenitiesModal #selectedAmenitiesTable thead th,
        #addAmenitiesModal #selectedAmenitiesTable tbody td {
            padding: 0.75rem 0.5rem;
        }

        #addAmenitiesModal .modal-footer .btn {
            padding: 0.6rem 1.5rem;
            font-size: 0.85rem;
        }
    }
</style>

<?php
include 'adminBackend/mydb.php';
include 'adminFrontend/header.php';

$bookings = [];

$sqlBookings = "SELECT * FROM bookings WHERE status = 'checkin'";
$resBookings = $conn->query($sqlBookings);

while ($row = $resBookings->fetch_assoc()) {
    $id = $row['booking_id'];
    $bookings[$id] = [
        "booking" => $row,
        "rooms" => [],
        "guests" => []
    ];
}

$sqlRooms = "
    SELECT 
        br.booking_id,
        br.room_type_id,
        br.room_type_name,
        br.price,
        br.room_number_fk_id,
        rn.room_number,
        rn.floor_number
    FROM booked_rooms br
    LEFT JOIN room_numbers rn 
        ON br.room_number_fk_id = rn.room_number_id
";
$resRooms = $conn->query($sqlRooms);

while ($r = $resRooms->fetch_assoc()) {
    $id = $r['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['rooms'][] = $r;
    }
}

$sqlGuests = "
    SELECT *
    FROM guest_names
";
$resGuests = $conn->query($sqlGuests);

while ($g = $resGuests->fetch_assoc()) {
    $id = $g['booking_id'];
    if (isset($bookings[$id])) {
        $bookings[$id]['guests'][] = $g;
    }
}



?>

<div class="main-content" id="mainContent">
    <div class="breadcrumb-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-home"> Checked In Room Bookings</i>
        </div>
    </div>

    <div class="info-card" style="margin-bottom: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="m-0"></h5>
        </div>
        <div class="table-responsive">
            <table id="roomTable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Fullname</th>
                        <th>Contact</th>
                        <th>Schedule</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $id => $data): ?>
                        <?php $b = $data['booking']; ?>
                        <tr>
                            <td><?= $b['booking_reference'] ?></td>
                            <td><?= $b['first_name'] . ' ' . $b['last_name'] ?></td>
                            <td><?= $b['contact'] ?></td>
                            <td>
                                <?= date("F j, Y", strtotime($b['check_in'])) ?> -
                                <?= date("F j, Y", strtotime($b['check_out'])) ?>
                            </td>
                            <td>
                                <a href="../Admin/index.php?checkInDetails_room_booking&id=<?= $id ?>"
                                    class="btn btn-primary btn-sm">
                                    Modify/View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php include 'adminFrontend/footer.php'; ?>