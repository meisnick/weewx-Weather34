<?php
/**
 * module.template.php — Weather34 grid module boilerplate
 *
 * Copy this file, rename it, and implement your module.
 * This fragment is AJAX-loaded into a .weather-item container: 320px wide × 195px tall.
 * Do NOT include <html>, <head>, or <body> — this is an HTML fragment.
 *
 * Design system classes are in css/w34-module.css (loaded by index.php).
 * Use the .mod-* classes rather than writing new position:absolute CSS.
 *
 * Layout guide:
 *   .mod              — flex column, full height container
 *   .mod-header       — top row: timestamp right-aligned
 *   .mod-primary      — large value + unit baseline-aligned
 *   .mod-rows         — flex column of secondary data rows
 *   .mod-row          — label : value pair
 *
 * Typography:
 *   .mod-xl           — 1.6rem weathertext2  (primary reading)
 *   .mod-lg           — 1.0rem weathertext2  (secondary or top-bar primary)
 *   .mod-md           — 0.85rem weathertext2 (smaller secondary)
 *   .mod-sm           — 0.7rem weathertext2  (min/max, trend)
 *   .mod-label        — 0.55rem Arial        (row labels, never bold)
 *   .mod-unit         — 0.55rem Arial        (unit suffixes)
 *   .mod-time         — 0.55rem weathertext2 (update timestamp)
 *
 * Color classes:
 *   .mod-warm         — orange  #ff7c39  (hot temp, high wind)
 *   .mod-hot          — red     #d35d4e  (danger / extreme)
 *   .mod-cold         — blue    #3b9cac  (cold / freezing)
 *   .mod-ok           — green   #90b12a  (normal / good)
 *   .mod-caution      — yellow  #e6a141  (watch / advisory)
 *   .mod-muted-c      — grey    var(--mod-muted)
 *
 * Trend: .mod-rising .mod-falling .mod-steady
 * Status: .mod-online  .mod-offline
 * Badge: .mod-pill .mod-pill-warm / -cold / -ok / -caution / -neutral
 */
include('w34CombinedData.php');
error_reporting(0);
?>
<div class="mod">

  <!-- Timestamp — top right, no absolute positioning needed -->
  <div class="mod-header">
    <span class="mod-time">
      <?php
      if (file_exists($livedata) && time() - filemtime($livedata) > 300)
          echo '<span class="mod-offline">Offline</span>';
      else
          echo $online . ' ' . $weather['time'];
      ?>
    </span>
  </div>

  <!-- Primary reading -->
  <div class="mod-primary">
    <span class="mod-xl mod-warm">--</span>
    <span class="mod-unit">&deg;<?php echo $weather['temp_units']; ?></span>
  </div>

  <!-- Secondary rows -->
  <div class="mod-rows">

    <div class="mod-row">
      <span class="mod-label">Label One</span>
      <span class="mod-md">-- <span class="mod-unit">unit</span></span>
    </div>

    <div class="mod-row">
      <span class="mod-label">Label Two</span>
      <span class="mod-md mod-cold">-- <span class="mod-unit">unit</span></span>
    </div>

    <!-- Pill badge example -->
    <div class="mod-row">
      <span class="mod-label">Badge</span>
      <span class="mod-pill mod-pill-neutral">--</span>
    </div>

  </div>

</div>
