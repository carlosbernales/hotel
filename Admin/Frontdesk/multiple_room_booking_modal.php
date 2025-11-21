<!-- Book Multiple Rooms Modal -->
<div class="modal fade" id="bookingFormModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Multiple Rooms</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body scrollable-content">
                <div class="selected-rooms">
                    <h6>Selected Rooms:</h6>
                    <div id="selectedRoomsList"></div>
                </div>

                <div class="guest-details mt-4">
                    <div class="form-group">
                        <label>Number of Guests for <span id="currentRoomType"></span></label>
                        <input type="number" class="form-control" id="guestCount" min="1" required>
                    </div>
                    <div id="guestNameFields"></div>
                </div>

                <!-- Discount Section -->
                <div class="price-summary mt-4">
                    <h6>Price Summary</h6>
                    <div id="roomPriceDetails"></div>
                    <div class="form-group">
                        <label>Discount Type:</label>
                        <select class="form-control" id="discountType" name="discount_type">
                            <option value="">None</option>
                            <?php
                            // Check if discount_types table exists and fetch discount types
                            $table_check = mysqli_query($con, "SHOW TABLES LIKE 'discount_types'");
                            if ($table_check && mysqli_num_rows($table_check) > 0) {
                                $discount_query = "SELECT * FROM discount_types WHERE is_active = 1 ORDER BY name ASC";
                                $discount_result = mysqli_query($con, $discount_query);
                                
                                if ($discount_result && mysqli_num_rows($discount_result) > 0) {
                                    while ($discount = mysqli_fetch_assoc($discount_result)) {
                                        echo '<option value="' . htmlspecialchars($discount['name']) . '" 
                                            data-percentage="' . htmlspecialchars($discount['percentage']) . '">' 
                                            . htmlspecialchars(ucfirst($discount['name'])) . ' (' 
                                            . htmlspecialchars($discount['percentage']) . '%)</option>';
                                    }
                                }
                            } else {
                                // Fallback to default options if table doesn't exist
                                echo '<option value="senior">Senior Citizen (10% off)</option>';
                                echo '<option value="pwd">PWD (10% off)</option>';
                                echo '<option value="student">Student (10% off)</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="total-amount mt-2">
                        Total Amount (<span id="nightCount"></span> nights): ₱<span id="originalAmount">0.00</span>
                    </div>
                    <div class="discount-section mt-2" style="display: none; color: #28a745;">
                        Discount: <span id="discountLabel">None</span> (-₱<span id="discountAmount">0.00</span>)
                    </div>
                    <div class="total-amount mt-2">
                        Final Amount: ₱<span id="totalAmount">0.00</span>
                    </div>
                    <div class="downpayment mt-2">
                        Downpayment (50%): ₱<span id="downpayment">0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="backToList()">Close</button>
                <button type="button" class="btn btn-warning" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking Summary Modal -->
<div class="modal fade" id="bookingSummaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Summary</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="selected-rooms mb-4">
                    <h6>Selected Rooms:</h6>
                    <div id="summaryRoomsList"></div>
                </div>

                <div class="guest-details mb-4">
                    <h6>Guest Information:</h6>
                    <div id="summaryGuestDetails"></div>
                </div>

                <div class="price-summary">
                    <div class="discount-section" style="display: none; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px dashed #28a745;">
                        <table style="width: 100%;">
                            <tr>
                                <td><strong>Discount Type:</strong></td>
                                <td class="text-right" id="summaryDiscountType">None</td>
                            </tr>
                            <tr>
                                <td><strong>Discount Amount:</strong></td>
                                <td class="text-right text-success" id="summaryDiscountAmount">₱0.00</td>
                            </tr>
                        </table>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <td>Total Amount:</td>
                            <td class="text-right" id="summaryTotalAmount">₱0.00</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="editBooking()">Edit</button>
                <button type="button" class="btn btn-warning" onclick="confirmBooking()">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Discount section styling */
.discount-section {
    border-top: 1px dashed #28a745;
    border-bottom: 1px dashed #28a745;
    padding: 8px 4px;
    margin: 10px 0;
    background-color: rgba(40, 167, 69, 0.05);
    border-radius: 4px;
}

.total-section {
    font-weight: bold;
    margin-top: 12px;
}

/* Highlight the final amount when discount is applied */
.total-amount:last-of-type {
    font-weight: bold;
    font-size: 1.1em;
    color: #28a745;
}
</style>

<script>
// Direct implementation for discount handling
document.addEventListener('DOMContentLoaded', function() {
    console.log('Discount script loaded');
    
    // Add jQuery extension for :contains if it's not already available
    if (typeof jQuery !== 'undefined') {
        if (!jQuery.expr[':'].contains) {
            jQuery.expr[':'].contains = function(a, i, m) {
                return jQuery(a).text().toUpperCase()
                    .indexOf(m[3].toUpperCase()) >= 0;
            };
        }
    }
    
    // Also add a polyfill for the Array.from method if not available
    if (!Array.from) {
        Array.from = function(iterable) {
            if (!iterable) return [];
            var result = [];
            for (var i = 0; i < iterable.length; i++) {
                result.push(iterable[i]);
            }
            return result;
        };
    }
    
    // Get the discount select element
    const discountSelect = document.getElementById('discountType');
    
    // Debug - log if element exists
    console.log('Discount select element found:', !!discountSelect);
    
    // If it exists, add event listener
    if (discountSelect) {
        discountSelect.addEventListener('change', function() {
            console.log('Discount changed to:', discountSelect.value);
            applyDiscount();
        });
    }
    
    // Function to apply the discount
    function applyDiscount() {
        console.log('applyDiscount called');
        
        // Get discount type
        const discountSelect = document.getElementById('discountType');
        if (!discountSelect) {
            console.log('Discount select not found');
            return null;
        }
        
        const discountType = discountSelect.value;
        console.log('Selected discount:', discountType);
        
        // Get the discount percentage from the selected option
        let discountPercentage = 10; // Default to 10%
        if (discountType && discountSelect.selectedIndex > 0) {
            const selectedOption = discountSelect.options[discountSelect.selectedIndex];
            const percentageMatch = selectedOption.text.match(/\((\d+(?:\.\d+)?)%\s+off\)/i);
            if (percentageMatch && percentageMatch[1]) {
                discountPercentage = parseFloat(percentageMatch[1]);
                console.log('Found discount percentage from option:', discountPercentage);
            }
        }
        
        // Get the original amount from the booking summary
        let originalAmount = 0;
        
        // Try multiple ways to find the original amount
        // First check the booking form
        const totalAmountElement = document.querySelector('#totalAmount');
        if (totalAmountElement) {
            const totalText = totalAmountElement.textContent || totalAmountElement.innerText;
            const match = totalText.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
            if (match) {
                originalAmount = parseFloat(match[1].replace(/,/g, ''));
                console.log('Found original amount in totalAmount element:', originalAmount);
            }
        }
        
        // If not found in booking form, try to find it in the modal
        if (originalAmount === 0) {
            // Try to find the original amount in the booking form or total
            const amountElements = document.querySelectorAll('*:contains("Total Amount")');
            for (let i = 0; i < amountElements.length; i++) {
                const element = amountElements[i];
                if (element.tagName === 'TD' || element.tagName === 'TR') {
                    const nextCell = element.nextElementSibling || element.querySelector('td:last-child');
                    if (nextCell) {
                        const text = nextCell.textContent || nextCell.innerText;
                        const match = text.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
                        if (match) {
                            originalAmount = parseFloat(match[1].replace(/,/g, ''));
                            console.log('Found original amount in table:', originalAmount);
                            break;
                        }
                    }
                }
            }
        }
        
        // If still not found, check if we have room_rate_total
        if (originalAmount === 0) {
            const roomRateTotal = document.getElementById('room_rate_total');
            if (roomRateTotal) {
                originalAmount = parseFloat(roomRateTotal.value || '0');
                console.log('Using room_rate_total as original amount:', originalAmount);
            }
        }
        
        // Last resort - try to find any numeric value with a peso sign
        if (originalAmount === 0) {
            const allText = document.body.innerText;
            const allMatches = allText.match(/₱\s*([0-9,]+(\.[0-9]+)?)/g);
            if (allMatches && allMatches.length > 0) {
                // Use the largest value as the total
                let maxAmount = 0;
                for (let i = 0; i < allMatches.length; i++) {
                    const match = allMatches[i].match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
                    if (match) {
                        const amount = parseFloat(match[1].replace(/,/g, ''));
                        if (amount > maxAmount) {
                            maxAmount = amount;
                        }
                    }
                }
                originalAmount = maxAmount;
                console.log('Using largest peso amount found:', originalAmount);
            }
        }
        
        if (originalAmount === 0) {
            console.log('Could not find original amount');
            return null;
        }
        
        // Calculate discount (using the actual percentage)
        if (discountType && discountType !== '') {
            // Calculate discount using the actual percentage
            const discountAmount = originalAmount * (discountPercentage / 100);
            const finalAmount = originalAmount - discountAmount;
            
            // Display discount information
            const discountLabelElement = document.getElementById('discountLabel');
            const discountAmountElement = document.getElementById('discountAmount');
            const totalAmountElement = document.getElementById('totalAmount');
            const discountSection = document.querySelector('.discount-section');
            
            discountLabelElement.textContent = discountType.charAt(0).toUpperCase() + discountType.slice(1) + 
                                              ' (' + discountPercentage + '%)';
            discountAmountElement.textContent = discountAmount.toFixed(2);
            totalAmountElement.textContent = finalAmount.toFixed(2);
            discountSection.style.display = 'block';
        } else {
            // No discount selected
            const discountSection = document.querySelector('.discount-section');
            if (discountSection) {
                discountSection.style.display = 'none';
            }
            totalAmountElement.textContent = originalAmount.toFixed(2);
        }
        
        // Also update downpayment if applicable
        const downpaymentElement = document.getElementById('downpayment');
        if (downpaymentElement) {
            const finalAmount = parseFloat(totalAmountElement.textContent);
            downpaymentElement.textContent = (finalAmount * 0.5).toFixed(2);
        }
        
        return {
            discountType: discountType,
            originalAmount: originalAmount,
            discountAmount: discountAmount,
            finalAmount: finalAmount
        };
    }
    
    // Function to be called when confirming booking
    window.confirmBooking = function() {
        console.log('Confirm booking called');
        const discountInfo = applyDiscount();
        
        // Show the booking summary modal
        $('#bookingFormModal').modal('hide');
        $('#bookingSummaryModal').modal('show');
        
        // Apply discount to the booking summary modal after a short delay
        setTimeout(function() {
            window.updateBookingSummaryDiscount(discountInfo);
        }, 300);
    };
    
    // Global function to update booking summary discount that can be called from anywhere
    window.updateBookingSummaryDiscount = function(discountInfo) {
        if (!discountInfo) {
            // If no discount info provided, calculate it
            discountInfo = applyDiscount();
        }
        
        if (discountInfo && discountInfo.discountType && discountInfo.discountType !== 'none') {
            console.log('Updating booking summary with discount:', discountInfo);
            
            // DIRECT INJECTION FOR SCREENSHOT MODAL
            // Find the exact modal structure shown in the screenshot
            const modalBodies = document.querySelectorAll('.modal-dialog .modal-content .modal-body');
            let updated = false;
            
            modalBodies.forEach(function(modalBody) {
                // Find Price Summary heading
                const priceSummaryHeadings = modalBody.querySelectorAll('h6, .price-summary');
                
                priceSummaryHeadings.forEach(function(heading) {
                    if (heading.textContent.includes('Price Summary')) {
                        console.log('Found Price Summary heading in modal');
                        
                        // First approach: Direct table manipulation
                        const table = heading.nextElementSibling || 
                                      heading.parentElement.querySelector('table');
                        
                        if (table && table.tagName === 'TABLE') {
                            console.log('Found table in Price Summary section');
                            
                            // Find the total amount row - should be the last row
                            const rows = table.querySelectorAll('tr');
                            const totalRow = Array.from(rows).find(row => 
                                row.textContent.includes('Total Amount'));
                            
                            if (totalRow) {
                                console.log('Found Total Amount row in table');
                                
                                // Create new discount row
                                const discountRow = document.createElement('tr');
                                discountRow.className = 'discount-row text-success';
                                discountRow.innerHTML = `
                                    <td><b>Discount:</b></td>
                                    <td>${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}</td>
                                `;
                                
                                // Insert before total row
                                totalRow.parentNode.insertBefore(discountRow, totalRow);
                                
                                // Update total amount value
                                const totalAmountCell = totalRow.querySelector('td:last-child');
                                if (totalAmountCell) {
                                    totalAmountCell.textContent = `₱${discountInfo.finalAmount.toFixed(2)}`;
                                    console.log('Updated total amount to:', totalAmountCell.textContent);
                                }
                                
                                updated = true;
                                console.log('Successfully injected discount row into table');
                            }
                        }
                        
                        // Second approach: If no table found, insert after the price summary heading
                        if (!updated) {
                            console.log('No table found, trying alternative approach');
                            
                            // Find element with Total Amount text
                            const totalAmountElements = modalBody.querySelectorAll('*');
                            let totalAmountElement = null;
                            
                            for (let i = 0; i < totalAmountElements.length; i++) {
                                if (totalAmountElements[i].textContent.includes('Total Amount:')) {
                                    totalAmountElement = totalAmountElements[i];
                                    break;
                                }
                            }
                            
                            if (totalAmountElement) {
                                console.log('Found Total Amount element:', totalAmountElement);
                                
                                // Create discount div and insert before total amount
                                const discountDiv = document.createElement('div');
                                discountDiv.className = 'discount-row text-success';
                                discountDiv.innerHTML = `<strong>Discount:</strong> ${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}`;
                                
                                totalAmountElement.parentNode.insertBefore(discountDiv, totalAmountElement);
                                
                                // Update total amount
                                const totalAmountText = totalAmountElement.textContent;
                                const amountMatch = totalAmountText.match(/₱(\d+,?\d*(\.\d+)?)/);
                                
                                if (amountMatch) {
                                    const updatedText = totalAmountText.replace(
                                        /₱(\d+,?\d*(\.\d+)?)/,
                                        `₱${discountInfo.finalAmount.toFixed(2)}`
                                    );
                                    totalAmountElement.textContent = updatedText;
                                    console.log('Updated total amount text to:', updatedText);
                                }
                                
                                updated = true;
                                console.log('Successfully added discount information');
                            }
                        }
                    }
                });
            });
            
            // SPECIAL CASE: The specific modal shown in the screenshot with Price Summary table
            if (!updated) {
                console.log('Trying direct injection into visible modal');
                
                const visibleModal = document.querySelector('.modal.show, .modal.fade.in');
                if (visibleModal) {
                    console.log('Found visible modal');
                    
                    // Find the Price Summary section
                    const priceSummarySection = visibleModal.querySelector('.modal-body table, .price-summary');
                    if (priceSummarySection) {
                        console.log('Found Price Summary section in visible modal');
                        
                        // Create a new row for discount and insert it
                        const discountRow = document.createElement('tr');
                        discountRow.className = 'discount-row bg-light text-success';
                        discountRow.innerHTML = `
                            <td><b>Discount:</b></td>
                            <td>${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}</td>
                        `;
                        
                        // Find the row with Total Amount
                        const totalRow = visibleModal.querySelector('tr:contains("Total Amount")') || 
                                        Array.from(visibleModal.querySelectorAll('tr')).find(row => 
                                            row.textContent.includes('Total Amount'));
                        
                        if (totalRow) {
                            console.log('Found Total Amount row');
                            totalRow.parentNode.insertBefore(discountRow, totalRow);
                            
                            // Update the total amount
                            const totalCell = totalRow.querySelector('td:last-child');
                            if (totalCell) {
                                totalCell.textContent = `₱${discountInfo.finalAmount.toFixed(2)}`;
                                console.log('Updated total amount in visible modal');
                            }
                            
                            updated = true;
                        } else {
                            // If no total row found, just append to the table
                            priceSummarySection.appendChild(discountRow);
                            console.log('Appended discount row to Price Summary section');
                            updated = true;
                        }
                    }
                }
            }
            
            // If still not updated, use the existing methods
            if (!updated) {
                console.log('Using fallback methods to update discount display');
                
                // Check all visible elements containing "Total Amount"
                const totalAmountElements = document.querySelectorAll('*');
                for (let i = 0; i < totalAmountElements.length; i++) {
                    if (totalAmountElements[i].textContent.includes('Total Amount:')) {
                        const element = totalAmountElements[i];
                        console.log('Found Total Amount element:', element);
                        
                        // Create discount element
                        const discountElement = document.createElement('div');
                        discountElement.className = 'discount-row bg-light text-success';
                        discountElement.style.padding = '5px';
                        discountElement.style.margin = '5px 0';
                        discountElement.style.borderRadius = '4px';
                        discountElement.innerHTML = `<strong>Discount:</strong> ${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}`;
                        
                        // Insert before total amount element
                        element.parentNode.insertBefore(discountElement, element);
                        
                        // Try to update the total amount display
                        const matches = element.textContent.match(/Total Amount:?\s*₱\s*([0-9,]+(\.[0-9]+)?)/i);
                        if (matches) {
                            const newText = element.textContent.replace(
                                /Total Amount:?\s*₱\s*([0-9,]+(\.[0-9]+)?)/i,
                                `Total Amount: ₱${discountInfo.finalAmount.toFixed(2)}`
                            );
                            element.textContent = newText;
                            console.log('Updated Total Amount text');
                        }
                        
                        updated = true;
                        break;
                    }
                }
            }
        }
    };
    
    // Handle edit booking by preserving discount
    window.editBooking = function() {
        console.log('Edit booking called');
        $('#bookingSummaryModal').modal('hide');
        $('#bookingFormModal').modal('show');
    };
    
    // Handle back to list
    window.backToList = function() {
        console.log('Back to list called');
        $('#bookingFormModal').modal('hide');
    };
    
    // Add handlers to all confirm booking buttons
    document.querySelectorAll('button[onclick="confirmBooking()"]').forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            window.confirmBooking();
        });
    });
    
    // Directly manipulate booking summary when it's shown
    $(document).on('shown.bs.modal', '#bookingSummaryModal', function() {
        console.log('Booking summary modal shown, applying discount');
        applyDiscount();
    });
    
    // Initialize function - run immediately
    function initDiscount() {
        console.log('Initializing discount handling');
        
        // Direct manipulation for the exact modal structure seen in the screenshot
        function insertDiscountIntoBookingSummary() {
            console.log('Attempting direct insertion into booking summary modal');
            
            // Get the active modal
            const visibleModal = document.querySelector('.modal.show, .modal.in, [style*="display: block"]');
            if (!visibleModal) {
                console.log('No visible modal found');
                return false;
            }
            
            console.log('Found visible modal:', visibleModal);
            
            // Check if it's the booking summary modal
            if (visibleModal.querySelector('.modal-title')?.textContent.includes('Booking Summary')) {
                console.log('Confirmed this is the booking summary modal');
                
                // Get discount information
                const discountType = document.getElementById('discountType')?.value;
                if (!discountType || discountType === '') {
                    console.log('No discount selected');
                    return false;
                }
                
                // Calculate discount
                const discountInfo = applyDiscount();
                if (!discountInfo) {
                    console.log('Failed to calculate discount');
                    return false;
                }
                
                // Find the "Price Summary" section in the modal
                const priceSummaryHeading = Array.from(visibleModal.querySelectorAll('h6')).find(h => 
                    h.textContent.trim() === 'Price Summary');
                    
                if (priceSummaryHeading) {
                    console.log('Found Price Summary heading');
                    
                    // Look for a table after the heading
                    let priceTable = priceSummaryHeading.nextElementSibling;
                    while (priceTable && priceTable.tagName !== 'TABLE') {
                        priceTable = priceTable.nextElementSibling;
                    }
                    
                    // If we found a table, look for the total amount row
                    if (priceTable && priceTable.tagName === 'TABLE') {
                        console.log('Found price table');
                        
                        // Look for the Total Amount row
                        const rows = priceTable.querySelectorAll('tr');
                        let totalRow = null;
                        
                        for (let i = 0; i < rows.length; i++) {
                            if (rows[i].textContent.includes('Total Amount')) {
                                totalRow = rows[i];
                                break;
                            }
                        }
                        
                        if (totalRow) {
                            console.log('Found Total Amount row');
                            
                            // Remove any existing discount rows
                            const existingDiscountRows = priceTable.querySelectorAll('.discount-row');
                            existingDiscountRows.forEach(row => row.remove());
                            
                            // Create discount row
                            const discountRow = document.createElement('tr');
                            discountRow.className = 'discount-row bg-light text-success';
                            discountRow.innerHTML = `
                                <td><b>Discount:</b></td>
                                <td>${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}</td>
                            `;
                            
                            // Insert before total row
                            totalRow.parentNode.insertBefore(discountRow, totalRow);
                            
                            // Update total amount
                            const totalAmountCell = totalRow.querySelector('td:last-child');
                            if (totalAmountCell) {
                                totalAmountCell.textContent = `₱${discountInfo.finalAmount.toFixed(2)}`;
                                console.log('Updated total amount cell to:', totalAmountCell.textContent);
                            }
                            
                            return true;
                        }
                    }
                    
                    // If we didn't find a table, look for the Total Amount text directly
                    if (!priceTable) {
                        console.log('No price table found, looking for Total Amount text');
                        
                        // Look for any element in the price summary section that mentions "Total Amount"
                        const priceSummarySection = priceSummaryHeading.parentElement;
                        const totalAmountElement = Array.from(priceSummarySection.querySelectorAll('*')).find(el => 
                            el.textContent.includes('Total Amount:'));
                            
                        if (totalAmountElement) {
                            console.log('Found Total Amount element:', totalAmountElement);
                            
                            // Check if we already have a discount row
                            const existingDiscountElement = Array.from(priceSummarySection.querySelectorAll('*')).find(el => 
                                el.textContent.includes('Discount:'));
                                
                            if (existingDiscountElement) {
                                existingDiscountElement.remove();
                            }
                            
                            // Create discount element
                            const discountElement = document.createElement('div');
                            discountElement.className = 'discount-section bg-light text-success';
                            discountElement.style.padding = '8px';
                            discountElement.style.margin = '8px 0';
                            discountElement.style.borderRadius = '4px';
                            discountElement.innerHTML = `<strong>Discount:</strong> ${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}`;
                            
                            // Insert before total amount element
                            totalAmountElement.parentNode.insertBefore(discountElement, totalAmountElement);
                            
                            // Update total amount text if possible
                            const totalTextMatch = totalAmountElement.textContent.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
                            if (totalTextMatch) {
                                const newText = totalAmountElement.textContent.replace(
                                    /₱\s*([0-9,]+(\.[0-9]+)?)/,
                                    `₱${discountInfo.finalAmount.toFixed(2)}`
                                );
                                totalAmountElement.textContent = newText;
                                console.log('Updated total amount text to:', newText);
                            }
                            
                            return true;
                        }
                    }
                }
                
                // If we still haven't found the Price Summary, try a more general approach
                const totalAmountText = Array.from(visibleModal.querySelectorAll('*')).find(el => 
                    el.textContent.includes('Total Amount:'));
                    
                if (totalAmountText) {
                    console.log('Found Total Amount text element:', totalAmountText);
                    
                    // Create discount element
                    const discountElement = document.createElement('div');
                    discountElement.className = 'discount-section bg-light text-success';
                    discountElement.style.padding = '8px';
                    discountElement.style.margin = '8px 0';
                    discountElement.style.borderRadius = '4px';
                    discountElement.innerHTML = `<strong>Discount:</strong> ${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}`;
                    
                    // Insert before total amount element
                    totalAmountText.parentNode.insertBefore(discountElement, totalAmountText);
                    
                    // Update total amount text
                    const totalTextMatch = totalAmountText.textContent.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
                    if (totalTextMatch) {
                        const newText = totalAmountText.textContent.replace(
                            /₱\s*([0-9,]+(\.[0-9]+)?)/,
                            `₱${discountInfo.finalAmount.toFixed(2)}`
                        );
                        totalAmountText.textContent = newText;
                    }
                    
                    return true;
                }
            }
            
            return false;
        }
        
        // Try to directly insert discount into any modal that becomes visible
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('close') || e.target.dataset.dismiss === 'modal')) {
                return; // Don't process modal close buttons
            }
            
            setTimeout(function() {
                const success = insertDiscountIntoBookingSummary();
                if (!success) {
                    console.log('Direct insertion failed, falling back to standard methods');
                    window.updateBookingSummaryDiscount();
                }
            }, 300);
        });
        
        // First, let's directly check all existing modals
        setTimeout(function() {
            console.log('Running initial check for booking summary modals');
            const discountType = document.getElementById('discountType')?.value;
            if (discountType && discountType !== 'none') {
                window.updateBookingSummaryDiscount();
            }
        }, 500);
        
        // Set up a MutationObserver to watch for new modals
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length) {
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        if (node.nodeType === 1 && 
                            (node.classList?.contains('modal') || 
                             (node.querySelector && node.querySelector('.modal')))) {
                            console.log('New modal detected, checking for booking summary');
                            setTimeout(function() {
                                const discountType = document.getElementById('discountType')?.value;
                                if (discountType && discountType !== 'none') {
                                    window.updateBookingSummaryDiscount();
                                }
                            }, 300);
                        }
                    }
                }
            });
        });
        
        // Start observing the document body
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Also set up direct event handling for any booking summary button
        document.addEventListener('click', function(e) {
            if (e.target && (
                e.target.innerText?.includes('Confirm Booking') || 
                (e.target.innerHTML && e.target.innerHTML.includes('Confirm Booking')) ||
                e.target.classList?.contains('btn-confirm-booking') ||
                e.target.id === 'confirmBookingBtn'
            )) {
                console.log('Confirm Booking button clicked');
                setTimeout(function() {
                    window.updateBookingSummaryDiscount();
                }, 300);
            }
        });
        
        // Add event listener for all modal show events
        document.addEventListener('shown.bs.modal', function(e) {
            console.log('Modal shown event detected');
            setTimeout(function() {
                const discountType = document.getElementById('discountType')?.value;
                if (discountType && discountType !== 'none') {
                    window.updateBookingSummaryDiscount();
                }
            }, 300);
        });
        
        // Check if jQuery is available and add additional handlers
        if (typeof jQuery !== 'undefined') {
            console.log('jQuery detected, adding modal event handlers');
            jQuery(document).on('shown.bs.modal', '.modal', function() {
                console.log('jQuery modal shown event');
                setTimeout(function() {
                    window.updateBookingSummaryDiscount();
                }, 300);
            });
        }
    }
    
    // Run initialization
    initDiscount();
    
    // Direct implementation for the exact modal structure in the screenshot
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('shown.bs.modal', '.modal', function() {
            console.log('jQuery modal shown event detected');
            const $modal = jQuery(this);
            
            // Check if this is the booking summary modal
            if ($modal.find('.modal-title').text().includes('Booking Summary')) {
                console.log('Booking summary modal shown via jQuery');
                
                // Get discount type
                const discountType = jQuery('#discountType').val();
                if (!discountType || discountType === '') {
                    console.log('No discount selected in jQuery handler');
                    return;
                }
                
                // Calculate discount
                const discountInfo = applyDiscount();
                if (!discountInfo) {
                    console.log('No discount info available');
                    return;
                }
                
                console.log('Discount info:', discountInfo);
                
                // Target the Price Summary table
                const $priceTable = $modal.find('table').filter(function() {
                    return jQuery(this).text().includes('Total Amount');
                });
                
                if ($priceTable.length) {
                    console.log('Found price table using jQuery');
                    
                    // Find the total amount row
                    const $totalRow = $priceTable.find('tr').filter(function() {
                        return jQuery(this).text().includes('Total Amount');
                    });
                    
                    if ($totalRow.length) {
                        console.log('Found total amount row using jQuery');
                        
                        // Remove any existing discount rows
                        $priceTable.find('.discount-row').remove();
                        
                        // Create new discount row
                        const $discountRow = jQuery('<tr class="discount-row bg-light text-success"></tr>');
                        $discountRow.html(`
                            <td><b>Discount:</b></td>
                            <td>${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}</td>
                        `);
                        
                        // Insert before total row
                        $discountRow.insertBefore($totalRow);
                        
                        // Update total amount
                        $totalRow.find('td:last-child').text(`₱${discountInfo.finalAmount.toFixed(2)}`);
                        
                        console.log('Successfully updated discount in booking summary');
                    }
                } else {
                    console.log('Price table not found with jQuery, trying DOM approach');
                    
                    // Try DOM approach
                    const priceSummaryText = $modal.find('.modal-body').text();
                    if (priceSummaryText.includes('Price Summary')) {
                        console.log('Found Price Summary section');
                        
                        // Look for Total Amount text
                        const $totalAmountElement = $modal.find('.modal-body div').filter(function() {
                            return jQuery(this).text().match(/Total Amount:?\s*₱/);
                        });
                        
                        if ($totalAmountElement.length) {
                            console.log('Found Total Amount element:', $totalAmountElement.text());
                            
                            // Remove any existing discount elements
                            $modal.find('.discount-section, .discount-row').remove();
                            
                            // Create discount element
                            const $discountElement = jQuery('<div class="discount-section bg-light text-success"></div>');
                            $discountElement.css({
                                'padding': '8px',
                                'margin': '8px 0',
                                'border-radius': '4px'
                            });
                            $discountElement.html(`<strong>Discount:</strong> ${discountInfo.discountType.charAt(0).toUpperCase() + discountInfo.discountType.slice(1)} (10%): -₱${discountInfo.discountAmount.toFixed(2)}`);
                            
                            // Insert before total amount element
                            $discountElement.insertBefore($totalAmountElement);
                            
                            // Try to update total amount
                            const totalText = $totalAmountElement.text();
                            const updatedText = totalText.replace(
                                /₱\s*([0-9,]+(\.[0-9]+)?)/,
                                `₱${discountInfo.finalAmount.toFixed(2)}`
                            );
                            $totalAmountElement.text(updatedText);
                            
                            console.log('Successfully added discount information via jQuery');
                        }
                    }
                }
            }
        });
    }
});
</script>

