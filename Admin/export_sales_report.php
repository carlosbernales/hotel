<?php
require_once 'includes/init.php';
require_once 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get export type (pdf or excel)
$exportType = $_POST['export_type'] ?? 'excel';
$reportType = $_POST['report_type'] ?? 'all';
$rangeType = $_POST['range_type'] ?? 'daily';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

// Set date range based on range type
$today = date('Y-m-d');
$dateCondition = '';

switch($rangeType) {
    case 'daily':
        $dateCondition = "DATE(created_at) = CURDATE()";
        $orderDateCondition = "DATE(order_date) = CURDATE()";
        break;
    case 'weekly':
        $dateCondition = "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
        $orderDateCondition = "YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $dateCondition = "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
        $orderDateCondition = "MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
        break;
    case 'yearly':
        $dateCondition = "YEAR(created_at) = YEAR(CURDATE())";
        $orderDateCondition = "YEAR(order_date) = YEAR(CURDATE())";
        break;
    case 'custom':
        if (!empty($startDate) && !empty($endDate)) {
            $dateCondition = "DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
            $orderDateCondition = "DATE(order_date) BETWEEN '$startDate' AND '$endDate'";
        }
        break;
}

// Function to get all sales data
function getAllSalesData($con, $reportType, $dateCondition, $orderDateCondition) {
    $data = [];
    
    // Base query for all reports
    $query = "SELECT 
                'Room Booking' as booking_type,
                b.booking_id,
                CONCAT(b.first_name, ' ', b.last_name) as customer_name,
                b.check_in as booking_date,
                b.total_amount,
                b.amount_paid,
                b.payment_method,
                b.status,
                COALESCE(b.user_types, 'frontdesk') as source,
                b.created_at
              FROM bookings b
              WHERE (b.status = 'Checked Out' OR b.status = 'checked_out' OR b.status = 'completed' OR b.status = 'Completed' 
                    OR b.status IN ('pending', 'Pending', 'confirmed', 'Confirmed', 'Walkin', 'walkin'))";
    
    // Add date condition if provided
    if (!empty($dateCondition)) {
        $query .= " AND ($dateCondition)";
    }
    
    // Add report type filter
    if ($reportType === 'admin') {
        $query .= " AND (b.user_types = 'admin' OR b.user_types IS NULL)";
    } elseif ($reportType === 'frontdesk') {
        $query .= " AND b.user_types = 'frontdesk'";
    } elseif ($reportType === 'cashier') {
        // For cashier, we'll get POS orders separately
        $query = "";
    }
    
    // Get room bookings if not cashier report
    if ($reportType !== 'cashier') {
        $roomBookings = mysqli_query($con, $query);
        if ($roomBookings) {
            while ($row = mysqli_fetch_assoc($roomBookings)) {
                $data[] = $row;
            }
        }
    }
    
    // Get POS orders for cashier or all reports
    if ($reportType === 'cashier' || $reportType === 'all') {
        $posQuery = "SELECT 
                        'POS Order' as booking_type,
                        id as booking_id,
                        COALESCE(customer_name, 'Walk-in Customer') as customer_name,
                        order_date as booking_date,
                        total_amount,
                        amount_paid,
                        payment_method,
                        status,
                        'cashier' as source,
                        order_date as created_at
                    FROM orders 
                    WHERE status = 'finished'";
        
        if (!empty($orderDateCondition)) {
            $posQuery .= " AND ($orderDateCondition)";
        }
        
        $posOrders = mysqli_query($con, $posQuery);
        if ($posOrders) {
            while ($row = mysqli_fetch_assoc($posOrders)) {
                $data[] = $row;
            }
        }
    }
    
    return $data;
}

// Get all sales data
$salesData = getAllSalesData($con, $reportType, $dateCondition, $orderDateCondition);

// Generate filename with timestamp
$filename = 'sales_report_' . date('Y-m-d_H-i-s');

