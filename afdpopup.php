<?php
include('w34CombinedData.php'); error_reporting(0);

$summary_file = 'jsondata/afd_summary.json';
$raw_sections = [];
$issued = '--';
$data_ok = false;

if (file_exists($summary_file)) {
    $data = json_decode(file_get_contents($summary_file), true);
    if (json_last_error() === JSON_ERROR_NONE && ($data['success'] ?? false)) {
        $raw_sections = $data['raw_sections'] ?? [];
        if (!empty($data['issued'])) {
            $issued = date($timeFormat, strtotime($data['issued']));
        }
        $data_ok = true;
    }
}

// ── Theme variables matching pop_aurora.php exactly ──────────────────────────
$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Area Forecast Discussion</title>
<style>
@font-face {
  font-family: weathertext2;
  src: url(css/fonts/verbatim-regular.woff) format("woff"),
       url(css/fonts/verbatim-regular.woff2) format("woff2"),
       url(css/fonts/verbatim-regular.ttf) format("truetype");
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  height: 100%; overflow: hidden;
  font-family: Arial, sans-serif;
  font-size: 13px;
  background: <?php echo $bg; ?>;
  color: <?php echo $text; ?>;
  -webkit-font-smoothing: antialiased;
}
body { display: flex; flex-direction: column; }

/* ── Header strip ─────────────────────────────────────────────────────────── */
.pop-header {
  flex: 0 0 auto;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  padding: 5px 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pop-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .8em;
  color: <?php echo $text; ?>;
  letter-spacing: 0.3px;
}
.pop-issued {
  font-size: .65em;
  color: <?php echo $text_dim; ?>;
  white-space: nowrap;
  margin-right: 50px; /* clear lity close button */
}

/* ── Tab row — matches pop_aurora.php exactly ────────────────────────────── */
.pop-tabs {
  flex: 0 0 auto;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  padding: 4px 5px;
  display: flex;
  flex-wrap: wrap;
  gap: 0;
}
.tablink {
  background-color: #555;
  color: white;
  border: 2px solid <?php echo $bg_chrome; ?>;
  border-radius: 5px;
  margin-top: 0;
  margin-left: 5px;
  outline: none;
  cursor: pointer;
  padding: 5px 8px;
  font-size: 10px;
  font-family: Arial, sans-serif;
}
.tablink:hover { background-color: #777; }

/* ── Content wrapper ─────────────────────────────────────────────────────── */
.pop-content {
  flex: 1;
  min-height: 0;
  position: relative;
  overflow: hidden;
}
.tabcontent {
  display: none;
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  overflow: hidden;
}

/* ── Content layout ──────────────────────────────────────────────────────── */
.discussion-body {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 8px 10px;
}
.fcst-card {
  background: <?php echo $bg_card; ?>;
  border-radius: 3px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
}
.fcst-card-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .75em;
  color: silver;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
  flex: 0 0 auto;
}
.fcst-card-text {
  flex: 1;
  overflow-y: auto;
  font-family: Arial, sans-serif;
  font-size: .85em;
  color: <?php echo $text; ?>;
  line-height: 1.5;
  white-space: pre-wrap;
  text-align: left;
  padding-right: 2px;
  
  /* Hide scrollbar completely to adhere to 'there is no scroll bars allowed' */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE/Edge */
}
.fcst-card-text::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
</style>
</head>
<body>

<!-- Header -->
<div class="pop-header">
  <span class="pop-title">Area Forecast Discussion</span>
  <span class="pop-issued">Issued: <?php echo htmlspecialchars($issued); ?></span>
</div>

<!-- Tab buttons -->
<div class="pop-tabs">
  <button class="tablink" onclick="openTab('t1', this)" id="defaultOpen">Key Messages</button>
  <button class="tablink" onclick="openTab('t2', this)">Short Term</button>
  <button class="tablink" onclick="openTab('t3', this)">Long Term</button>
  <button class="tablink" onclick="openTab('t4', this)">Outlook Trend</button>
</div>

<!-- Content panels -->
<div class="pop-content">

  <!-- Tab 1: Key Messages -->
  <div id="t1" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">Key Messages</div>
        <div class="fcst-card-text"><?php echo !empty($raw_sections['key_messages']) ? nl2br(htmlspecialchars($raw_sections['key_messages'])) : 'No source text available.'; ?></div>
      </div>
    </div>
  </div>

  <!-- Tab 2: Short Term -->
  <div id="t2" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">Short Term Discussion</div>
        <div class="fcst-card-text"><?php echo !empty($raw_sections['short_term']) ? nl2br(htmlspecialchars($raw_sections['short_term'])) : 'No source text available.'; ?></div>
      </div>
    </div>
  </div>

  <!-- Tab 3: Long Term -->
  <div id="t3" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">Long Term Discussion</div>
        <div class="fcst-card-text"><?php echo !empty($raw_sections['long_term']) ? nl2br(htmlspecialchars($raw_sections['long_term'])) : 'No source text available.'; ?></div>
      </div>
    </div>
  </div>

  <!-- Tab 4: Outlook Trend -->
  <div id="t4" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">Outlook Trend</div>
        <div class="fcst-card-text"><?php echo !empty($raw_sections['outlook']) ? nl2br(htmlspecialchars($raw_sections['outlook'])) : 'No source text available.'; ?></div>
      </div>
    </div>
  </div>

</div><!-- .pop-content -->

<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
function openTab(name, el) {
  document.querySelectorAll('.tabcontent').forEach(function(t){ t.style.display = 'none'; });
  document.querySelectorAll('.tablink').forEach(function(t){ t.style.backgroundColor = ''; });
  document.getElementById(name).style.display = 'block';
  el.style.backgroundColor = 'rgba(194, 102, 58)';
}
document.getElementById('defaultOpen').click();
</script>

</body>
</html>
