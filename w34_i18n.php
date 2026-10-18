<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick                                                                    #
//	Translation helpers for module text, dates and forecast/sky condition phrases.                                 #
//	Every helper falls back to the English text it is given, so a missing key never renders blank.                #
//###################################################################################################################
include_once __DIR__ . '/common.php';

// Is a non-English language active? (lang.en.php is also used for 'us' and 'can')
function w34_is_english() {
    global $lang_file;
    return empty($lang_file) || $lang_file === 'lang.en.php';
}

// BCP 47 tag for the pages' html lang attribute. Browsers apply language rules to CSS text-transform with it: Greek
// capitals drop their accents (ΚΕΡΑΥΝΟΙ, not ΚΕΡΑΥΝΟΊ), Turkish i -> İ, German ß -> SS.
function w34_html_lang() {
    global $lang_file;
    $tags = array('lang.dk.php' => 'da', 'lang.nl.php' => 'nl', 'lang.pl.php' => 'pl', 'lang.dl.php' => 'de',
        'lang.it.php' => 'it', 'lang.sp.php' => 'es', 'lang.cat.php' => 'ca', 'lang.fr.php' => 'fr',
        'lang.gr.php' => 'el', 'lang.tr.php' => 'tr', 'lang.hu.php' => 'hu', 'lang.no.php' => 'nb', 'lang.sw.php' => 'sv');
    return isset($lang_file, $tags[$lang_file]) ? $tags[$lang_file] : 'en';
}

// Translated text for $key, or $english when the key is missing.
function w34t($key, $english = '') {
    global $lang;
    return (is_array($lang) && isset($lang[$key]) && trim($lang[$key]) !== '') ? trim($lang[$key]) : $english;
}

// Translated sprintf format, e.g. w34f('AlmToday', '%s Today', w34t('Barometer', 'Barometer')),
// so each language can put the pieces in its own order.
function w34f($key, $english) {
    $args = array_slice(func_get_args(), 2);
    try {
        return vsprintf(w34t($key, $english), $args);
    } catch (\Throwable $e) {  // a translation with the wrong number of %s
        return vsprintf($english, $args);
    }
}

// Translate English month/day names (full and 3-letter) and ordinal suffixes inside date text,
// e.g. '23rd Jan', 'Saturday 17 Oct 2026', 'October'. Works on fresh date() output and on
// pre-formatted strings from the data layer.
function w34_datetext($text) {
    static $map = null;
    if (w34_is_english() || $text === null || $text === '') return $text;
    if ($map === null) {
        $map = array();
        $names = array('January' => 'January', 'February' => 'Febuary', 'March' => 'March', 'April' => 'April',
            'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September',
            'October' => 'October', 'November' => 'November', 'December' => 'December',
            'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday',
            'Friday' => 'Friday', 'Saturday' => 'Saturday', 'Sunday' => 'Sunday');
        foreach ($names as $english => $key) {
            $map[substr($english, 0, 3)] = w34t($key . 'Short', mb_substr(w34t($key, $english), 0, 3));
        }
        foreach ($names as $english => $key) {  // after the abbreviations, so 'May' keeps its full name
            $map[$english] = w34t($key, $english);
        }
    }
    $text = preg_replace('/(\d)(st|nd|rd|th)\b/', '$1', $text);
    return strtr($text, $map);
}

// date() with localized month/day names.
function w34_date($format, $ts = null) {
    return w34_datetext(date($format, $ts === null ? time() : $ts));
}

// Weather condition phrases (forecast, METAR sky, current conditions), e.g. 'Mostly Sunny',
// "Light Rain\nShowers", 'Misty Fog<br>Conditions'. Key = 'Cond' + the letters of the phrase.
// A '|' in the translation marks where the original line break ("\n" or <br>) goes.
function w34_cond($phrase) {
    if ($phrase === null || $phrase === '' || w34_is_english()) return $phrase;
    $words = strip_tags(preg_replace('/<br\s*\/?>/i', ' ', $phrase));
    $key = 'Cond' . preg_replace('/[^A-Za-z]/', '', ucwords(strtolower($words)));
    $t = w34t($key, '');
    if ($t === '') return $phrase;
    $sep = (strpos($phrase, "\n") !== false) ? "\n" : ((stripos($phrase, '<br') !== false) ? '<br>' : ' ');
    return str_replace('|', $sep, $t);
}

// Translate the English words in a data-layer string, e.g. 'Rise<moonrisecolor> 14:03'.
// $words maps English text => lang key.
function w34_words($text, $words) {
    if (w34_is_english() || $text === null || $text === '') return $text;
    $map = array();
    foreach ($words as $english => $key) $map[$english] = w34t($key, $english);
    return strtr($text, $map);
}

// Compass abbreviation ('SSW', 'N', 'NNW') in the active language, e.g. French 'SSO', Greek 'ΝΝΔ'.
// The 16-point keys predate this helper; N/E/S/W and NNW (stored as 'NWN') get their own keys.
function w34_dir($abbr) {
    if (w34_is_english() || !is_string($abbr) || !preg_match('/^[NESW]{1,3}$/', trim($abbr))) return $abbr;
    $a = trim($abbr);
    $keys = array('N' => 'DirN', 'E' => 'DirE', 'S' => 'DirS', 'W' => 'DirW', 'NNW' => 'DirNNW');
    return w34t(isset($keys[$a]) ? $keys[$a] : $a, $a);
}

// Meteor shower event text, e.g. 'Orionids peak', 'Approaching Lyrids' or the popup's
// 'Meteor Shower<br> <orange1>Orionids</orange1> ... Active Oct 21st-Oct 22nd ... Peaks ...'.
function w34_meteor($text) {
    if (w34_is_english() || $text === null || $text === '') return $text;
    $words = array('Meteor Shower' => 'MeteorShower', 'Peak Viewing Now' => 'PeakViewingNow',
        'Estimated ZHR' => 'EstimatedZHR', 'Peaks' => 'Peaks', 'Active' => 'Active',
        'Approaching' => 'MeteorApproaching', 'peak' => 'MeteorPeak', 'passed' => 'MeteorPassed',
        'Meteor' => 'MeteorWord', 'Quadrantids' => 'Quadrantids', 'Lyrids' => 'Lyrids',
        'ETA Aquarids' => 'EtaAquarids', 'Delta Aquarids' => 'DeltaAquarids', 'Perseids' => 'Perseids',
        'Draconids' => 'Draconids', 'Orionids' => 'Orionids', 'South Taurids' => 'SouthTaurids',
        'North Taurids' => 'NorthTaurids', 'Leonids' => 'Leonids', 'Geminids' => 'Geminids', 'Ursids' => 'Ursids');
    return w34_datetext(w34_words($text, $words));
}

// A multi-line label ('West South<br><span>West</span>') on one line.
function w34_nobr($s) {
    return preg_replace('/\s*<br\s*\/?>\s*/i', ' ', $s);
}

// Moon phase name from the data layer ('First Quarter') -> existing phase keys.
function w34_moonphase($english) {
    $keys = array('New Moon' => 'Newmoon', 'Waxing Crescent' => 'Waxingcrescent', 'First Quarter' => 'Firstquarter',
        'Waxing Gibbous' => 'Waxinggibbous', 'Full Moon' => 'Fullmoon', 'Waning Gibbous' => 'Waninggibbous',
        'Last Quarter' => 'Lastquarter', 'Waning Crescent' => 'Waningcrescent');
    return isset($keys[$english]) ? w34t($keys[$english], $english) : $english;
}