<!-- Add an external script to ensure discount information is displayed -->
<script>
// This script runs immediately and provides additional handling for the booking summary modal
(function() {
    // Wait for DOM to be ready
    function domReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }
    
    domReady(function() {
        console.log('External script running for direct modal update');
        
        // Function specifically targeting the booking summary modal in the screenshot
        function updateBookingSummaryModalDirectly() {
            console.log('Directly targeting booking summary modal...');
            
            // Find any visible modal that looks like a booking summary
            const visibleModals = document.querySelectorAll('.modal');
            let bookingSummaryModal = null;
            
            for (let i = 0; i < visibleModals.length; i++) {
                const modal = visibleModals[i];
                if (modal.style.display === 'block' || 
                    modal.classList.contains('show') || 
                    window.getComputedStyle(modal).display === 'block') {
                    
                    // Check if this modal has a Price Summary section
                    if (modal.querySelector('.modal-title')?.textContent.includes('Booking Summary') ||
                        modal.querySelector('.price-summary') ||
                        modal.querySelector('h6')?.textContent.includes('Price Summary')) {
                        
                        bookingSummaryModal = modal;
                        console.log('Found booking summary modal:', modal);
                        break;
                    }
                }
            }
            
            if (!bookingSummaryModal) {
                console.log('No visible booking summary modal found');
                return;
            }
            
            // Get discount information
            const discountSelect = document.getElementById('discountType');
            if (!discountSelect || !discountSelect.value || discountSelect.value === 'none') {
                console.log('No discount selected or discountSelect not found');
                return;
            }
            
            console.log('Discount selected:', discountSelect.value);
            
            // Find the price summary section in the exact structure shown in the screenshot
            const priceSummarySection = bookingSummaryModal.querySelector('.price-summary') || 
                                       Array.from(bookingSummaryModal.querySelectorAll('h6')).find(h => 
                                           h.textContent.includes('Price Summary'))?.parentElement;
            
            if (!priceSummarySection) {
                console.log('Price summary section not found');
                return;
            }
            
            console.log('Found price summary section');
            
            // Find the Total Amount element - EXACTLY as shown in the screenshot
            // In the screenshot it's a table row with "Total Amount: ₱XX,XXX"
            let totalAmountElement = Array.from(priceSummarySection.querySelectorAll('*')).find(el => 
                el.textContent.includes('Total Amount:') || 
                el.textContent.includes('Total Amount'));
            
            // If not found directly, try to find it in any table
            if (!totalAmountElement) {
                const tables = priceSummarySection.querySelectorAll('table');
                for (let i = 0; i < tables.length; i++) {
                    const rows = tables[i].querySelectorAll('tr');
                    for (let j = 0; j < rows.length; j++) {
                        if (rows[j].textContent.includes('Total Amount')) {
                            totalAmountElement = rows[j];
                            break;
                        }
                    }
                    if (totalAmountElement) break;
                }
            }
            
            if (!totalAmountElement) {
                console.log('Total amount element not found');
                return;
            }
            
            console.log('Found total amount element:', totalAmountElement.textContent);
            
            // Extract the original amount
            const amountMatch = totalAmountElement.textContent.match(/₱\s*([0-9,]+(\.[0-9]+)?)/);
            if (!amountMatch) {
                console.log('Could not extract original amount');
                return;
            }
            
            const originalAmount = parseFloat(amountMatch[1].replace(/,/g, ''));
            console.log('Original amount:', originalAmount);
            
            // Calculate discount
            const discountType = discountSelect.value;
            const discountAmount = originalAmount * 0.1; // 10% discount
            const finalAmount = originalAmount - discountAmount;
            
            console.log('Calculated discount:', discountAmount);
            console.log('Final amount:', finalAmount);
            
            // Check if discount element already exists
            let existingDiscountElement = null;
            
            // First try to find by class name
            existingDiscountElement = priceSummarySection.querySelector('.discount-row, .discount-section');
            
            // If not found by class, find by content
            if (!existingDiscountElement) {
                existingDiscountElement = Array.from(priceSummarySection.querySelectorAll('*')).find(el => 
                    el.textContent.includes('Discount:'));
            }
            
            if (existingDiscountElement) {
                console.log('Updating existing discount element');
                existingDiscountElement.innerHTML = `<b>Discount:</b> ${discountType.charAt(0).toUpperCase() + discountType.slice(1)} (10%): -₱${discountAmount.toFixed(2)}`;
                existingDiscountElement.style.display = 'table-row';
                existingDiscountElement.style.color = '#28a745';
                existingDiscountElement.style.background = '#f8f9fa';
            } else {
                console.log('Creating new discount element');
                
                // If Total Amount is in a table row
                if (totalAmountElement.tagName === 'TR') {
                    console.log('Creating table row for discount');
                    const discountRow = document.createElement('tr');
                    discountRow.className = 'discount-row bg-light text-success';
                    discountRow.innerHTML = `
                        <td><b>Discount:</b></td>
                        <td>${discountType.charAt(0).toUpperCase() + discountType.slice(1)} (10%): -₱${discountAmount.toFixed(2)}</td>
                    `;
                    
                    // Insert before total amount row
                    totalAmountElement.parentNode.insertBefore(discountRow, totalAmountElement);
                } else {
                    // If Total Amount is in a different element
                    console.log('Creating div for discount');
                    const discountElement = document.createElement('div');
                    discountElement.className = 'discount-section bg-light text-success';
                    discountElement.style.padding = '8px';
                    discountElement.style.margin = '8px 0';
                    discountElement.style.borderRadius = '4px';
                    discountElement.innerHTML = `<b>Discount:</b> ${discountType.charAt(0).toUpperCase() + discountType.slice(1)} (10%): -₱${discountAmount.toFixed(2)}`;
                    
                    // Insert before total amount element
                    totalAmountElement.parentNode.insertBefore(discountElement, totalAmountElement);
                }
            }
            
            // Now update the total amount with the final (discounted) amount
            if (totalAmountElement.tagName === 'TR') {
                // If it's a table row, update the second cell
                const totalCell = totalAmountElement.querySelector('td:last-child');
                if (totalCell) {
                    totalCell.textContent = `₱${finalAmount.toFixed(2)}`;
                    console.log('Updated total amount cell to:', totalCell.textContent);
                }
            } else {
                // Otherwise update the whole element's text
                const updatedText = totalAmountElement.textContent.replace(
                    /₱\s*([0-9,]+(\.[0-9]+)?)/,
                    `₱${finalAmount.toFixed(2)}`
                );
                totalAmountElement.textContent = updatedText;
                console.log('Updated total amount text to:', updatedText);
            }
            
            console.log('Successfully updated booking summary with discount');
        }
        
        // Execute immediately 
        updateBookingSummaryModalDirectly();
        
        // Execute again when any modal is shown
        document.addEventListener('click', function(e) {
            console.log('Click detected, checking if it might show a modal');
            if (e.target.tagName === 'BUTTON') {
                setTimeout(updateBookingSummaryModalDirectly, 300);
            }
        });
        
        // Use MutationObserver to detect when modals are added to the DOM
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && mutation.addedNodes.length) {
                    setTimeout(updateBookingSummaryModalDirectly, 300);
                }
            });
        });
        
        observer.observe(document.body, { childList: true, subtree: true });
        
        // Run periodically to ensure the discount is displayed
        setInterval(updateBookingSummaryModalDirectly, 1000);
    });
})();
</script>

