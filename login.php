<?php
require_once 'db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    }
    else {
        // Only allow admins to login
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM admins WHERE username = ? AND role = 'admin'");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;
        }
        else {
            $error = "Invalid admin username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Megastar Eid Carnival 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Theme Initializer -->
    <script>
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: var(--bg-color);
        }
        .login-container {
            background-color: var(--card-bg);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            color: var(--heading-color);
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        .login-container p {
            color: var(--text-color);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--input-bg);
            color: var(--input-text);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-top: 0.5rem;
            font-family: inherit;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            border: none;
            background-color: var(--accent-color);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 1rem;
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        .error-msg {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>Megastar Carnival '26</h1>
        <p>Finance Management System</p>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php
endif; ?>

        <form action="" method="POST">
            <div class="form-group" style="text-align: left; margin-bottom: 1rem;">
                <label for="username" style="font-size: 0.9rem; font-weight: 500; color:var(--heading-color)">Username</label>
                <input type="text" id="username" name="username" required autofocus placeholder="e.g. MGsecratery">
            </div>
            <div class="form-group" style="text-align: left; margin-bottom: 1.5rem;">
                <label for="password" style="font-size: 0.9rem; font-weight: 500; color:var(--heading-color)">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password">
            </div>
            
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
    </div>

</body>
</html>
