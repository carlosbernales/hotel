<tr>
    <td><?php echo date('Y-m-d', strtotime($row['sale_date'])); ?></td>
    <td>₱<?php echo number_format($row['total_revenue'], 2); ?></td>
    <td>₱<?php echo number_format($row['total_costs'], 2); ?></td>
    <td>₱<?php echo number_format($row['total_profit'], 2); ?></td>
</tr> 