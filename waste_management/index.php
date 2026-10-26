<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include the Schedule class file
require_once 'classes/Schedule.php';

// Instantiate the Schedule class
$scheduleObj = new Schedule();

// Read search query parameter from URL string (e.g. index.php?search=Purok)
$keyword = $_GET['search'] ?? '';

// If a search keyword is given, run searchSchedules(), otherwise fetch all schedules
$schedules = !empty($keyword) ? $scheduleObj->searchSchedules($keyword) : $scheduleObj->getSchedules();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTrack - Waste Collection Dashboard</title>
    <!-- Loads Tailwind CSS dynamically from CDN (eliminates need for manual custom CSS) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen antialiased">

    <!-- Top Navigation Header -->
    <nav class="bg-slate-800/80 backdrop-blur border-b border-slate-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">♻️</div>
                <div>
                    <h1 class="font-bold text-lg text-white leading-tight">EcoTrack Portal</h1>
                    <p class="text-xs text-slate-400">Waste Management System</p>
                </div>
            </div>
            
            <!-- User greeting & Action Buttons -->
<div class="flex items-center gap-3">
    <span class="text-xs text-slate-300 hidden sm:inline">Logged in as: <strong class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
    
    <!-- Link to Transaction Activity Logs -->
    <a href="logs.php" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-medium px-4 py-2 rounded-lg transition text-sm">
        📋 Logs
    </a>

    <a href="add.php" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold px-4 py-2 rounded-lg shadow-lg transition-all text-sm">
        <span>+</span> Add Schedule
    </a>
    
    <a href="logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 font-medium px-4 py-2 rounded-lg transition text-sm">
        Logout
    </a>
</div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-8">

        <!-- GET Search Form -->
        <form method="GET" class="mb-8 flex gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="<?= htmlspecialchars($keyword) ?>" 
                       placeholder="Search by resident name, street address, purok, or waste type..." 
                       class="w-full bg-slate-800/90 border border-slate-700 text-white placeholder-slate-500 rounded-xl px-4 py-3.5 pl-11 focus:outline-none focus:border-emerald-500 transition text-sm">
                <span class="absolute left-4 top-3.5 text-slate-500">🔍</span>
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-medium px-6 py-3.5 rounded-xl transition text-sm">
                Search
            </button>
            <?php if (!empty($keyword)): ?>
                <!-- Clear Button appears only when a search term is actively applied -->
                <a href="index.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 font-medium px-5 py-3.5 rounded-xl transition text-sm flex items-center">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Main Data Table -->
        <div class="bg-slate-800/40 border border-slate-700/60 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/80 border-b border-slate-700/80 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Resident Name</th>
                            <th class="py-4 px-6">Street Address</th>
                            <th class="py-4 px-6">Purok</th>
                            <th class="py-4 px-6">Date & Time Window</th>
                            <th class="py-4 px-6">Waste Type</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50 text-sm">
                        <?php if (empty($schedules)): ?>
                            <!-- Display message if zero records exist -->
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    No schedules found in database. Click <strong>"+ Add Resident Schedule"</strong> to create one!
                                </td>
                            </tr>
                        <?php else: ?>
                            <!-- Loop through array of schedules returned by MySQL -->
                            <?php foreach ($schedules as $row): ?>
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="py-4 px-6 font-mono text-slate-500 text-xs">#<?= $row['schedule_id'] ?></td>
                                    <td class="py-4 px-6 font-semibold text-white"><?= htmlspecialchars($row['resident_name']) ?></td>
                                    <td class="py-4 px-6 text-slate-300"><?= htmlspecialchars($row['street_address']) ?></td>
                                    <td class="py-4 px-6 text-slate-400"><?= htmlspecialchars($row['purok_name']) ?></td>
                                    <td class="py-4 px-6 text-slate-300 text-xs">
                                        <!-- Formats date (M d, Y) and time (g:i A) into human-readable text -->
                                        <div><?= date("M d, Y", strtotime($row['collection_date'])) ?></div>
                                        <div class="text-slate-500"><?= date("g:i A", strtotime($row['start_time'])) ?> – <?= date("g:i A", strtotime($row['end_time'])) ?></div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium border bg-emerald-500/10 text-emerald-400 border-emerald-500/30">
                                            <?= $row['waste_type'] ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border bg-indigo-500/10 text-indigo-400 border-indigo-500/30">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <!-- Pass row ID via GET parameters to edit.php and delete.php -->
                                        <a href="edit.php?id=<?= $row['schedule_id'] ?>" class="text-sky-400 hover:text-sky-300 font-medium text-xs transition">Edit</a>
                                        <a href="delete.php?id=<?= $row['schedule_id'] ?>" onclick="return confirm('Delete this resident record?');" class="text-rose-400 hover:text-rose-300 font-medium text-xs transition">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>