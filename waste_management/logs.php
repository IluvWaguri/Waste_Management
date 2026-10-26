<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'classes/Database.php';
$database = new Database();
$pdo = $database->connect();

// Fetch all transaction logs joined with the users table
try {
    $stmt = $pdo->query("
        SELECT t.*, u.username 
        FROM transactions t 
        JOIN users u ON t.user_id = u.user_id 
        ORDER BY t.created_at DESC
    ");
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = []; // Fallback if table doesn't exist yet
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs - EcoTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-6">

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">System Activity Logs</h1>
                <p class="text-xs text-slate-400 mt-1">Track user actions, additions, edits, and deletions</p>
            </div>
            <a href="index.php" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-medium px-4 py-2 rounded-xl transition text-sm">
                &larr; Back to Dashboard
            </a>
        </div>

        <!-- Logs Table Container -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden backdrop-blur">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900/60 border-b border-slate-700 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">ID</th>
                            <th class="p-4">User</th>
                            <th class="p-4">Action Details</th>
                            <th class="p-4">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60 text-sm">
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">No activity logs found or transactions table is missing.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-slate-700/30 transition">
                                    <td class="p-4 text-slate-300 font-mono text-xs">#<?= $log['transaction_id'] ?></td>
                                    <td class="p-4 font-medium text-white"><?= htmlspecialchars($log['username']) ?></td>
                                    <td class="p-4 text-slate-300"><?= htmlspecialchars($log['action_details']) ?></td>
                                    <td class="p-4 text-xs text-slate-400"><?= $log['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>