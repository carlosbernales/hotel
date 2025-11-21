<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TableBookingController extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        // Load necessary models
        $this->load->model('BookingModel');
        $this->load->library('form_validation');
    }

    public function processBooking() {
        // Set JSON header
        header('Content-Type: application/json');
        
        // Validate required fields
        $this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('number_of_guests', 'Number of Guests', 'required|numeric');
        $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
        $this->form_validation->set_rules('total_amount', 'Total Amount', 'required|numeric');
        $this->form_validation->set_rules('amount_paid', 'Amount Paid', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors()
            ]);
            return;
        }

        // Get POST data
        $bookingData = [
            'time' => $this->input->post('time'),
            'number_of_guests' => $this->input->post('number_of_guests'),
            'payment_method' => $this->input->post('payment_method'),
            'total_amount' => $this->input->post('total_amount'),
            'amount_paid' => $this->input->post('amount_paid'),
            'special_requests' => $this->input->post('special_requests'),
            'booking_date' => date('Y-m-d'),
            'status' => 'pending'
        ];

        try {
            // Save booking to database
            $booking_id = $this->BookingModel->createBooking($bookingData);
            
            if ($booking_id) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Booking created successfully',
                    'booking_id' => $booking_id
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create booking'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred while processing your booking'
            ]);
        }
    }
}