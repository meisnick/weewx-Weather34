<?php  include('shared.php');include('common.php');

// --- Load AQ data safely ---
$aqiweather = ['aqi' => 0, 'aqiozone' => 'N/A', 'time' => '--', 'city' => '--'];
$aq_pm25 = null; $aq_pm10 = null; $aq_o3 = null; $aq_no2 = null;
$aq_dominant = '';
$aq_file = 'jsondata/aq.txt';
if (file_exists($aq_file) && (time() - filemtime($aq_file) < 7200)) {
    $raw = file_get_contents($aq_file);
    $parsed_json = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($parsed_json['data'])) {
        $d = $parsed_json['data'];
        $aqiweather['aqi']  = (int)($d['aqi'] ?? 0);
        $aqiweather['time'] = date($timeFormat, $d['time']['v'] ?? time());
        $aqiweather['city'] = $d['city']['name'] ?? '--';
        $iaqi = $d['iaqi'] ?? [];
        $aq_pm25     = isset($iaqi['pm25']['v']) ? round((float)$iaqi['pm25']['v']) : null;
        $aq_pm10     = isset($iaqi['pm10']['v']) ? round((float)$iaqi['pm10']['v']) : null;
        $aq_o3       = isset($iaqi['o3']['v'])   ? round((float)$iaqi['o3']['v'])   : null;
        $aq_no2      = isset($iaqi['no2']['v'])  ? round((float)$iaqi['no2']['v'])  : null;
        $aq_dominant = strtolower($d['dominentpol'] ?? '');
    }
}

function aq_pill_bg($val) {
    if ($val === null) return 'var(--bg0)';
    if ($val > 150) return 'var(--red)';
    if ($val > 100) return 'var(--orange)';
    if ($val > 50)  return 'var(--amber)';
    return 'var(--green)';
}

?>
<div class="mod-airquality">
    <div class="updatedtime">
        <span>
            <?php if(file_exists('jsondata/aq.txt') && time() - filemtime('jsondata/aq.txt') < 7200) {
                echo $online . " " . date($timeFormat, filemtime('jsondata/aq.txt'));
            } else {
                echo $offline . ' <offline>Offline</offline>';
            }?>
        </span>
    </div>

    <div class="airqualitywordbig">Air Quality</div>

    <div class="tempconverter2">
        <?php //WEATHER34 AIR QUALITY SVG
        if ($aqiweather["aqi"] > 200) {
            echo "<div class='tempconvertercirclepurple'>PM2.5</div>";
        } else if ($aqiweather["aqi"] > 150) {
            echo "<div class='tempconvertercirclered'>PM2.5</div>";
        } else if ($aqiweather["aqi"] > 100) {
            echo "<div class='tempconvertercircleorange'>PM2.5</div>";
        } else if ($aqiweather["aqi"] > 50) {
            echo "<div class='tempconvertercircleyellow'>PM2.5</div>";
        } else if ($aqiweather["aqi"] >= 0) {
            echo "<div class='tempconvertercirclegreen'>PM2.5</div>";
        }
        ?>
    </div>

    <div class="airqualitymoduleposition">
        <div class="tempcontainer">
            <?php //WEATHER34 AIR QUALITY SVG
            if ($aqiweather["aqi"] > 300) {
                echo "<div class='air300'><img src='css/aqi/hazair.svg?ver=1.4' width='110px' height='100px' alt='weather34 hazardous air quality' title='weather34 hazardous air quality'></div>";
            } else if ($aqiweather["aqi"] > 200) {
                echo "<div class='air200'><img src='css/aqi/vhair.svg?ver=1.4' width='110px' height='100px' alt='weather34 very unhealthy air quality' title='weather34 very unhealthy air quality'></div>";
            } else if ($aqiweather["aqi"] > 150) {
                echo "<div class='air150'><img src='css/aqi/uhair.svg?ver=1.4' width='110px' height='100px' alt='weather34 unhealthy air quality' title='weather34 unhealthy air quality'></div>";
            } else if ($aqiweather["aqi"] > 100) {
                echo "<div class='air100'><img src='css/aqi/uhfsair.svg?ver=1.4' width='110px' height='100px' alt='weather34 unhealthy for some air quality' title='weather34 unhealthy for some air quality'></div>";
            } else if ($aqiweather["aqi"] > 50) {
                echo "<div class='air50'><img src='css/aqi/modair.svg?ver=1.4' width='110px' height='100px' alt='weather34 moderate air quality' title='weather34 moderate air quality'></div>";
            } else if ($aqiweather["aqi"] >= 0) {
                echo "<div class='air0'><img src='css/aqi/goodair.svg?ver=1.4' width='110px' height='100px' alt='weather34 good air quality' title='weather34 good air quality'></div>";
            }
            ?>
        </div>
    </div>

    <div class="airsvg">
        <?php
        if ($aqiweather["aqi"] > 300) {
            echo "<div class='dottedcirclered'>";
        } else if ($aqiweather["aqi"] > 200) {
            echo "<div class='dottedcirclepurple'>";
        } else if ($aqiweather["aqi"] > 150) {
            echo "<div class='dottedcirclered'>";
        } else if ($aqiweather["aqi"] > 100) {
            echo "<div class='dottedcircleorange'>";
        } else if ($aqiweather["aqi"] > 50) {
            echo "<div class='dottedcircleyellow'>";
        } else if ($aqiweather["aqi"] >= 0) {
            echo "<div class='dottedcirclegreen'>";
        }
        ?>
            <div class="airvalue">
                <?php //WEATHER34 AIR QUALITY VALUE
                echo $aqiweather["aqi"];

                //WEATHER34 air quality description
                if ($aqiweather["aqi"] > 300) {
                    echo "<br><airdescription><indoorred>&nbsp;" . $lang['Hazordous'] . "</indoorred></airdescription>";
                } else if ($aqiweather["aqi"] > 200) {
                    echo "<br><airdescription><indoorpurple>" . $lang['VeryUnhealthy'] . "</indoorpurple></airdescription>";
                } else if ($aqiweather["aqi"] > 150) {
                    echo "<br><airdescription><indoorred>&nbsp;" . $lang['Unhealthy'] . "</indoorred></airdescription>";
                } else if ($aqiweather["aqi"] > 100) {
                    echo "<br><airdescription><indoororange>" . $lang['UnhealthyFS'] . "</indoororange></airdescription>";
                } else if ($aqiweather["aqi"] > 50) {
                    echo "<br><airdescription><indooryellow>&nbsp;" . $lang['Moderate'] . "</indooryellow></airdescription>";
                } else if ($aqiweather["aqi"] >= 0) {
                    echo "<br><airdescription><indoorgreen>&nbsp; &nbsp;" . $lang['Good'] . "</indoorgreen></airdescription>";
                }
                ?>
            </div>
        </div>
    </div>

    <div class="aq-pollutants">
        <?php
        $polls = [
            'PM2.5' => $aq_pm25,
            'PM10'  => $aq_pm10,
            'O3'    => $aq_o3,
            'NO₂'   => $aq_no2,
        ];
        foreach ($polls as $label => $val):
            $bg   = aq_pill_bg($val);
            $disp = ($val !== null) ? $val : '--';
            $slug = strtolower(str_replace(['₂', '.', ' ', '₃'], ['2', '', '', '3'], $label));
            $dom  = ($slug === $aq_dominant) ? ' aq-dominant' : '';
        ?>
        <div class="aq-poll-pill<?php echo $dom; ?>" style="background:<?php echo $bg; ?>">
            <span class="aq-poll-val"><?php echo $disp; ?></span>
            <span class="aq-poll-lbl"><?php echo $label; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
