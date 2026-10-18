<?php include_once('w34_i18n.php'); ?>
<?php include_once('shared_core.php'); include('common.php');
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
            $b = str_replace('<strong>Key Messages</strong>', '<strong>'.w34t('KeyMessages', 'Key Messages').' <span class="mod-fct-sub">('.w34t('Active', 'Active').')</span></strong>', $b);
            $b = str_replace('<strong>Short Term</strong>', '<strong>'.w34t('ShortTerm', 'Short Term').' <span class="mod-fct-sub">('.w34t('Days1to3', '1-3 Days').')</span></strong>', $b);
            $b = str_replace('<strong>Long Term</strong>', '<strong>'.w34t('LongTerm', 'Long Term').' <span class="mod-fct-sub">('.w34t('Days3to7', '3-7 Days').')</span></strong>', $b);
            $b = str_replace('<strong>Outlook</strong>', '<strong>'.w34t('AfdOutlook', 'Outlook').' <span class="mod-fct-sub">('.w34t('Days7plus', 'Days 7+').')</span></strong>', $b);
        ?>
        <div class="mod-fct-line">
            <?php echo $b; ?>
        </div>
        <?php endforeach; ?>
    </div>

</div>
