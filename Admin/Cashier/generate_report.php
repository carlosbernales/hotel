<?php
session_start();
require_once('db.php');

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include TCPDF library directly
require_once(__DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    // Page header - Professional Design
    public function Header($page = '') {
        // Set document information
        $this->SetCreator('Casa Estela');
        $this->SetAuthor('Casa Estela');
        $this->SetTitle('Sales Analysis Report');
        
        // Set logo path
        $logo_path = __DIR__ . '/img/download.jfif';
        $has_logo = file_exists($logo_path);
        
        // Set colors
        $header_bg = array(245, 247, 250);  // Light blue-gray background
        $primary_color = array(30, 64, 175); // Dark blue
        $text_color = array(31, 41, 55);    // Dark gray text
        $accent_color = array(99, 102, 241); // Indigo accent
        
        // Calculate header height based on content
        $header_height = 55; // Increased height to prevent overlap
        
        // Add header background with subtle gradient
        $this->SetFillColorArray($header_bg);
        $this->Rect(0, 0, 210, $header_height, 'F');
        
        // Add subtle pattern to header background
        $this->SetDrawColor(220, 231, 255);
        for ($i = 0; $i < 210; $i += 10) {
            $this->Line($i, 0, $i + 5, $header_height);
        }
        
        // Reset colors for content
        $this->SetTextColorArray($text_color);
        
        // Start header content container with more top margin
        $this->SetY(12);
        
        // Add logo if exists with a subtle border
        if ($has_logo) {
            // Create a container with border for the logo
            $logo_size = 20; // Slightly smaller logo
            $logo_x = 15;
            $logo_y = 12;
            
            $this->SetDrawColorArray(array(203, 213, 225));
            $this->SetFillColor(255, 255, 255);
            $this->RoundedRect($logo_x, $logo_y, $logo_size, $logo_size, 3, '1111', 'DF');
            
            // Add the logo with proper alignment
            $this->Image($logo_path, 
                $logo_x + 2, 
                $logo_y + 2, 
                $logo_size - 4, 
                $logo_size - 4, 
                '', 
                '', 
                '', 
                false, 
                300, 
                '', 
                false, 
                false, 
                0, 
                false, 
                false, 
                false
            );
            
            // Set X position for text after logo
            $text_x = $logo_x + $logo_size + 10;
        } else {
            $text_x = 15;
        }
        
        // Set position for text with proper line height
        $this->SetX($text_x);
        
        // Main title with better typography and spacing
        $this->SetFont('helvetica', 'B', 16); // Slightly smaller font
        $this->SetTextColorArray($primary_color);
        $this->Cell(0, 5, 'CASA ESTELA', 0, 1, 'L');
        
        // Subtitle with accent color
        $this->SetX($text_x);
        $this->SetFont('helvetica', 'B', 12); // Slightly smaller font
        $this->SetTextColorArray($accent_color);
        $this->Cell(0, 5, 'SALES ANALYSIS REPORT', 0, 1, 'L');
        
        // Add some space before metadata
        $this->Ln(2);
        
        // Report metadata with adjusted positioning
        $this->SetX($text_x);
        $this->SetFont('helvetica', '', 8); // Smaller font for metadata
        $this->SetTextColorArray(array(100, 116, 139)); // Cool gray
        
        // Report ID and date in one line with proper spacing
        $report_id = 'RPT-' . strtoupper(uniqid());
        $this->Cell(0, 4, 'Report #' . $report_id . ' • Generated: ' . date('M j, Y g:i A'), 0, 1, 'L');
        
        // Report period if available
        if (isset($_SESSION['report_start_date']) && isset($_SESSION['report_end_date'])) {
            $this->SetX($text_x);
            $this->Cell(0, 4, 'Period: ' . date('M j, Y', strtotime($_SESSION['report_start_date'])) . ' - ' . 
                date('M j, Y', strtotime($_SESSION['report_end_date'])), 0, 1, 'L');
        }
        
        // Add a subtle separator line with more space
        $this->Ln(3);
        $this->SetDrawColorArray(array(220, 231, 255)); // Lighter separator
        $this->Line(15, $header_height - 5, 195, $header_height - 5);
        $this->SetLineWidth(0.5);
        $this->Line(15, 45, 195, 45);
        
        // Add a small accent line under the header
        $this->SetDrawColorArray($accent_color);
        $this->SetLineWidth(2);
        $this->Line(15, 46, 50, 46);
        
        // Reset Y position for content with proper spacing
        $this->SetY(55);
    }

    // Page footer - Professional Design
    public function Footer() {
        $primary_color = array(30, 64, 175); // Dark blue
        $text_color = array(100, 116, 139);  // Cool gray
        
        // Position at 15 mm from bottom
        $this->SetY(-18);
        
        // Add a subtle top border
        $this->SetDrawColorArray(array(226, 232, 240));
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        
        // Set font and color
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColorArray($text_color);
        
        // Footer content with better spacing
        $this->SetY(-15);
        
        // Left side - Company info
        $this->SetX(15);
        $this->Cell(80, 6, 'Casa Estela • Sales Analysis Report', 0, 0, 'L');
        
        // Center - Page number
        $this->SetX(0);
        $this->Cell(0, 6, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, 0, 'C');
        
        // Right side - Timestamp
        $this->SetX(-60);
        $this->Cell(45, 6, 'Generated: ' . date('M j, Y g:i A'), 0, 0, 'R');
        
        // Add a small version number or system info
        $this->SetY(-10);
        $this->SetX(15);
        $this->SetFont('helvetica', 'I', 7);
        $this->SetTextColorArray(array(148, 163, 184)); // Lighter gray
        $this->Cell(0, 4, 'POS v2.0 • Report ID: RPT-' . strtoupper(uniqid()), 0, 0, 'L');
    }
}

