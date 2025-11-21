<tr>
    <td><?php echo date('Y-m-d', strtotime($row['order_date'])); ?></td>
    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
    <td><?php echo htmlspecialchars($row['order_type']); ?></td>
    <td><?php echo $row['pickup_time']; ?></td>
    <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
    <td><span class="badge badge-<?php echo $row['status'] == 'Completed' ? 'success' : 'warning'; ?>"><?php echo $row['status']; ?></span></td>
    <td><?php echo htmlspecialchars($row['special_instructions']); ?></td>
    <td><?php echo htmlspecialchars($row['ordered_by']); ?></td>
</tr> 