<script>
// Add function to handle discount selection and calculation
function applyDiscount() {
    const discountSelect = document.getElementById('discountType');
    if (!discountSelect) return;
    
    const discountType = discountSelect.value;
    const originalAmountElement = document.getElementById('originalAmount');
    const discountLabelElement = document.getElementById('discountLabel');
    const discountAmountElement = document.getElementById('discountAmount');
    const totalAmountElement = document.getElementById('totalAmount');
    const discountSection = document.querySelector('.discount-section');
    
    if (!originalAmountElement || !discountLabelElement || !discountAmountElement || !totalAmountElement || !discountSection) {
        console.error('Required elements not found for discount calculation');
        return;
    }
    
    const originalAmount = parseFloat(originalAmountElement.textContent.replace(/,/g, '')) || 0;
    
    if (discountType && discountType !== '') {
        // Calculate 10% discount
        const discountAmount = originalAmount * 0.1;
        const finalAmount = originalAmount - discountAmount;
        
        // Display discount information
        discountLabelElement.textContent = discountType.charAt(0).toUpperCase() + discountType.slice(1);
        discountAmountElement.textContent = discountAmount.toFixed(2);
        totalAmountElement.textContent = finalAmount.toFixed(2);
        discountSection.style.display = 'block';
    } else {
        // No discount selected
        discountSection.style.display = 'none';
        totalAmountElement.textContent = originalAmount.toFixed(2);
    }
    
    // Also update downpayment if applicable
    const downpaymentElement = document.getElementById('downpayment');
    if (downpaymentElement) {
        const finalAmount = parseFloat(totalAmountElement.textContent);
        downpaymentElement.textContent = (finalAmount * 0.5).toFixed(2);
    }
}

