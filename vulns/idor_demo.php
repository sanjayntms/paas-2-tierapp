<?php
// v ul ns/idor_demo.php  (INTENTIONALLY VULNERABLE)
// Insecure Direct Object Reference (IDOR) demo: anyone can view any inquiry by id
session_start();
include __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$record = null;
if ($id > 0) {
    // VULNERABLE: no authorization check — anyone can fetch any row by id
    $stmt = $pdo->prepare("SELECT id, name, mobile_number, email, message, photo_url, created_at FROM Inquiries WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
<h3>Insecure Direct Object Reference (IDOR)</h3>
<p>Enter an Inquiry ID to view. The demo intentionally does <strong>no authorization</strong> check.</p>

<form method="get" style="max-width:420px;">
  <input name="id" placeholder="Inquiry ID (e.g., 1)" style="width:100%;padding:8px;margin:6px 0;">
  <button>View</button>
</form>

<?php if ($record): ?>
  <div style="border:1px solid #ddd;padding:12px;margin-top:12px;border-radius:6px;">
    <strong><?php echo htmlspecialchars($record['name']); ?></strong> <small><?php echo $record['created_at']; ?></small>
    <p><?php echo nl2br(htmlspecialchars($record['message'])); ?></p>
    <?php if ($record['photo_url']): ?>
      <div><img src="<?php echo htmlspecialchars($record['photo_url']); ?>" alt="photo" style="max-width:240px;border:1px solid #ccc;"></div>
    <?php endif; ?>
  </div>
<?php elseif (isset($_GET['id'])): ?>
  <div style="color:#a33;margin-top:10px;">No record found with that ID.</div>
<?php endif; ?>

<hr>
<div style="background:#fffbe6;padding:12px;border-left:4px solid #ffdd57;">
  <strong>Fix:</strong>
  <pre style="white-space:pre-wrap;background:#f6f8fa;padding:8px;border-radius:4px;">
// Require authorization check: ensure requesting user is allowed to view the resource
// Example (pseudo):
$currentUser = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT owner_username FROM Inquiries WHERE id = ?");
$stmt->execute([$id]);
$owner = $stmt->fetchColumn();
if ($owner !== $currentUser) {
    // deny access
}
  </pre>
  <small>Also: avoid predictable numeric IDs or use access control lists / object-level ACLs.</small>
</div>
