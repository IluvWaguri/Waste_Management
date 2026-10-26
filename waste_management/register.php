<?php
session_start();
require_once 'classes/Database.php';

$database = new Database();
$pdo = $database->connect();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->fetch()) {
                $error = "Username is already taken.";
            } else {
                // Hash password securely
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $insertStmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                if ($insertStmt->execute([$username, $hashed_password])) {
                    // Record registration transaction log if needed, or redirect to login
                    header("Location: login.php?registered=success");
                    exit;
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - EcoTrack Waste Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center antialiased">
    <div class="max-w-md w-full bg-slate-800/40 border border-slate-700/60 rounded-2xl p-8 shadow-xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Create an Account</h2>
        
        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full px-4 py-2 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:border-emerald-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-lg transition">Register</button>
        </form>
        
        <p class="mt-4 text-center text-sm text-slate-400">
            Already have an account? <a href="login.php" class="text-emerald-400 hover:underline">Login here</a>
        </p>
    </div>
</body>
</html>