if ($exportType === 'pdf') {
    // Set headers for PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
    
    // Include TCPDF library
    require_once('tcpdf/tcpdf.php');
    
    // Create new PDF document
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Casa Estela');
    $pdf->SetAuthor('Casa Estela');
    $pdf->SetTitle('Sales Report');
    
    // Set default header data
    $pdf->SetHeaderData('', 0, 'Sales Report', 'Generated on ' . date('Y-m-d H:i:s'));
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    // Set margins
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add title
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Add filters info
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'Report Type: ' . ucfirst($reportType), 0, 1);
    $pdf->Cell(0, 8, 'Date Range: ' . ucfirst($rangeType), 0, 1);
    if ($rangeType === 'custom') {
        $pdf->Cell(0, 8, 'From: ' . $startDate . ' To: ' . $endDate, 0, 1);
    }
    $pdf->Ln(5);
    
    // Create table header
    $header = array('Type', 'ID', 'Customer', 'Date', 'Amount', 'Paid', 'Method', 'Status', 'Source');
    $w = array(30, 20, 50, 30, 25, 25, 30, 25, 20);
    
    // Set table header style
    $pdf->SetFillColor(218, 165, 32);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);
    $pdf->SetFont('', 'B');
    
    // Add table header
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Reset font and colors
    $pdf->SetFont('');
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0);
    
    // Add table data
    $fill = false;
    $totalAmount = 0;
    
    foreach($salesData as $row) {
        $pdf->Cell($w[0], 6, $row['booking_type'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 6, $row['booking_id'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[2], 6, $row['customer_name'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[3], 6, date('Y-m-d', strtotime($row['booking_date'])), 'LR', 0, 'C', $fill);
        $pdf->Cell($w[4], 6, '₱' . number_format($row['total_amount'], 2), 'LR', 0, 'R', $fill);
        $pdf->Cell($w[5], 6, '₱' . number_format($row['amount_paid'], 2), 'LR', 0, 'R', $fill);
        $pdf->Cell($w[6], 6, $row['payment_method'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[7], 6, ucfirst($row['status']), 'LR', 0, 'C', $fill);
        $pdf->Cell($w[8], 6, ucfirst($row['source']), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        
        $fill = !$fill;
        $totalAmount += $row['total_amount'];
    }
    
    // Add total row
    $pdf->SetFont('', 'B');
    $pdf->Cell(array_sum($w) - $w[4], 6, 'Total:', 1, 0, 'R', true);
    $pdf->Cell($w[4], 6, '₱' . number_format($totalAmount, 2), 1, 0, 'R', true);
    
    // Close and output PDF
    $pdf->Output($filename . '.pdf', 'D');
    
} else {
    // Export to Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    
    // Include PHPExcel library
    require_once 'PHPExcel/PHPExcel.php';
    
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    
    // Set document properties
    $objPHPExcel->getProperties()
        ->setCreator('Casa Estela')
        ->setLastModifiedBy('Casa Estela')
        ->setTitle('Sales Report')
        ->setSubject('Sales Report')
        ->setDescription('Sales Report generated by Casa Estela');
    
    // Add worksheet
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    $sheet->setTitle('Sales Report');
    
    // Add header
    $header = array('Type', 'ID', 'Customer', 'Date', 'Amount', 'Paid', 'Method', 'Status', 'Source');
    $col = 0;
    
    // Set header style
    $headerStyle = array(
        'font' => array('bold' => true),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'DAA520')
        ),
        'borders' => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
        )
    );
    
    // Add header row
    foreach ($header as $h) {
        $sheet->setCellValueByColumnAndRow($col++, 1, $h);
    }
    
    // Apply header style
    $sheet->getStyle('A1:' . PHPExcel_Cell::stringFromColumnIndex(count($header) - 1) . '1')->applyFromArray($headerStyle);
    
    // Add data rows
    $rowNum = 2;
    $totalAmount = 0;
    
    foreach ($salesData as $row) {
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['booking_type']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['booking_id']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['customer_name']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, PHPExcel_Shared_Date::PHPToExcel(new DateTime($row['booking_date'])));
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['total_amount']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['amount_paid']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, $row['payment_method']);
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, ucfirst($row['status']));
        $sheet->setCellValueByColumnAndRow($col++, $rowNum, ucfirst($row['source']));
        
        // Format date column
        $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        
        // Format amount columns
        $sheet->getStyle('E' . $rowNum . ':F' . $rowNum)->getNumberFormat()->setFormatCode('₱#,##0.00');
        
        $totalAmount += $row['total_amount'];
        $rowNum++;
    }
    
    // Add total row
    $sheet->setCellValue('D' . $rowNum, 'Total:');
    $sheet->setCellValue('E' . $rowNum, $totalAmount);
    $sheet->getStyle('D' . $rowNum . ':E' . $rowNum)->getFont()->setBold(true);
    $sheet->getStyle('E' . $rowNum)->getNumberFormat()->setFormatCode('₱#,##0.00');
    
    // Auto-size columns
    foreach (range('A', $sheet->getHighestDataColumn()) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Redirect output to a client's web browser (Excel2007)
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
}

// Close database connection
mysqli_close($con);
