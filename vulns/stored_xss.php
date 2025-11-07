<?php
// v ul ns/stored_xss.php  (INTENTIONALLY VULNERABLE)
// Stored XSS demo: user posts message stored in DB and rendered without escaping.
session_start();
include __DIR__ . '/../db.php';

$posted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $msg  = $_POST['message'] ?? '';

    // Vulnerable: message inserted and later rendered without escaping
    $sql = "INSERT INTO Inquiries (name, mobile_number, email, message, photo_url, created_at)
            VALUES (:n, '', '', :m, '', GETDATE())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':n'=>$name, ':m'=>$msg]);
    $posted = true;
}

// Fetch recent messages
$rows = $pdo->query("SELECT TOP 50 name, message, created_at FROM Inquiries ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h3>Stored XSS (persistent)</h3>
<p>Purpose: store a message that will be rendered unsafely later.</p>

<form method="post" style="max-width:640px;">
  <input name="name" placeholder="Your name" style="width:100%;padding:8px;margin:6px 0;">
  <textarea name="message" placeholder="Your message (HTML allowed)" style="width:100%;padding:8px;height:90px;margin:6px 0;"></textarea>
  <button style="padding:8px 12px">Submit Message</button>
</form>

<?php if ($posted): ?>
  <div style="margin-top:10px;padding:8px;background:#e6ffed;border:1px solid #8ee6a8;">Stored. Scroll down to see rendered messages.</div>
<?php endif; ?>

<h4>Recent Messages (vulnerable rendering)</h4>
<?php foreach($rows as $r): ?>
  <div style="border:1px solid #ddd;padding:10px;margin:8px 0;border-radius:6px;">
    <strong><?php echo htmlspecialchars($r['name']); ?></strong> <small style="color:#666">— <?php echo $r['created_at']; ?></small>
    <div style="margin-top:8px;">
      <?php
        // VULNERABLE: intentionally NOT escaping the message
        echo $r['message'];
      ?>
    </div>
  </div>
<?php endforeach; ?>

<hr>
<div style="background:#fffbe6;padding:12px;border-left:4px solid #ffdd57;">
  <strong>Fix:</strong>
  <pre style="white-space:pre-wrap;background:#f6f8fa;padding:8px;border-radius:4px;">
// Escape output when rendering:
echo htmlspecialchars($r['message'], ENT_QUOTES, 'UTF-8');
  </pre>
  <small>Consider also: Content Security Policy (CSP), input sanitization, and output encoding.</small>
</div>
