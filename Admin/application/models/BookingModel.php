<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingModel extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function createBooking($data) {
        $this->db->insert('table_bookings', $data);
        return $this->db->insert_id();
    }
} 