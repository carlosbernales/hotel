<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../../fpdf/fpdf.php'); // Adjust path to where you'll store FPDF
require "db.php";

if (!isset($_SESSION['report_data'])) {
    die('No report data available');
}

$data = $_SESSION['report_data'];
$start_date = $_SESSION['report_start_date'];
$end_date = $_SESSION['report_end_date'];

class SalesReport extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Casa Estela Boutique Hotel & Cafe', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Sales Report', 0, 1, 'C');
    }
}

$pdf = new SalesReport();
$pdf->AddPage();

// Report Period
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Period: ' . date('M d, Y', strtotime($start_date)) . 
           ' to ' . date('M d, Y', strtotime($end_date)), 0, 1, 'C');

// Sales Summary
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Sales Summary', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(60, 8, 'Filtered Period Sales:', 0);
$pdf->Cell(0, 8, '₱ ' . number_format($data['daily_total'], 2), 0, 1);
$pdf->Cell(60, 8, 'Monthly Sales:', 0);
$pdf->Cell(0, 8, '₱ ' . number_format($data['monthly_total'], 2), 0, 1);
$pdf->Cell(60, 8, 'Annual Sales:', 0);
$pdf->Cell(0, 8, '₱ ' . number_format($data['annual_total'], 2), 0, 1);

// Orders Table
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Order Details', 0, 1, 'L');

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'ID', 1);
$pdf->Cell(35, 8, 'Date', 1);
$pdf->Cell(60, 8, 'Items', 1);
$pdf->Cell(30, 8, 'Amount', 1);
$pdf->Cell(25, 8, 'Payment', 1);
$pdf->Cell(20, 8, 'Status', 1);
$pdf->Ln();

// Table Content
$pdf->SetFont('Arial', '', 9);
foreach ($data['orders'] as $order) {
    // Format items text
    $items_text = '';
    foreach ($order['items'] as $item) {
        $item_text = $item['quantity'] . 'x ' . $item['name'];
        if (!empty($item['addons'])) {
            $item_text .= ' (+' . implode(', ', array_unique($item['addons'])) . ')';
        }
        $items_text .= $item_text . '; ';
    }
    $items_text = rtrim($items_text, '; ');
    
    $pdf->Cell(20, 8, $order['order_id'], 1);
    $pdf->Cell(35, 8, date('m/d/y H:i', strtotime($order['order_date'])), 1);
    $pdf->Cell(60, 8, substr($items_text, 0, 40) . (strlen($items_text) > 40 ? '...' : ''), 1);
    $pdf->Cell(30, 8, '₱ ' . number_format($order['total_amount'], 2), 1);
    $pdf->Cell(25, 8, $order['payment_method'], 1);
    $pdf->Cell(20, 8, $order['status'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'sales_report_' . date('Y-m-d_His') . '.pdf');

// Clear session data
unset($_SESSION['report_data']);
unset($_SESSION['report_start_date']);
unset($_SESSION['report_end_date']);
?> 