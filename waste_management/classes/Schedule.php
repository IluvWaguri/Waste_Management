<?php

require_once __DIR__ . '/Database.php';

class Schedule {
    private PDO $conn;
    private string $table = 'schedules';

    // Properties corresponding to the database columns
    public ?int $schedule_id = null;
    public string $resident_name = '';
    public string $street_address = '';
    public string $purok_name = '';
    public string $collection_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $waste_type = '';
    public string $status = 'Scheduled';

    // Constructor automatically runs Database::connect() when a new Schedule object is instantiated
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // CREATE: Inserts a new resident pickup record into MySQL
    public function addSchedule(): bool {
        $query = "INSERT INTO " . $this->table . " 
                  (resident_name, street_address, purok_name, collection_date, start_time, end_time, waste_type, status) 
                  VALUES (:resident_name, :street_address, :purok_name, :collection_date, :start_time, :end_time, :waste_type, :status)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize string inputs using htmlspecialchars and strip_tags to prevent XSS attacks before binding
        return $stmt->execute([
            ':resident_name'   => htmlspecialchars(strip_tags($this->resident_name)),
            ':street_address'  => htmlspecialchars(strip_tags($this->street_address)),
            ':purok_name'      => htmlspecialchars(strip_tags($this->purok_name)),
            ':collection_date' => $this->collection_date,
            ':start_time'      => $this->start_time,
            ':end_time'        => $this->end_time,
            ':waste_type'      => $this->waste_type,
            ':status'          => $this->status
        ]);
    }

    // READ: Fetches all schedules ordered chronologically
    public function getSchedules(): array {
        $query = "SELECT * FROM " . $this->table . " ORDER BY collection_date ASC, start_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // READ SINGLE: Fetches one specific record by its primary key (schedule_id)
    public function getScheduleById(int $id): ?array {
        $query = "SELECT * FROM " . $this->table . " WHERE schedule_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // UPDATE: Modifies an existing schedule entry using parameter binding
    public function updateSchedule(int $id): bool {
        $query = "UPDATE " . $this->table . " 
                  SET resident_name = :resident_name,
                      street_address = :street_address,
                      purok_name = :purok_name, 
                      collection_date = :collection_date, 
                      start_time = :start_time, 
                      end_time = :end_time, 
                      waste_type = :waste_type, 
                      status = :status 
                  WHERE schedule_id = :id";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':resident_name'   => htmlspecialchars(strip_tags($this->resident_name)),
            ':street_address'  => htmlspecialchars(strip_tags($this->street_address)),
            ':purok_name'      => htmlspecialchars(strip_tags($this->purok_name)),
            ':collection_date' => $this->collection_date,
            ':start_time'      => $this->start_time,
            ':end_time'        => $this->end_time,
            ':waste_type'      => $this->waste_type,
            ':status'          => $this->status,
            ':id'              => $id
        ]);
    }

    // DELETE: Removes a schedule entry from the table by ID
    public function deleteSchedule(int $id): bool {
        $query = "DELETE FROM " . $this->table . " WHERE schedule_id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }

    // SEARCH: Performs wildcard matching across resident names, street addresses, puroks, or statuses
    public function searchSchedules(string $keyword): array {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE resident_name LIKE ? 
                     OR street_address LIKE ? 
                     OR purok_name LIKE ? 
                     OR waste_type LIKE ? 
                     OR status LIKE ? 
                  ORDER BY collection_date ASC";

        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        
        $stmt->execute([
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm
        ]);
        
        return $stmt->fetchAll();
    }
}