// Create new PDF document with proper encoding for special characters
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set default font that supports the peso sign (₱)
$pdf->SetFont('dejavusans', '', 10, '', true); // The last parameter enables font subsetting

// Set the Peso sign (₱) as a constant for easy reference
define('PESO_SIGN', '₱');

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Casa Estela');
$pdf->SetTitle('Sales Report');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts - ensure they support the peso sign
$pdf->setHeaderFont(Array('dejavusans', '', 10, '', true));
$pdf->setFooterFont(Array('dejavusans', '', 8, '', true));

// Set default monospaced font that supports the peso sign
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set a font that supports the peso sign
$pdf->SetFont('dejavusans', '', 12, '', true); // 'dejavusans' supports the peso sign

// Get the report data from POST or session
$start_date = $_POST['start_date'] ?? $_SESSION['report_start_date'] ?? '';
$end_date = $_POST['end_date'] ?? $_SESSION['report_end_date'] ?? '';

// Check for selected orders
$selected_orders = isset($_POST['selected_orders']) ? explode(',', $_POST['selected_orders']) : [];
$has_selected_orders = !empty($selected_orders);

// Store in session for later use
$_SESSION['report_start_date'] = $start_date;
$_SESSION['report_end_date'] = $end_date;

// Add report period and selection info
if ($has_selected_orders) {
    $pdf->Cell(0, 10, 'Selected Sales Report: ' . count($selected_orders) . ' order(s)', 0, 1);
    if ($start_date && $end_date) {
        $pdf->Cell(0, 10, 'Period: ' . $start_date . ' to ' . $end_date, 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'Report Period: ' . $start_date . ' to ' . $end_date, 0, 1);
}
$pdf->Ln(5);

// Define colors
$primary_color = array(30, 64, 175);   // Dark blue
$accent_color = array(99, 102, 241);   // Indigo
$success_color = array(16, 185, 129);  // Green
$danger_color = array(239, 68, 68);    // Red
$text_color = array(31, 41, 55);       // Dark gray
$muted_text = array(100, 116, 139);    // Cool gray

// Add report summary section
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColorArray($primary_color);
$pdf->Cell(0, 8, 'Sales Overview', 0, 1, 'L');

// Add a subtle subtitle
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColorArray($muted_text);
$pdf->Cell(0, 6, 'Summary of sales performance for the selected period', 0, 1, 'L');
$pdf->Ln(5);

// Create an info card for the report period
$pdf->SetDrawColorArray(array(226, 232, 240));
$pdf->SetFillColorArray(array(248, 250, 252));
$pdf->RoundedRect(15, $pdf->GetY(), 180, 25, 3, '1111', 'DF');

// Add period information
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColorArray($primary_color);
$pdf->SetXY(20, $pdf->GetY() + 7);
$pdf->Cell(30, 6, 'REPORT PERIOD:', 0, 0, 'L');

$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColorArray($text_color);
$pdf->Cell(0, 6, date('F j, Y', strtotime($start_date)) . ' - ' . date('F j, Y', strtotime($end_date)), 0, 1, 'L');

// Add selected orders info if applicable
if ($has_selected_orders) {
    $pdf->SetX(20);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColorArray($muted_text);
    $pdf->Cell(0, 6, 'Selected Orders: ' . count($selected_orders) . ' order(s)', 0, 1, 'L');
}

$pdf->Ln(12);

// Get sales data from database
$query = "SELECT 
    COALESCE(SUM(total_amount + discount_amount), 0) as gross_sales,
    COALESCE(SUM(discount_amount), 0) as total_discounts,
    COALESCE(SUM(total_amount), 0) as net_sales,
    COUNT(*) as transaction_count
    FROM orders 
    WHERE status = 'finished'";

// Add filter conditions
$params = [];
$param_types = '';

if ($has_selected_orders) {
    $placeholders = implode(',', array_fill(0, count($selected_orders), '?'));
    $query .= " AND id IN ($placeholders)";
    $params = array_merge($params, $selected_orders);
    $param_types .= str_repeat('i', count($selected_orders));
}

if ($start_date && $end_date) {
    $query .= " AND DATE(order_date) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $param_types .= 'ss';
}

try {
    $stmt = $conn->prepare($query);
    
    // Bind parameters if we have any
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $salesData = $result->fetch_assoc();

    // Add sales data to PDF with professional formatting
    
    // Table header with better styling
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColorArray($primary_color);
    $pdf->SetTextColor(255, 255, 255); // White text for header
    $pdf->SetDrawColorArray(array(203, 213, 225)); // Light border
    
    // Header cells with padding
    $pdf->Cell(110, 12, 'METRIC', 1, 0, 'L', true);
    $pdf->Cell(35, 12, 'VALUE', 1, 0, 'R', true);
    $pdf->Cell(35, 12, 'PERCENTAGE', 1, 1, 'R', true);
    
    // Reset text color for data rows
    $pdf->SetTextColorArray($text_color);
    $pdf->SetFont('helvetica', '', 10);
    
    // Calculate percentages
    $gross_sales = floatval($salesData['gross_sales']);
    $total_discounts = floatval($salesData['total_discounts']);
    $net_sales = floatval($salesData['net_sales']);
    $transaction_count = intval($salesData['transaction_count']);
    
    // Function to add a data row
    $addDataRow = function($label, $value, $is_negative = false, $is_highlighted = false, $percentage = null) use ($pdf, $text_color, $muted_text, $primary_color, $success_color, $danger_color) {
        $pdf->SetFont('helvetica', $is_highlighted ? 'B' : '', 10);
        
        // Left column - Label
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColorArray($is_highlighted ? $primary_color : $text_color);
        $pdf->Cell(110, 10, $label, 'LRB', 0, 'L', true);
        
        // Middle column - Value
        $pdf->SetTextColorArray($is_negative ? $danger_color : ($is_highlighted ? $success_color : $text_color));
        // Use the PESO_SIGN constant and ensure proper number formatting
        $formatted_value = ($is_negative ? '-' : '') . PESO_SIGN . ' ' . number_format($value, 2);
        $pdf->Cell(35, 10, $formatted_value, 'RB', 0, 'R', true);
        
        // Right column - Percentage (if provided)
        if ($percentage !== null) {
            $pdf->SetTextColorArray($muted_text);
            $pdf->Cell(35, 10, $percentage . '%', 'RB', 1, 'R', true);
        } else {
            $pdf->Cell(35, 10, '', 'RB', 1, 'R', true);
        }
        
        // Reset colors
        $pdf->SetTextColorArray($text_color);
    };
    
    // Add data rows with alternating background
    $addDataRow('Gross Sales', $gross_sales, false, false, 100);
    
    // Calculate discount percentage
    $discount_percentage = $gross_sales > 0 ? round(($total_discounts / $gross_sales) * 100, 1) : 0;
    $addDataRow('Total Discounts', $total_discounts, true, false, $discount_percentage);
    
    // Add a divider line
    $pdf->SetDrawColorArray(array(226, 232, 240));
    $pdf->SetLineWidth(0.3);
    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    $pdf->Ln(1);
    
    // Net Sales (highlighted)
    $addDataRow('Net Sales', $net_sales, false, true);
    
    // Add a small space before the transaction count
    $pdf->Ln(2);
    
    // Transaction Count
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Cell(110, 10, 'Number of Transactions', 'LRB', 0, 'L', true);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColorArray($primary_color);
    $pdf->Cell(70, 10, number_format($transaction_count), 'RB', 1, 'R', true);
    
    // Add a small space after the table
    $pdf->Ln(15);
    
    // Add a summary box
    $pdf->SetDrawColorArray(array(203, 213, 225));
    $pdf->SetFillColorArray(array(248, 250, 252));
    $pdf->RoundedRect(15, $pdf->GetY(), 180, 20, 3, '1111', 'DF');
    
    // Add summary text
    $pdf->SetFont('helvetica', 'I', 9);
    $pdf->SetTextColorArray($muted_text);
    $pdf->SetXY(20, $pdf->GetY() + 6);
    $pdf->MultiCell(170, 4, "This report was generated on " . date('F j, Y \a\t g:i A') . " and includes all completed transactions" . 
        ($has_selected_orders ? ' for the selected orders' : ' for the specified period') . ". The data is accurate as of the time of generation.", 0, 'L');
    
    $pdf->Ln(20);
    
    // Add detailed transactions if needed
    $pdf->Ln(10);
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 10, 'Detailed Transactions', 0, 1);
    $pdf->SetFont('dejavusans', '', 10);
    
    // Create table headers
    $pdf->Cell(40, 7, 'Date', 1);
    $pdf->Cell(30, 7, 'Order ID', 1);
    $pdf->Cell(40, 7, 'Amount', 1);
    $pdf->Cell(40, 7, 'Payment Method', 1);
    $pdf->Cell(40, 7, 'Order Type', 1);
    $pdf->Ln();
    
    // Get detailed transactions
    $detailQuery = "SELECT order_date, id, total_amount, payment_method, order_type 
                   FROM orders 
                   WHERE status = 'finished'";
                   
    // Reset params for detail query
    $detail_params = [];
    $detail_param_types = '';
    
    // Add filter conditions
    if ($has_selected_orders) {
        $placeholders = implode(',', array_fill(0, count($selected_orders), '?'));
        $detailQuery .= " AND id IN ($placeholders)";
        $detail_params = array_merge($detail_params, $selected_orders);
        $detail_param_types .= str_repeat('i', count($selected_orders));
    }
    
    if ($start_date && $end_date) {
        $detailQuery .= " AND DATE(order_date) BETWEEN ? AND ?";
        $detail_params[] = $start_date;
        $detail_params[] = $end_date;
        $detail_param_types .= 'ss';
    }
    
    $detailQuery .= " ORDER BY order_date DESC";
    
    $stmt = $conn->prepare($detailQuery);
    
    // Bind parameters if we have any
    if (!empty($detail_params)) {
        $stmt->bind_param($detail_param_types, ...$detail_params);
    }
    $stmt->execute();
    $details = $stmt->get_result();
    
    while ($row = $details->fetch_assoc()) {
        $pdf->Cell(40, 6, date('Y-m-d H:i', strtotime($row['order_date'])), 1);
        $pdf->Cell(30, 6, $row['id'], 1);
        $pdf->Cell(40, 6, '₱' . number_format($row['total_amount'], 2), 1);
        $pdf->Cell(40, 6, ucfirst($row['payment_method']), 1);
        $pdf->Cell(40, 6, ucfirst($row['order_type']), 1);
        $pdf->Ln();
    }
    
} catch (Exception $e) {
    error_log("Error generating report: " . $e->getMessage());
    $pdf->Cell(0, 10, 'Error generating report data', 0, 1);
}

// Output the PDF
$pdf->Output('sales_report.pdf', 'D');

// Clear session data
unset($_SESSION['report_data']);
unset($_SESSION['report_start_date']);
unset($_SESSION['report_end_date']);
?> 