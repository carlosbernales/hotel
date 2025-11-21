<tr>
    <td><?php echo htmlspecialchars($row['category']); ?></td>
    <td>₱<?php echo number_format($row['revenue'], 2); ?></td>
    <td>₱<?php echo number_format($row['costs'], 2); ?></td>
    <td>₱<?php echo number_format($row['profit'], 2); ?></td>
    <td><span class="badge badge-<?php echo strpos($row['growth'], '-') === 0 ? 'danger' : 'success'; ?>"><?php echo $row['growth']; ?>%</span></td>
</tr> 