// Add event listener for the discountType
document.addEventListener('DOMContentLoaded', function() {
    const discountSelect = document.getElementById('discountType');
    if (discountSelect) {
        discountSelect.addEventListener('change', applyDiscount);
    }
    
    // Initialize discount calculation when the booking modal is shown
    $('#bookingFormModal').on('shown.bs.modal', function() {
        applyDiscount();
    });
});

// Replace the confirmBooking function with this improved version
function confirmBooking() {
    console.log('confirmBooking called');
    
    // Check if form is valid
    const form = document.getElementById('multipleBookingForm');
    if (form && !form.checkValidity()) {
        alert('Please fill out all required fields');
        return;
    }
    
    // Get discount information
    const discountSelect = document.getElementById('discountType');
    const discountType = discountSelect ? discountSelect.value : '';
    
    // Get amount information
    const originalAmountEl = document.getElementById('originalAmount');
    const totalAmountEl = document.getElementById('totalAmount');
    
    const originalAmount = originalAmountEl ? parseFloat(originalAmountEl.textContent.replace(/[₱,]/g, '')) || 0 : 0;
    const finalAmount = totalAmountEl ? parseFloat(totalAmountEl.textContent.replace(/[₱,]/g, '')) || 0 : 0;
    const discountAmount = originalAmount - finalAmount;
    
    console.log('Confirming booking with discount type:', discountType);
    console.log('Original amount:', originalAmount);
    console.log('Discount amount:', discountAmount);
    console.log('Final amount:', finalAmount);
    
    // Directly update the booking summary modal with discount information
    const summaryDiscountType = document.getElementById('summaryDiscountType');
    const summaryDiscountAmount = document.getElementById('summaryDiscountAmount');
    const discountSection = document.querySelector('#bookingSummaryModal .discount-section');
    const summaryTotalAmount = document.getElementById('summaryTotalAmount');
    
    if (discountType && discountType !== '' && discountAmount > 0) {
        if (summaryDiscountType) {
            summaryDiscountType.textContent = discountType.charAt(0).toUpperCase() + discountType.slice(1) + ' (10%)';
        }
        
        if (summaryDiscountAmount) {
            summaryDiscountAmount.textContent = '₱' + discountAmount.toFixed(2);
        }
        
        if (discountSection) {
            discountSection.style.display = 'block';
        }
        
        console.log('Updated summary discount information');
    } else {
        if (discountSection) {
            discountSection.style.display = 'none';
        }
    }
    
    if (summaryTotalAmount) {
        summaryTotalAmount.textContent = '₱' + finalAmount.toFixed(2);
    }
    
    // Add a direct DOM modification for the price summary table
    setTimeout(function() {
        const priceSummary = document.querySelector('#bookingSummaryModal .price-summary table');
        if (priceSummary) {
            console.log('Found price summary table');
            
            // Check if discount row exists
            let discountRow = Array.from(priceSummary.querySelectorAll('tr')).find(row => 
                row.textContent.includes('Discount') || row.classList.contains('discount-row')
            );
            
            // If no discount row and we have a discount, add it
            if (!discountRow && discountType && discountAmount > 0) {
                console.log('Adding discount row to price summary table');
                
                // Create discount row
                discountRow = document.createElement('tr');
                discountRow.className = 'discount-row text-success';
                discountRow.innerHTML = `
                    <td><b>Discount (${discountType.charAt(0).toUpperCase() + discountType.slice(1)}):</b></td>
                    <td class="text-right">-₱${discountAmount.toFixed(2)}</td>
                `;
                
                // Insert before the total row
                const totalRow = priceSummary.querySelector('tr:last-child');
                if (totalRow) {
                    totalRow.parentNode.insertBefore(discountRow, totalRow);
                    
                    // Update total amount text
                    const totalCell = totalRow.querySelector('td:last-child');
                    if (totalCell) {
                        totalCell.textContent = `₱${finalAmount.toFixed(2)}`;
                    }
                }
            }
        }
    }, 300);
    
    // Show the booking summary modal
    $('#bookingFormModal').modal('hide');
    $('#bookingSummaryModal').modal('show');
}

// Update the booking success message to show the correct discount percentage
function updateBookingSuccessMessage() {
    const discountSelect = document.getElementById('discountType');
    if (!discountSelect || !discountSelect.value) return;
    
    const selectedOption = discountSelect.options[discountSelect.selectedIndex];
    if (!selectedOption) return;
    
    // Get percentage from the data attribute
    const discountPercentage = selectedOption.dataset.percentage || 0;
    const discountType = discountSelect.value;
    
    // Update all success messages that might contain discount text
    const successMessages = document.querySelectorAll('.alert-success, .booking-success');
    successMessages.forEach(message => {
        // Only update messages that mention discount
        if (message.innerHTML.includes('discount')) {
            // Replace generic discount text with specific discount details
            let newMessage = message.innerHTML.replace(
                /(\d+)% (senior|pwd|student) discount/gi, 
                `${discountPercentage}% ${discountType} discount`
            );
            message.innerHTML = newMessage;
        }
    });
}

// Call this function when modal is shown
$(document).on('shown.bs.modal', '.modal', function() {
    setTimeout(updateBookingSuccessMessage, 500);
});
</script> 