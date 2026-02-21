<?php
require_once 'db_con.php';

class MaintenanceConfig {
    private $pdo;
    private $settings;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadSettings();
    }

    private function loadSettings() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM maintenance_settings LIMIT 1");
            $this->settings = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If there's an error, default to disabled maintenance mode
            $this->settings = [
                'is_enabled' => false,
                'message' => 'Site under maintenance',
                'allowed_ips' => '127.0.0.1',
                'start_time' => null,
                'end_time' => null
            ];
        }
    }

    public function isMaintenanceMode() {
        if (!$this->settings['is_enabled']) {
            return false;
        }

        // Check if maintenance is scheduled
        if ($this->settings['start_time'] && $this->settings['end_time']) {
            $now = new DateTime();
            $start = new DateTime($this->settings['start_time']);
            $end = new DateTime($this->settings['end_time']);
            
            return $now >= $start && $now <= $end;
        }

        return true;
    }

    public function isAllowedIP() {
        $visitor_ip = $_SERVER['REMOTE_ADDR'];
        $allowed_ips = array_map('trim', explode(',', $this->settings['allowed_ips']));
        return in_array($visitor_ip, $allowed_ips);
    }

    public function getMessage() {
        return $this->settings['message'];
    }

    public function getEndTime() {
        return $this->settings['end_time'];
    }
}

// Initialize the maintenance configuration
$maintenanceConfig = new MaintenanceConfig($pdo); 