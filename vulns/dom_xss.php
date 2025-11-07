<!-- v ul ns/dom_xss.php  (INTENTIONALLY VULNERABLE) -->
<!-- DOM-based XSS: unsafe client-side insertion of URL parameter -->
<h3>DOM-based XSS</h3>
<p>Purpose: inject via the URL (client-side). Example: <code>?msg=&lt;script&gt;alert(1)&lt;/script&gt;</code></p>

<div style="background:#f8f9fb;border:1px solid #e3e7ee;padding:12px;border-radius:6px;">
  <label>Message from URL (vulnerable):</label>
  <div id="output" style="margin-top:10px;padding:12px;background:#fff;border-radius:4px;"></div>
</div>

<script>
  // VULNERABLE: directly writing URL param into innerHTML
  const p = new URLSearchParams(window.location.search).get('msg');
  if (p) {
    document.getElementById('output').innerHTML = p; // <- DOM XSS
  } else {
    document.getElementById('output').textContent = 'No msg param provided.';
  }
</script>

<hr>
<div style="background:#fffbe6;padding:12px;border-left:4px solid #ffdd57;margin-top:12px;">
  <strong>Fix:</strong>
  <pre style="white-space:pre-wrap;background:#f6f8fa;padding:8px;border-radius:4px;">
// Use textContent or safe DOM APIs:
const clean = new URLSearchParams(window.location.search).get('msg') || '';
document.getElementById('output').textContent = clean;

// Or sanitize before assignment if HTML is required.
  </pre>
</div>
