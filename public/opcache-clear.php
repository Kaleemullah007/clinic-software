<?php
// One-time OPcache reset helper — delete this file after use.
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo '<p style="font-family:monospace;color:green">✅ OPcache cleared successfully.</p>';
} else {
    echo '<p style="font-family:monospace;color:orange">⚠️ OPcache extension is not enabled — no action needed.</p>';
}
echo '<p style="font-family:monospace">You can now delete this file.</p>';
