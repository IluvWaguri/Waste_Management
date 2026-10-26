<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'classes/Database.php';
require_once 'classes/Schedule.php';

$database = new Database();
$pdo = $database->connect();

$scheduleObj = new Schedule();
$id = $_GET['id'] ?? null;

if ($id) {
    // Fetch schedule info for the transaction log before deleting
    $data = $scheduleObj->getScheduleById((int)$id);
    
    if ($data) {
        $residentName = $data['resident_name'];
        
        // Delete the schedule record
        if ($scheduleObj->deleteSchedule((int)$id)) {
            // Record the activity in transactions log
            $userId = $_SESSION['user_id'];
            $actionDetails = "Deleted schedule ID: " . $id . " for " . $residentName;

            $logStmt = $pdo->prepare("INSERT INTO transactions (user_id, action_details) VALUES (?, ?)");
            $logStmt->execute([$userId, $actionDetails]);
        }
    }
}

// Redirect back to dashboard
header("Location: index.php");
exit;