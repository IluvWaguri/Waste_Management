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

if (!$id) {
    header("Location: index.php");
    exit;
}

$data = $scheduleObj->getScheduleById((int)$id);

if (!$data) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleObj->resident_name = $_POST['resident_name'];
    $scheduleObj->street_address = $_POST['street_address'];
    $scheduleObj->purok_name = $_POST['purok_name'];
    $scheduleObj->collection_date = $_POST['collection_date'];
    $scheduleObj->start_time = $_POST['start_time'];
    $scheduleObj->end_time = $_POST['end_time'];
    $scheduleObj->waste_type = $_POST['waste_type'];
    $scheduleObj->status = $_POST['status'];

    if ($scheduleObj->updateSchedule((int)$id)) {
        $userId = $_SESSION['user_id'];
        $actionDetails = "Updated schedule ID: " . $id . " for " . $_POST['resident_name'];

        $logStmt = $pdo->prepare("INSERT INTO transactions (user_id, action_details) VALUES (?, ?)");
        $logStmt->execute([$userId, $actionDetails]);

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule - EcoTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #090d16;
            color: #f1f5f9;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .ecotrack-card {
            width: 100%;
            max-width: 560px;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(51, 65, 85, 0.7);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }
        .ecotrack-input {
            width: 100%;
            background-color: #020617;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px 16px;
            color: #f8fafc;
            font-size: 14px;
            transition: all 0.2s ease;
            box-sizing: border-box;
            outline: none;
        }
        .ecotrack-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }
        .ecotrack-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .ecotrack-btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .ecotrack-btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }
        .ecotrack-btn-cancel {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .ecotrack-btn-cancel:hover {
            color: #f8fafc;
        }
    </style>
</head>
<body>

    <div class="ecotrack-card">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Edit Schedule</h1>
                <p class="text-xs text-emerald-400 font-medium mt-1">Updating Record ID #<?= htmlspecialchars($data['schedule_id']) ?></p>
            </div>
            <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold rounded-full">
                EcoTrack System
            </span>
        </div>

        <form method="POST" class="space-y-4">
            <div>
                <label class="ecotrack-label">Resident Full Name</label>
                <input type="text" name="resident_name" value="<?= htmlspecialchars($data['resident_name']) ?>" required class="ecotrack-input">
            </div>

            <div>
                <label class="ecotrack-label">Street Address</label>
                <input type="text" name="street_address" value="<?= htmlspecialchars($data['street_address']) ?>" required class="ecotrack-input">
            </div>

            <div>
                <label class="ecotrack-label">Zone / Neighborhood (Purok)</label>
                <input type="text" name="purok_name" value="<?= htmlspecialchars($data['purok_name']) ?>" required class="ecotrack-input">
            </div>

            <div>
                <label class="ecotrack-label">Collection Date</label>
                <input type="date" name="collection_date" value="<?= htmlspecialchars($data['collection_date']) ?>" required class="ecotrack-input">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="ecotrack-label">Start Time</label>
                    <input type="time" name="start_time" value="<?= htmlspecialchars($data['start_time']) ?>" required class="ecotrack-input">
                </div>
                <div>
                    <label class="ecotrack-label">End Time</label>
                    <input type="time" name="end_time" value="<?= htmlspecialchars($data['end_time']) ?>" required class="ecotrack-input">
                </div>
            </div>

            <div>
                <label class="ecotrack-label">Waste Type</label>
                <select name="waste_type" class="ecotrack-input">
                    <?php foreach (['Biodegradable', 'Non-Biodegradable', 'Recyclable', 'Bulk'] as $type): ?>
                        <option value="<?= $type ?>" <?= $data['waste_type'] === $type ? 'selected' : '' ?> style="background-color: #0f172a; color: white;"><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="ecotrack-label">Status</label>
                <select name="status" class="ecotrack-input">
                    <?php foreach (['Scheduled', 'In Progress', 'Completed', 'Delayed'] as $st): ?>
                        <option value="<?= $st ?>" <?= $data['status'] === $st ? 'selected' : '' ?> style="background-color: #0f172a; color: white;"><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-slate-800 mt-6">
                <a href="index.php" class="ecotrack-btn-cancel">&larr; Cancel & Return</a>
                <button type="submit" class="ecotrack-btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</body>
</html>