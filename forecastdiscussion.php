<?php include('shared_core.php'); include('common.php');
$summary_file = 'jsondata/afd_summary.json';
$bullets = [
    '<span class="hl-red">'.$lang['NoSummary'].'</span> '.$lang['GeneratedYet'],
    $lang['CheckBackgroundScript'],
    $lang['ConfirmOllamaStatus']
];
$raw_sections = [];
$issued = '--';
$data_ok = false;

if (file_exists($summary_file)) {
    $data = json_decode(file_get_contents($summary_file), true);
    if (json_last_error() === JSON_ERROR_NONE && ($data['success'] ?? false)) {
        $bullets = $data['bullets'];
        $raw_sections = $data['raw_sections'] ?? [];
        $issued = date($timeFormat, strtotime($data['issued']));
        $data_ok = true;
    }
}
?>
<div class="updatedtime"><span>
    <?php if ($data_ok):
        echo $online . ' ' . $issued;
    else:
        echo $offline . ' <offline>'.$lang['Offline'].'</offline>';
    endif; ?>
</span></div>

<div class="mod-fct">

    <!-- Parsed Narrative Lines with Inline Highlights -->
    <div class="mod-fct-lines">
        <?php foreach ($bullets as $b): 
            // Append timeframe notations dynamically to the bullet labels
            $b = str_replace('<strong>Key Messages</strong>', '<strong>Key Messages <span style="font-size:0.55rem; color:#888; font-weight:normal;">(Active)</span></strong>', $b);
            $b = str_replace('<strong>Short Term</strong>', '<strong>Short Term <span style="font-size:0.55rem; color:#888; font-weight:normal;">(1-3 Days)</span></strong>', $b);
            $b = str_replace('<strong>Long Term</strong>', '<strong>Long Term <span style="font-size:0.55rem; color:#888; font-weight:normal;">(3-7 Days)</span></strong>', $b);
            $b = str_replace('<strong>Outlook</strong>', '<strong>Outlook <span style="font-size:0.55rem; color:#888; font-weight:normal;">(Days 7+)</span></strong>', $b);
        ?>
        <div class="mod-fct-line">
            <?php echo $b; ?>
        </div>
        <?php endforeach; ?>
    </div>

</div>
