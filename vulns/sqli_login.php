<?php
// v ul ns/sqli_login.php  (INTENTIONALLY VULNERABLE)
// SQL Injection (login) demo - stores/reads plain-text password for the demo
session_start();
include __DIR__ . '/../db.php'; // expects $pdo

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    // VULNERABLE: direct interpolation (no prepared statements) => SQLi
    $sql = "SELECT * FROM Users WHERE username = '$u' AND password_hash = '$p'";
    try {
        $stmt = $pdo->query($sql);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['username'] = $user['username'];
            echo "<div style='padding:10px;background:#e6ffed;border:1px solid #8ee6a8;margin-bottom:12px;'>Login successful — session started.</div>";
        } else {
            $error = 'Invalid username or password.';
        }
    } catch (Exception $e) {
        $error = 'DB error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
<h3>SQL Injection — Vulnerable Login</h3>
<p>Purpose: Demonstrate SQL injection. <strong>Only use in a lab.</strong></p>
<p><em>Try:</em> Username: <code>admin</code> — Password: <code>' OR '1'='1</code></p>

<form method="post" style="max-width:480px;">
  <input name="username" placeholder="username" style="width:100%;padding:8px;margin:6px 0;">
  <input name="password" placeholder="password" style="width:100%;padding:8px;margin:6px 0;">
  <button style="padding:8px 12px">Login</button>
</form>

<?php if ($error): ?>
  <div style="color:#9a1222;padding:8px;border:1px solid #f1c4c4;background:#fff2f2;margin-top:10px;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<hr>
<div style="background:#fffbe6;padding:12px;border-left:4px solid #ffdd57;">
  <strong>Fix:</strong>
  <pre style="white-space:pre-wrap;background:#f6f8fa;padding:8px;border-radius:4px;">
// Use parameterized queries (prepared statements)
$stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ? AND password_hash = ?");
$stmt->execute([$u, $p]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
  </pre>
  <small>Also: store hashed passwords (password_hash) and use password_verify() — never plain text in production.</small>
</div>
