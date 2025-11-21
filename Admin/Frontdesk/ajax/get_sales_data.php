<?php
require_once '../includes/init.php';
require_once '../db.php';

header('Content-Type: application/json');

$startDate = $_POST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_POST['end_date'] ?? date('Y-m-d');
$reportType = $_POST['report_type'] ?? 'overall';

try {
    $response = [
        'summary' => [],
        'charts' => [],
        'table' => []
    ];

    // Get data based on report type
    $result = getSalesData($startDate, $endDate, $reportType);
    
    switch($reportType) {
        case 'overall':
            $totalRevenue = 0;
            $totalCosts = 0;
            $totalProfit = 0;
            $chartData = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $totalRevenue += $row['total_revenue'];
                $totalCosts += $row['total_costs'];
                $totalProfit += $row['total_profit'];
                
                $chartData[] = [
                    'date' => $row['sale_date'],
                    'revenue' => $row['total_revenue'],
                    'costs' => $row['total_costs'],
                    'profit' => $row['total_profit']
                ];
                
                $response['table'][] = [
                    'date' => date('Y-m-d', strtotime($row['sale_date'])),
                    'revenue' => '₱' . number_format($row['total_revenue'], 2),
                    'costs' => '₱' . number_format($row['total_costs'], 2),
                    'profit' => '₱' . number_format($row['total_profit'], 2)
                ];
            }
            
            $response['summary'] = [
                'Total Revenue' => [
                    'value' => number_format($totalRevenue, 2),
                    'prefix' => '₱',
                    'color' => 'primary'
                ],
                'Total Costs' => [
                    'value' => number_format($totalCosts, 2),
                    'prefix' => '₱',
                    'color' => 'warning'
                ],
                'Net Profit' => [
                    'value' => number_format($totalProfit, 2),
                    'prefix' => '₱',
                    'color' => 'success'
                ],
                'Profit Margin' => [
                    'value' => number_format(($totalProfit / $totalRevenue) * 100, 1),
                    'suffix' => '%',
                    'color' => 'info'
                ]
            ];
            
            $response['charts'] = [
                'revenue' => $chartData
            ];
            break;
            
        case 'frontdesk':
            while ($row = mysqli_fetch_assoc($result)) {
                $response['table'][] = [
                    'date' => date('Y-m-d', strtotime($row['booking_date'])),
                    'room_type' => $row['room_type_name'],
                    'room_number' => $row['room_number'],
                    'guest_name' => $row['guest_name'],
                    'check_in' => date('Y-m-d', strtotime($row['check_in'])),
                    'check_out' => date('Y-m-d', strtotime($row['check_out'])),
                    'nights' => $row['nights'],
                    'amount' => '₱' . number_format($row['total_price'], 2),
                    'status' => $row['payment_status']
                ];
            }
            
            // Calculate summary statistics
            $totalBookings = count($response['table']);
            $totalRevenue = array_sum(array_map(function($row) {
                return (float)str_replace(['₱', ','], '', $row['amount']);
            }, $response['table']));
            
            $response['summary'] = [
                'Total Bookings' => [
                    'value' => $totalBookings,
                    'color' => 'primary'
                ],
                'Total Revenue' => [
                    'value' => number_format($totalRevenue, 2),
                    'prefix' => '₱',
                    'color' => 'success'
                ],
                'Average per Booking' => [
                    'value' => number_format($totalRevenue / ($totalBookings ?: 1), 2),
                    'prefix' => '₱',
                    'color' => 'info'
                ]
            ];
            break;
            
        case 'cashier':
            while ($row = mysqli_fetch_assoc($result)) {
                $response['table'][] = [
                    'date' => date('Y-m-d', strtotime($row['order_date'])),
                    'order_id' => $row['order_id'],
                    'type' => $row['order_type'],
                    'pickup_time' => $row['pickup_time'],
                    'amount' => '₱' . number_format($row['total_amount'], 2),
                    'payment' => $row['payment_method'],
                    'status' => $row['status'],
                    'instructions' => $row['special_instructions'],
                    'ordered_by' => $row['ordered_by']
                ];
            }
            
            // Calculate summary statistics
            $totalOrders = count($response['table']);
            $totalRevenue = array_sum(array_map(function($row) {
                return (float)str_replace(['₱', ','], '', $row['amount']);
            }, $response['table']));
            
            $response['summary'] = [
                'Total Orders' => [
                    'value' => $totalOrders,
                    'color' => 'primary'
                ],
                'Total Revenue' => [
                    'value' => number_format($totalRevenue, 2),
                    'prefix' => '₱',
                    'color' => 'success'
                ],
                'Average per Order' => [
                    'value' => number_format($totalRevenue / ($totalOrders ?: 1), 2),
                    'prefix' => '₱',
                    'color' => 'info'
                ]
            ];
            break;
            
        case 'admin':
            $totalRevenue = 0;
            $totalCosts = 0;
            $totalProfit = 0;
            
            while ($row = mysqli_fetch_assoc($result)) {
                $response['table'][] = [
                    'category' => $row['category'],
                    'revenue' => '₱' . number_format($row['revenue'], 2),
                    'costs' => '₱' . number_format($row['costs'], 2),
                    'profit' => '₱' . number_format($row['profit'], 2),
                    'growth' => $row['growth'] . '%'
                ];
                
                $totalRevenue += $row['revenue'];
                $totalCosts += $row['costs'];
                $totalProfit += $row['profit'];
            }
            
            $response['summary'] = [
                'Total Revenue' => [
                    'value' => number_format($totalRevenue, 2),
                    'prefix' => '₱',
                    'color' => 'primary'
                ],
                'Total Costs' => [
                    'value' => number_format($totalCosts, 2),
                    'prefix' => '₱',
                    'color' => 'warning'
                ],
                'Net Profit' => [
                    'value' => number_format($totalProfit, 2),
                    'prefix' => '₱',
                    'color' => 'success'
                ],
                'Profit Margin' => [
                    'value' => number_format(($totalProfit / $totalRevenue) * 100, 1),
                    'suffix' => '%',
                    'color' => 'info'
                ]
            ];
            
            $response['charts'] = [
                'revenue' => array_map(function($row) {
                    return [
                        'category' => $row['category'],
                        'revenue' => (float)str_replace(['₱', ','], '', $row['revenue']),
                        'profit' => (float)str_replace(['₱', ','], '', $row['profit'])
                    ];
                }, $response['table'])
            ];
            break;
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 