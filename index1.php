<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// choose active tab (default: sqli)
$tab = $_GET['v'] ?? 'sqli';

// helper to safely include a vuln file (prevents directory traversal)
function include_vuln($name) {
    $allowed = ['sqli','xss','dom','auth','access'];
    if (!in_array($name, $allowed, true)) $name = 'sqli';
    $file = __DIR__ . "/vulns/{$name}_login.php";
    // some vuln pages use different filenames — map explicitly
    $map = [
        'sqli'  => __DIR__ . '/vulns/sqli_login.php',
        'xss'   => __DIR__ . '/vulns/stored_xss.php',
        'dom'   => __DIR__ . '/vulns/dom_xss.php',
        'auth'  => __DIR__ . '/vulns/broken_auth.php',
        'access'=> __DIR__ . '/vulns/idor_demo.php',
    ];
    $path = $map[$name] ?? $map['sqli'];
    if (file_exists($path)) {
        include $path;
    } else {
        echo "<div style='padding:20px;color:#a33;background:#fff4f4;border:1px solid #f0c;'>Demo page not found: " . htmlspecialchars(basename($path)) . "</div>";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NTMS OWASP Demo Portal</title>
  <style>
    /* top bar */
    body { font-family: Arial, Helvetica, sans-serif; margin:0; background:#f4f6f8; color:#222; }
    .top-bar { background:#111; color:#fff; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; }
    .title { font-weight:700; font-size:16px; }
    .user-actions { display:flex; gap:12px; align-items:center; }
    .logout-btn { background:#ff4d4d; color:#fff; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; }

    /* tab nav */
    .tabs { display:flex; gap:8px; padding:12px 20px; background:#fff; border-bottom:1px solid #e6e9ec; }
    .tabs a { text-decoration:none; color:#2b3a42; padding:10px 14px; border-radius:6px; }
    .tabs a.active { background:#e8f0ff; color:#0a58ca; box-shadow: inset 0 -3px 0 rgba(10,88,202,0.08); font-weight:600; }

    /* layout */
    .wrap { max-width:1100px; margin:28px auto; display:grid; grid-template-columns: 280px 1fr; gap:20px; padding:0 20px; }
    .menu { background:#fff; border-radius:8px; padding:14px; box-shadow:0 6px 18px rgba(20,30,40,0.04); }
    .menu a { display:block; padding:8px 10px; color:#1f2933; text-decoration:none; border-radius:6px; margin-bottom:6px; }
    .menu a.active { background:#0a58ca; color:#fff; }

    .content { background:#fff; border-radius:8px; padding:18px; box-shadow:0 6px 18px rgba(20,30,40,0.04); min-height:420px; }
    .section-title { margin:0 0 12px 0; font-size:18px; color:#0b3a66; }

    /* small responsive */
    @media (max-width:900px) {
      .wrap { grid-template-columns: 1fr; }
      .menu { order:2; }
    }
  </style>
</head>
<body>

  <!-- top -->
  <div class="top-bar">
    <div class="title">NTMS Azure Batch – PaaS based 2-Tier Sample Application — OWASP Demo</div>
    <div class="user-actions">
      <div style="color:#ddd">Hello, <strong style="color:#fff"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
      <form action="logout.php" method="post" style="margin:0;">
        <button class="logout-btn" type="submit">Log off</button>
      </form>
    </div>
  </div>

  <!-- tabs -->
  <div class="tabs" role="navigation" aria-label="OWASP tabs">
    <a href="?v=sqli" class="<?php echo $tab==='sqli' ? 'active':''; ?>">A03: Injection</a>
    <a href="?v=xss"  class="<?php echo $tab==='xss'  ? 'active':''; ?>">A03: XSS</a>
    <a href="?v=dom"  class="<?php echo $tab==='dom'  ? 'active':''; ?>">DOM: XSS</a>
    <a href="?v=auth" class="<?php echo $tab==='auth' ? 'active':''; ?>">A07: Auth Failures</a>
    <a href="?v=access" class="<?php echo $tab==='access' ? 'active':''; ?>">A01: Broken Access</a>
  </div>

  <div class="wrap">
    <!-- left menu / quick links -->
    <aside class="menu" aria-label="Quick demos">
      <strong>Quick Demos</strong>
      <a href="?v=sqli" class="<?php echo $tab==='sqli' ? 'active':''; ?>">SQL Injection (login)</a>
      <a href="?v=xss"  class="<?php echo $tab==='xss' ? 'active':''; ?>">Stored XSS</a>
      <a href="?v=dom"  class="<?php echo $tab==='dom' ? 'active':''; ?>">DOM XSS</a>
      <a href="?v=auth" class="<?php echo $tab==='auth' ? 'active':''; ?>">Auth Failures</a>
      <a href="?v=access" class="<?php echo $tab==='access' ? 'active':''; ?>">Broken Access (IDOR)</a>

      <hr style="margin:12px 0;border:none;border-top:1px solid #eef2f5" />

      <strong>Utilities</strong>
      <a href="view.php">View All Inquiries</a>
      <a href="search_form.php">Search</a>
      <a href="index.php#add">Add Inquiry</a>
      <a href="seed_demo_data.php">(Re)Seed Demo Data</a>
    </aside>

    <!-- main content -->
    <main class="content" role="main">
      <h2 class="section-title">
        <?php
          $titles = ['sqli'=>'SQL Injection (Login)','xss'=>'Stored XSS','dom'=>'DOM XSS','auth'=>'Auth Failures','access'=>'Broken Access (IDOR)'];
          echo $titles[$tab] ?? 'SQL Injection (Login)';
        ?>
      </h2>

      <!-- include the selected demo (safe mapping) -->
      <?php include_vuln($tab); ?>

      <!-- footer / notes -->
      <div style="margin-top:18px;padding:12px;border-top:1px dashed #e6eef9;color:#2b3a42">
        <strong>Notice:</strong> These pages are intentionally vulnerable for learning and testing in a controlled environment only.
      </div>
    </main>
  </div>

</body>
</html>
