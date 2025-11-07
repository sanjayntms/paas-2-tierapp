<?php
// v ul ns/broken_auth.php  (INTENTIONALLY VULNERABLE)
// Demonstrates weak auth/session handling: no session timeout, no account lockout, predictable "admin" backdoor.
session_start();
include __DIR__ . '/../db.php';

$msg = '';

// Very weak login (for demo) — this page demonstrates broken auth patterns.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // VULNERABLE: naive check; plain-text passwords and a "backdoor" account
    if ($username === 'admin' && $password === 'letmein') {
        $_SESSION['username'] = 'admin';
        $msg = 'Admin logged in (demo backdoor).';
    } else {
        // check db (plain text compare)
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($u && $u['password_hash'] === $password) {
            $_SESSION['username'] = $u['username'];
            $msg = 'Login successful.';
        } else {
            $msg = 'Invalid credentials.';
        }
    }
}

// Demonstrate missing session timeout and no check for reuse
?>
<h3>Broken Authentication Demo</h3>
<p>This demo shows common broken patterns: plain-text passwords, backdoor credentials, no lockout, no session expiry.</p>

<form method="post" style="max-width:420px;">
  <input name="username" placeholder="username" style="width:100%;padding:8px;margin:6px 0;">
  <input name="password" placeholder="password" style="width:100%;padding:8px;margin:6px 0;">
  <button style="padding:8px 12px">Login</button>
</form>

<?php if ($msg): ?>
  <div style="margin-top:10px;padding:8px;background:#fff3cd;border:1px solid #ffeeba;"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<hr>
<div style="background:#fffbe6;padding:12px;border-left:4px solid #ffdd57;">
  <strong>Fix:</strong>
  <ul>
    <li>Store passwords using <code>password_hash()</code> and verify with <code>password_verify()</code>.</li>
    <li>Implement account lockout or rate limiting after failed attempts.</li>
    <li>Use secure session settings: <code>session.cookie_secure</code>, <code>httponly</code>, and session expiry/inactivity timeout.</li>
    <li>Remove any hardcoded/backdoor credentials.</li>
  </ul>
</div>
