<div class="info-item">
    <label><i class="fas fa-percent"></i> Discount Type</label>
    <select id="discountType" class="form-control">
        <option value="">No Discount</option>
        <option value="pwd">PWD (20%)</option>
        <option value="senior">Senior Citizen (20%)</option>
    </select>
</div>


<div class="info-item">
    <label><i class="fas fa-percent"></i> Discount Applied</label>
    <input class="form-control" id="discountPercentage" value="<?= (int) $booking['discount_percentage'] ?>%" readonly>
</div>
<div class="info-item">
    <label><i class="fas fa-money-bill-wave"></i> Discount Amount</label>
    <input type="hidden" id="discount_amount" value="<?= $booking['discount_amount'] ?>">
    <input class="form-control" id="discountAmount" value="0" readonly>

</div>