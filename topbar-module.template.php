<?php
/**
 * topbar-module.template.php — Weather34 top-bar module boilerplate
 *
 * Copy this file, rename it, and implement your module.
 * This fragment is AJAX-loaded into a .weather34box container: 240px wide × 83px tall.
 * Do NOT include <html>, <head>, or <body> — this is an HTML fragment.
 *
 * Add to $topbar_modules in modules.php to display.
 * Use .mod.mod-top (83px height variant) — see css/w34-module.css.
 *
 * Keep it compact — only 83px tall. Typical layout:
 *   - One prominent value (mod-lg)
 *   - One or two label rows (mod-label / mod-sm)
 *   - Optional timestamp in mod-header
 */
include('w34CombinedData.php');
error_reporting(0);
?>
<div class="mod mod-top">

  <!-- Timestamp (optional — omit if space is tight) -->
  <div class="mod-header">
    <span class="mod-time"><?php echo $weather['time']; ?></span>
  </div>

  <!-- Primary reading -->
  <div class="mod-primary">
    <span class="mod-lg mod-warm">--</span>
    <span class="mod-unit">unit</span>
  </div>

  <!-- One supporting label row -->
  <div class="mod-label">Description of reading</div>

</div>
