<tr>
    <td><?php echo date('Y-m-d', strtotime($row['booking_date'])); ?></td>
    <td><?php echo htmlspecialchars($row['room_type_name']); ?></td>
    <td><?php echo htmlspecialchars($row['room_number']); ?></td>
    <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
    <td><?php echo date('Y-m-d', strtotime($row['check_in'])); ?></td>
    <td><?php echo date('Y-m-d', strtotime($row['check_out'])); ?></td>
    <td><?php echo $row['nights']; ?></td>
    <td>₱<?php echo number_format($row['total_price'], 2); ?></td>
    <td><span class="badge badge-<?php echo $row['payment_status'] == 'Paid' ? 'success' : 'warning'; ?>"><?php echo $row['payment_status']; ?></span></td>
</tr> 