<?php
class SeasonalEffects {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTable();
    }
    
    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS seasonal_effects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            effect_type ENUM('snow', 'hearts', 'fireworks') NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $this->pdo->exec($sql);
            
            // Insert default effects if table is empty
            $count = $this->pdo->query("SELECT COUNT(*) FROM seasonal_effects")->fetchColumn();
            if ($count == 0) {
                $this->insertDefaultEffects();
            }
        } catch(PDOException $e) {
            error_log("Error creating seasonal_effects table: " . $e->getMessage());
        }
    }
    
    private function insertDefaultEffects() {
        $effects = [
            [
                'Christmas Snow',
                '2025-12-01',
                '2024-12-31',
                'snow'
            ],
            [
                'Valentine Hearts',
                '2024-02-14',
                '2024-02-14',
                'hearts'
            ],
            [
                'New Year Fireworks',
                '2024-01-01',
                '2024-01-01',
                'fireworks'
            ]
        ];
        
        $sql = "INSERT INTO seasonal_effects (name, start_date, end_date, effect_type) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($effects as $effect) {
            try {
                $stmt->execute($effect);
            } catch(PDOException $e) {
                error_log("Error inserting default effect: " . $e->getMessage());
            }
        }
    }
    
    public function getCurrentEffects() {
        $sql = "SELECT * FROM seasonal_effects 
                WHERE is_active = TRUE 
                AND CURDATE() BETWEEN start_date AND end_date";
        
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting current effects: " . $e->getMessage());
            return [];
        }
    }
} 