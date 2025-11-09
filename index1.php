<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// If an 'id' parameter is provided and no explicit 'v' tab, automatically switch to IDOR demo
if (isset($_GET['v']) && $_GET['v'] !== '') {
    $tab = $_GET['v'];
} elseif (isset($_GET['id']) && !isset($_GET['v'])) {
    $tab = 'idor';
} else {
    $tab = 'inquiry';
}

// flash handling
$flash = null;
if (isset($_GET['upload'])) {
    $flash = ($_GET['upload'] === '1') ? ['type'=>'success','text'=>'Inquiry submitted successfully.'] : ['type'=>'error','text'=>'There was an error submitting the inquiry.'];
} elseif (isset($_GET['msg'])) {
    $flash = ['type'=>'info','text' => substr(strip_tags($_GET['msg']),0,400)];
}

// safe include mapping for vuln tabs
function include_vuln($name) {
    $map = [
        'sqli'  => __DIR__ . '/vulns/sqli_login.php',
        'xss'   => __DIR__ . '/vulns/stored_xss.php',
        'dom'   => __DIR__ . '/vulns/dom_xss.php',
        'auth'  => __DIR__ . '/vulns/broken_auth.php',
        'idor'  => __DIR__ . '/vulns/idor_demo.php',
    ];
    $path = $map[$name] ?? null;
    if ($path && file_exists($path)) {
        include $path;
    } else {
        echo "<div style='padding:16px;border:1px solid #f1c4c4;background:#fff2f2;color:#900;border-radius:6px'>Demo page not found: " . htmlspecialchars($name) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>NTMS Azure Demo — Inquiries & OWASP</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <style>
        :root{
            --bg:#f4f6f8;
            --card:#fff;
            --accent:#0a58ca;
            --muted:#666;
            --success:#e6ffed;
            --error:#fff2f2;
        }
        body { font-family: Arial, Helvetica, sans-serif; margin:0; background:var(--bg); color:#222; }
        .top-bar { background:#111; color:#fff; padding:12px 20px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.12); }
        .app-title { font-weight:700; font-size:16px; }
        .logout-btn { background:#ff4d4d; color:#fff; border:0; padding:8px 12px; border-radius:6px; cursor:pointer; }

        .wrap { display:grid; grid-template-columns: 240px 1fr; gap:20px; max-width:1200px; margin:26px auto; padding:0 16px; }
        @media (max-width:900px){ .wrap{ grid-template-columns: 1fr; } .sidebar{ order:2 } .main{ order:1 } }

        .sidebar { background:#243141; color:#fff; border-radius:8px; padding:14px; min-height:220px; box-shadow:0 6px 18px rgba(20,30,40,0.06); }
        .sidebar h4{ margin:6px 0 12px 0; font-size:14px; color:#e7f0ff; }
        .nav-link { display:block; color:#cfe7ff; padding:10px 12px; text-decoration:none; border-radius:6px; margin-bottom:6px; }
        .nav-link:hover { background:#325a8f; color:#fff; }
        .nav-link.active { background:var(--accent); color:#fff; font-weight:600; }

        .main { background:var(--card); padding:18px; border-radius:8px; box-shadow:0 6px 18px rgba(20,30,40,0.04); min-height:420px; }
        .section-title { margin:0 0 12px 0; font-size:18px; color:#0b3a66; }

        .card { background:#fff; padding:18px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
        h2 { text-align:center; color:#333; margin-top:0; }

        form { display:flex; flex-direction:column; align-items:center; }
        input[type="text"], input[type="email"], input[type="submit"], input[type="file"], textarea {
            padding:10px; margin:10px; width:80%; font-size:16px; border-radius:5px; border:1px solid #ccc;
        }
        input[type="submit"] { background:#007bff; color:#fff; border:none; padding:10px 14px; cursor:pointer; font-weight:600; }
        input[type="submit"]:hover { background:#0056b3; }
        .small { font-size:13px; color:var(--muted); text-align:center; margin-top:8px; }

        .search-inline { display:flex; gap:8px; justify-content:center; margin-bottom:8px; }
        .search-inline input[type="text"]{ width:60%; margin:0; }

        .flash { padding:12px;border-radius:6px;margin-bottom:12px; }
        .flash.success { background:var(--success); border-left:4px solid #6ee6a8; }
        .flash.error { background:var(--error); border-left:4px solid #f1a8a8; }
        .flash.info { background:#eef7ff;border-left:4px solid #8eb8ff; }

        .notice { padding:12px; background:#fffbe6; border-left:4px solid #ffdd57; border-radius:6px; margin-top:12px; }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="app-title">NTMS Azure Batch – PaaS based 2-tier Sample Application</div>
        <div style="display:flex;gap:12px;align-items:center">
            <div style="color:#ddd;font-size:14px">Welcome, <strong style="color:#fff"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></div>
            <form action="logout.php" method="post" style="margin:0;">
                <button class="logout-btn" type="submit">Log off</button>
            </form>
        </div>
    </div>

    <div class="wrap">
        <aside class="sidebar" aria-label="Main navigation">
            <h4>Navigation</h4>
            <a class="nav-link <?php echo $tab==='inquiry'?'active':'' ?>" href="index.php?v=inquiry">Inquiry Form</a>
            <a class="nav-link <?php echo $tab==='search'?'active':'' ?>" href="index.php?v=search">Search</a>

            <hr style="border:none;border-top:1px solid rgba(255,255,255,0.06);margin:12px 0">

            <h4 style="color:#dbeeff">OWASP Demos</h4>
            <a class="nav-link <?php echo $tab==='sqli'?'active':'' ?>" href="index.php?v=sqli">A03: SQL Injection</a>
            <a class="nav-link <?php echo $tab==='xss'?'active':'' ?>" href="index.php?v=xss">A03: Stored XSS</a>
            <a class="nav-link <?php echo $tab==='dom'?'active':'' ?>" href="index.php?v=dom">DOM XSS</a>
            <a class="nav-link <?php echo $tab==='auth'?'active':'' ?>" href="index.php?v=auth">A07: Broken Auth</a>
            <a class="nav-link <?php echo $tab==='idor'?'active':'' ?>" href="index.php?v=idor">A01: IDOR</a>

            <hr style="border:none;border-top:1px solid rgba(255,255,255,0.06);margin:12px 0">
            <div class="small">Demo only — run in test environment. Do not expose to production.</div>
        </aside>

        <main class="main" role="main">
            <?php if ($flash): ?>
                <div class="flash <?php echo htmlspecialchars($flash['type']); ?>">
                    <?php echo htmlspecialchars($flash['text']); ?>
                </div>
            <?php endif; ?>

            <?php if ($tab === 'inquiry'): ?>

                <div class="card">
                    <h2>Submit an Inquiry</h2>

                    <div class="search-inline">
                        <form action="search_results.php" method="post" style="display:flex;align-items:center;">
                            <input type="text" name="search_query" placeholder="Enter search term">
                            <input type="submit" value="Search">
                        </form>
                    </div>

                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="text" name="mobile" placeholder="Mobile Number" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                        <input type="file" name="photo" required>
                        <input type="submit" value="Submit Inquiry">
                    </form>
                </div>

            <?php elseif ($tab === 'search'): ?>

                <div class="card">
                    <h2>Search Inquiries</h2>
                    <form action="search_results.php" method="post">
                        <input type="text" name="search_query" placeholder="Enter search term">
                        <input type="submit" value="Search">
                    </form>
                </div>

            <?php elseif (in_array($tab, ['sqli','xss','dom','auth','idor'])): ?>

                <div class="section-title"><?php
                    $titles = ['sqli'=>'SQL Injection','xss'=>'Stored XSS','dom'=>'DOM XSS','auth'=>'Broken Auth','idor'=>'IDOR'];
                    echo htmlspecialchars($titles[$tab] ?? 'Demo');
                ?></div>

                <div class="card">
                    <?php include_vuln($tab); ?>
                </div>

            <?php else: ?>

                <div class="card">
                    <h2>Submit an Inquiry</h2>
                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="text" name="mobile" placeholder="Mobile Number" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                        <input type="file" name="photo" required>
                        <input type="submit" value="Submit Inquiry">
                    </form>
                </div>

            <?php endif; ?>

            <div class="notice" role="note">
                <strong>Note:</strong> OWASP demos are intentionally vulnerable. Use seed_demo_data.php to populate demo data.
            </div>
        </main>
    </div>

</body>
</html>
