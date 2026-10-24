<?php $units = $units ?? ''; ?>
<div class="weather34admin">
  
  <!-- Top Bar -->
  <header class="weather34admin__header">
    <div class="weather34toolbar">
      <div class="weather34toolbar__left">
        <button class="weather34btn weather34btn--primary"></button>
<?php
  // Sensor health check-engine light. Reads persisted signal/battery columns
  // from the archive -- see [GW1000] field_map_extensions in weewx.conf.
  include_once('w34_i18n.php');
  include_once('sensor_health.php');
  $w34h    = w34_sensor_health();
  $w34hTip = $w34h['state'] === 'ok'
           ? w34t('AllSensorsOk', 'All sensors reporting normally')
           : implode(' | ', $w34h['issues']);
  $w34hWords = array('ok' => mb_strtoupper(w34t('Sensors', 'Sensors')), 'warn' => w34t('HealthWarn', 'WARN'),
                     'fault' => w34t('HealthFault', 'FAULT'));
  $w34hTxt = $w34hWords[$w34h['state']] ?? strtoupper($w34h['state']);
?>
        <weather34mbhealth id="w34health" class="is-<?php echo $w34h['state']; ?>"
          title="<?php echo htmlspecialchars($w34hTip, ENT_QUOTES); ?>">
          <a href="pop_sensors.php" data-lity>
            <span class="w34health__dot"></span>
            <span class="w34health__txt"><?php echo $w34hTxt; ?></span>
          </a>
        </weather34mbhealth>
        <script>
        (function () {
          var el = document.getElementById('w34health');
          if (!el) return;
          function poll() {
            fetch('sensor_health.php', { cache: 'no-store' })
              .then(function (r) { return r.json(); })
              .then(function (d) {
                el.className = 'is-' + d.state;
                el.title = d.state === 'ok'
                  ? <?php echo json_encode(w34t('AllSensorsOk', 'All sensors reporting normally')); ?>
                  : d.issues.join(' | ');
                var t = el.querySelector('.w34health__txt');
                var words = <?php echo json_encode($w34hWords); ?>;
                if (t) t.textContent = words[d.state] || String(d.state).toUpperCase();
              })
              .catch(function () { /* leave last known state on the light */ });
          }
          setInterval(poll, 60000);
        })();
        </script>
      </div>
      <div class="weather34toolbar__center">
        <button class="weather34btn weather34btn--primary">
          <weather34toptitle><?php echo $stationName; ?>&nbsp; <?php echo w34t('Weatherstation', 'Weather Station'); ?></weather34toptitle>
        </button>
      </div>
      <div class="weather34toolbar__right">
        <weather34mbuptime>
          <?php
          if (w34_is_english()) {
            echo "Server " . (shell_exec('uptime -p'));
          } else {
            // uptime -p is English-only: rebuild it from /proc/uptime with translated units
            $up = (int) floatval(@file_get_contents('/proc/uptime'));
            $parts = array();
            foreach (array(array(86400, 'UnitDay', 'day', 'UnitDays', 'days'), array(3600, 'UnitHour', 'hour', 'UnitHours', 'hours'), array(60, 'UnitMinute', 'minute', 'UnitMinutes', 'minutes')) as $u) {
              $n = intdiv($up, $u[0]); $up -= $n * $u[0];
              if ($n > 0) $parts[] = $n . ' ' . ($n == 1 ? w34t($u[1], $u[2]) : w34t($u[3], $u[4]));
            }
            echo w34t('ServerUp', 'Server up') . ' ' . implode(', ', $parts);
          }
          ?>
        </weather34mbuptime>
          <?php if ($units != 'us')
{ ?>
            <a href="./?units=us" title="<?php echo htmlspecialchars(w34t('SelectImperial', 'Select Imperial')); ?>"><topbarimperial>&deg;F</topbarimperial></a>
          <?php
}
else
{ ?>
            <a href="./?units=metric" title="<?php echo htmlspecialchars(w34t('SelectMetric', 'Select Metric')); ?>"><topbarmetric>&deg;C</topbarmetric></a>
          <?php
} ?>
      </div>
    </div>
  </header>


  <!-- Side Menu -->
  <input type="checkbox" class="openweather34sidebarMenu" id="openweather34sidebarMenu"/>
  <label for="openweather34sidebarMenu" class="weather34sidebarIconToggle">
    <div class="weather34spinner weather34cross part-1"></div>
    <div class="weather34spinner weather34horizontal"></div>
    <div class="weather34spinner weather34cross part-2"></div>
  </label>
  <div id="weather34sidebarMenu">
    <ul class="weather34sidebarMenuInner">
      <br/>
      <br/>
      <br/>
      
      
      <li class="header"><?php echo mb_strtoupper(w34t('MenuAdmin', 'Admin')); ?></li>
      <li>
        <a href="templateSetup.php" target="_blank" title="<?php echo htmlspecialchars(w34t('Settings', 'Settings')); ?>">
          <weather34menumarkeryellow></weather34menumarkeryellow> <?php echo $lang['Settings']; ?>
        </a>
      </li>
      <li>
        <a href="index.php" title="<?php echo htmlspecialchars(w34t('MenuHome', 'Home')); ?>">
          <weather34menumarkergreen></weather34menumarkergreen> <?php echo w34t('MenuHome', 'Home'); ?>
        </a>
      </li>
      
      
      <li class="header"><?php echo mb_strtoupper(w34t('MenuTheme', 'UI Theme')); ?></li>
      <li>
        <a href="<?php echo ($theme == 'dark') ? '?theme=light' : '?theme=dark'; ?>">
          <?php if ($theme == 'dark')
{ ?>
            <weather34menumarkerlight></weather34menumarkerlight> <?php echo w34t('Lighttheme', 'Light Theme'); ?>
          <?php
}
else
{ ?>
            <weather34menumarkerbluegrey></weather34menumarkerbluegrey> <?php echo w34t('Darktheme', 'Dark Theme'); ?>
          <?php
} ?>
        </a>
      </li>
      
      
      <li class="header"><?php echo mb_strtoupper(w34t('MenuUnits', 'Units')); ?></li>
      <?php if ($units != Null && $units != 'default')
{ ?>
        <li>
          <a href="./?units=default"><weather34menumarkerred></weather34menumarkerred> <?php echo w34t('DefaultUnits', 'Default Units'); ?>
            <?php if ($tempunit == 'F')
    { ?>
              <topbarimperialf>&deg;F</topbarimperialf>
            <?php
    }
    else
    { ?>
              <topbarmetricc>&deg;C</topbarmetricc>
            <?php
    } ?>
          </a>
        </li>
      <?php
}
if ($units != 'us')
{ ?>
        <li>
          <a href="./?units=us"><weather34menumarkerorange></weather34menumarkerorange> <?php echo w34t('Imperial', 'Imperial'); ?> <topbarimperialf>&deg;F</topbarimperialf></a>
        </li>
      <?php
}
if ($units != 'metric')
{ ?>
        <li>
          <a href="./?units=metric"><weather34menumarkerblue></weather34menumarkerblue> <?php echo w34t('MetricUnits', 'Metric'); ?> <topbarmetricc>&deg;C</topbarmetricc></a>
        </li>
      <?php
}
if ($units != 'uk')
{ ?>
        <li>
          <a href="./?units=uk"><weather34menumarkeryellow></weather34menumarkeryellow> UK (MPH) <topbarmetricc>&deg;C</topbarmetricc></a>
        </li>
      <?php
}
if ($units != 'scandinavia')
{ ?>
        <li>
          <a href="./?units=scandinavia"><weather34menumarkerred></weather34menumarkerred> M/S <topbarmetricc>&deg;C</topbarmetricc></a>
        </li>
      <?php
}
if ($units != 'ca')
{ ?>
        <li>
          <a href="./?units=ca"><weather34menumarkerred></weather34menumarkerred> CA (kPa) <topbarmetricc>&deg;C</topbarmetricc></a>
        </li>
      <?php
}

if ($extralinks == 'yes')
{ ?>
      <li class="header sub"><?php echo mb_strtoupper(w34t('MenuLinks', 'Links')); ?> <img class="menuimg" src="img/arrow34iconlink.svg" alt=""/>
        <ul>
          <?php if ($linkWU == 'yes')
    { ?>
            <li>
              <?php # if its linkWUNew is either yes or missing, use the new WU Site, else use the old site
         ?>
              <a href="<?php echo ($linkWUNew == 'yes' || empty($linkWUNew) ? 'https://www.wunderground.com/dashboard/pws/' : 'https://www.wunderground.com/personal-weather-station/dashboard?id=') . $id; ?>" title="<?php echo $id; ?> on Weather Underground" target="_blank">
                <img class="menuimg" src="img/wulogo.svg" style="width:30px" alt=""/>
                <?php echo w34f('OnSite', '%s on %s', $id, 'WU'); ?>
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkCWOPID))
    { ?>
            <li>
              <a href="https://weather.gladstonefamily.net/site/<?php echo $linkCWOPID; ?>" title="<?php echo $linkCWOPID; ?> on CWOP" target="_blank">
                <img class="menuimg" src="img/arrow34iconlink.svg" alt=""/>
                <?php echo $linkCWOPID; ?> on CWOP
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkFindUID))
    { ?>
            <li>
              <a href="http://www.findu.com/cgi-bin/wxpage.cgi?call=<?php echo $linkFindUID; ?>&last=48" title="<?php echo $linkFindUID; ?> on Findu.com" target="_blank">
                <img class="menuimg" src="img/arrow34iconlink.svg" alt=""/>
                <?php echo $linkFindUID; ?> on FindU.com
              </a>
            </li>
          <?php
    } ?>
          <?php if (($linkNOAA == 'yes') && (!empty($linkCWOPID) && (empty($linkNOAAID))))
    { ?>
            <li>
              <a href="https://www.wrh.noaa.gov/mesowest/getobext.php?wfo=lox&sid=<?php echo $linkCWOPID; ?>" title="<?php echo $linkCWOPID; ?> on NOAA Meso West" target="_blank">
                <img class="menuimg" src="img/noaa.svg" style="max-width:30px" alt=""/>
                <?php echo $linkCWOPID; ?> on NOAA
              </a>
            </li>
          <?php
    } ?>
         <?php if ($linkNOAA == 'yes' && !empty($linkNOAAID))
    { ?>
            <li>
              <a href="https://www.wrh.noaa.gov/mesowest/getobext.php?wfo=lox&sid=<?php echo $linkNOAAID; ?>" title="<?php echo $linkNOAAID; ?> on NOAA Meso West" target="_blank">
                <img class="menuimg" src="img/noaa.svg" style="max-width:30px" alt=""/>
                <?php echo $linkNOAAID; ?> on NOAA
              </a>
            </li>
          <?php
    } ?>
          <?php if ($linkMADIS == 'yes' && !empty($linkCWOPID))
    { ?>
            <li>
              <a href="https://madis-data.ncep.noaa.gov/MadisSurface/?CenterLAT=<?php echo $lat; ?>&CenterLON=<?php echo $lon; ?>&Zoom=11.00&StationID=<?php echo $linkCWOPID; ?>" title="<?php echo $linkCWOPID; ?> on MADIS Map" target="_blank">
                <img class="menuimg" src="img/noaa.svg" style="max-width:30px" alt=""/>
                <?php echo $linkCWOPID; ?> on NOAA MADIS Map
              </a>
            </li>
          <?php
    } ?>
          <?php if (($linkMesoWest == 'yes') && (!empty($linkCWOPID) && (empty($linkMesoWestID))))
    { ?>
            <li>
              <a href="https://mesowest.utah.edu/cgi-bin/droman/meso_base.cgi?stn=<?php echo $linkCWOPID; ?>" title="<?php echo $linkCWOPID; ?> on Meso West" target="_blank">
                <img class="menuimg" src="img/mesowest.svg" alt=""/>
                <?php echo $linkCWOPID; ?> on Meso West
              </a>
            </li>
          <?php
    } ?>
          <?php if ($linkMesoWest == 'yes' && !empty($linkMesoWestID))
    { ?>
            <li>
              <a href="https://mesowest.utah.edu/cgi-bin/droman/meso_base.cgi?stn=<?php echo $linkMesoWestID; ?>" title="<?php echo $linkMesoWestID; ?> on Meso West" target="_blank">
                <img class="menuimg" src="img/mesowest.svg" alt=""/>
                <?php echo $linkMesoWestID; ?> on Meso West
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkWeatherCloudID))
    { ?>
            <li>
              <a href="https://app.weathercloud.net/<?php echo $linkWeatherCloudID; ?>" title="View on WeatherCloud" target="_blank">
                <img class="menuimg" src="img/weathercloud.svg" style="width:21px" alt=""/> 
                View on WeatherCloud
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkWindyID))
    { ?>
            <li>
              <a href="https://www.windy.com/station/pws-<?php echo $linkWindyID . "?" . $lat . "," . $lon; ?>,8" title="View on Windy.com" target="_blank">
                <img class="menuimg" src="img/windy.svg" style="width:21px" alt=""/>
                View on Windy.com
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkAWEKASID))
    { ?>
            <li>
              <a href="https://www.awekas.at/en/instrument.php?id=<?php echo $linkAWEKASID; ?>" title="View on AWEKAS" target="_blank">
                <img class="menuimg" src="img/awekas.svg" alt=""/>
                View on AWEKAS
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkAmbientWeatherID))
    { ?>
            <li>
              <a href="https://dashboard.ambientweather.net/devices/public/<?php echo $linkAmbientWeatherID; ?>" title="Ambient Weather" target= "_blank">
                <img class="menuimg" src="img/ambientweather.svg" alt=""/>
                View on Ambient Weather
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkPWSWeatherID))
    { ?>
            <li>
              <a href="https://www.pwsweather.com/obs/<?php echo $linkPWSWeatherID; ?>.html" title="PWS Weather" target="_blank">
                <img style="background-color:white" class="menuimg" src="img/pwslogo.svg" alt=""/>
                View on PWS Weather
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkMetOfficeID))
    { ?>
            <li>
              <a href="http://wow.metoffice.gov.uk/observations/details?site_id=<?php echo $linkMetOfficeID; ?>" title="MetOffice/WoW" target="_blank">
                <img class="menuimg" src="img/metoffice.svg" alt=""/>
                View on MetOffice/WoW
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkCustom1Title))
    { ?>
            <li>
              <a href="<?php echo $linkCustom1URL; ?>" title="<?php echo $linkCustom1Title; ?>" target="_blank">
                <img class="menuimg" src="img/arrow34iconlink.svg" alt=""/>
                <?php echo $linkCustom1Title; ?>
              </a>
            </li>
          <?php
    } ?>
          <?php if (!empty($linkCustom2Title) && !empty($linkCustom2URL))
    { ?>
            <li>
              <a href="<?php echo $linkCustom2URL; ?>" title="<?php echo $linkCustom2Title; ?>" target="_blank">
                <img class="menuimg" src="img/arrow34iconlink.svg" alt=""/>
                <?php echo $linkCustom2Title; ?>
              </a>
            </li>
          <?php
    } ?>
        </ul>
      </li>
      <?php
} ?>
      <li class="header"><?php echo mb_strtoupper(w34t('MenuExtras', 'Extras')); ?></li>
      <?php if ($weatherflowoption == "yes")
{ ?>
        <li>
          <a href="https://tempestwx.com/map/<?php echo $lat . '/' . $lon . '/' . $weatherflowmapzoom; ?>" data-lity title='see your weather station on official weatherflow map'>
            <weather34menumarkerblue></weather34menumarkerblue> Weatherflow Map
          </a>
        </li>
      <?php
}
if (!empty($webcamurl) && $webcamurl != ' ' && $webcamurl != 'Null' && $webcamurl != 'null')
{ ?>
        <li><!--webcam-->
          <a href="cam.php" data-lity title="<?php echo htmlspecialchars(w34t('Webcam', 'Webcam')); ?>"><weather34menumarkeryellow></weather34menumarkeryellow> <?php echo w34t('Webcam', 'Webcam'); ?></a>
        </li>
      <?php
} ?>
      <li><!--contact info-->
        <a href="bio.php" data-lity title="<?php echo htmlspecialchars(w34t('Contactinfo', 'Station & Contact Info')); ?>"><weather34menumarkerorange></weather34menumarkerorange> <?php echo w34t('ContactCard', 'Contact Card'); ?></a>
      </li>
      <li><!--hardware info-->
        <a href="stationinfo.php" data-lity title="<?php echo htmlspecialchars(w34t('StationHardware', 'Weather Station Hardware')); ?>"><weather34menumarkerred></weather34menumarkerred> <?php echo w34t('HardwareInfo', 'Hardware Info'); ?></a>
      </li>
      <li><!--sensor battery status-->
        <a href="pop_sensors.php" data-lity title="<?php echo htmlspecialchars(w34t('SensorBatterySignal', 'Sensor Battery & Signal Status')); ?>"><weather34menumarkergreen></weather34menumarkergreen> <?php echo w34t('SensorStatus', 'Sensor Status'); ?></a>
      </li>
      <?php if (!empty($extraLinkTitle) && !empty($extraLinkURL) && !empty($extraLinkColor))
{ ?>
        <li>
          <a href="<?php echo $extraLinkURL; ?>" title="<?php echo $extraLinkTitle; ?>" target="_blank">
            <?php if ($extraLinkColor == 'white')
    { ?>
              <weather34menumarkerlight></weather34menumarkerlight>
            <?php
    }
    else if ($extraLinkColor == 'red')
    { ?>
              <weather34menumarkerred></weather34menumarkerred>
            <?php
    }
    else if ($extraLinkColor == 'grey')
    { ?>
              <weather34menumarkerbluegrey></weather34menumarkerbluegrey>
            <?php
    }
    else if ($extraLinkColor == 'green')
    { ?>
              <weather34menumarkergreen></weather34menumarkergreen>
            <?php
    }
    else if ($extraLinkColor == 'yellow')
    { ?>
              <weather34menumarkeryellow></weather34menumarkeryellow>
            <?php
    }
    else if ($extraLinkColor == 'blue')
    { ?>
              <weather34menumarkerblue></weather34menumarkerblue>
            <?php
    }
    else
    { ?>
              <weather34menumarkerorange></weather34menumarkerorange>
            <?php
    }
    echo $extraLinkTitle; ?>
          </a>
        </li>
      <?php
} ?>
      <!--languages-->
      <?php if ($languages == "yes")
{ ?>
        <li class="header"><?php echo $lang["language"]; ?></li>
        <li class="flagstop">
          <a href="index.php?lang=en"><img src="img/flags/en.svg" title="English" class="flags" alt="en"/></a>&nbsp;
          <a href="index.php?lang=dk"><img src="img/flags/dk.svg" title="Dansk" class="flags" alt="dk"/></a>&nbsp;
          <a href="index.php?lang=gr"><img src="img/flags/gr.svg" title="Ελληνικά" class="flags" alt="gr"/></a>&nbsp;
          <a href="index.php?lang=it"><img src="img/flags/it.svg" title="Italiano" class="flags" alt="it"/></a>&nbsp;
          <a href="index.php?lang=fr"><img src="img/flags/fr.svg" title="Français" class="flags" alt="fr"/></a>&nbsp;
        </li>

        <li class="flagsmiddle">
          <a href="index.php?lang=dl"><img src="img/flags/dl.svg" title="Deutsch" class="flags" alt="dl"/></a>&nbsp;
          <a href="index.php?lang=nl"><img src="img/flags/nl.svg" title="Nederlands" class="flags" alt="nl"/></a>&nbsp;
          <a href="index.php?lang=cat"><img src="img/flags/cat.svg" title="Català" class="flags" alt="cat"/></a>&nbsp;
          <a href="index.php?lang=sp"><img src="img/flags/sp.svg" title="Español" class="flags" alt="sp"/></a>&nbsp;
          <a href="index.php?lang=tr"><img src="img/flags/tr.svg" title="Türkçe" class="flags" alt="tr"/></a>&nbsp;
          <a href="index.php?lang=br"><img src="img/flags/br.svg" title="Português (Brasil)" class="flags" alt="br"/></a>&nbsp;
        </li>

        <li class="flagsbottom">
          <a href="index.php?lang=hu"><img src="img/flags/hu.svg" title="Magyar" class="flags" alt="hu"/></a>&nbsp;
          <a href="index.php?lang=pl"><img src="img/flags/pl.svg" title="Polski" class="flags" alt="pl"/></a>&nbsp;
          <a href="index.php?lang=sp"><img src="img/flags/ar.svg" title="Español (Argentina)" class="flags" alt="ar"/></a>&nbsp;
          <a href="index.php?lang=dl"><img src="img/flags/ch.svg" title="Deutsch (Schweiz)" class="flags" alt="ch"/></a>&nbsp;
          <a href="index.php?lang=no"><img src="img/flags/no.svg" title="Norsk" class="flags" alt="no"/></a>&nbsp;
          <a href="index.php?lang=sw"><img src="img/flags/sw.svg" title="Svenska" class="flags" alt="sw"/></a>&nbsp;
        </li>
      <?php
} ?>
      <!--credits | download info-->
      <?php // please do not remove this and if so no support is given and your domain will be blacklisted from support it is not much to ask //
 ?>
      <li>
        <a href="https://github.com/meisnick/weewx-Weather34/blob/main/INSTALLATION.md" title="<?php echo htmlspecialchars(w34t('InstallationGuide', 'Installation Guide')); ?>" target="_blank">
          <weather34menumarkerbluegrey></weather34menumarkerbluegrey> <?php echo w34t('InstallationGuide', 'Installation Guide'); ?>
        </a>
      </li>
      <li>
        <a href="https://github.com/meisnick/weewx-Weather34/" title="<?php echo htmlspecialchars(w34f('OnSite', '%s on %s', 'weewx-Weather34', 'GitHub')); ?>" target="_blank">
          <weather34menumarkerbluegrey></weather34menumarkerbluegrey> GitHub: meisnick/weewx-Weather34
        </a>
      </li>
      <li>
        <a href="templateSetup.php" title="<?php echo htmlspecialchars(w34t('DashboardSetupPage', 'Dashboard Setup Page')); ?>">
          <weather34menumarkerbluegrey></weather34menumarkerbluegrey> <?php echo w34t('DashboardSetupPage', 'Dashboard Setup Page'); ?>
        </a>
      </li>
      <li>
        <a href="https://github.com/meisnick/weewx-Weather34" title="<?php echo htmlspecialchars(w34t('ForkCredit', 'This fork by meisnick, based on the WeeWX port by Ian Millard (Steepleian)')); ?>" target="_blank">
          <weather34menumarkerbluegrey></weather34menumarkerbluegrey> <?php echo w34t('ForkShort', 'WeeWX skin (fork of Steepleian’s port)'); ?>
        </a>
      </li>
      <li>
        <a href="https://weather34.com/homeweatherstation/" title="Weather34" target="_blank">
          <weather34menumarkerbluegrey></weather34menumarkerbluegrey> <?php echo w34f('DesignedBy', 'Designed by %s', 'weather34.com'); ?>
        </a>
      </li>
      <!-- USA WeatherFinder -->
      <?php if (!empty($USAWeatherFinder))
{ ?>
        <li>
          <a href="https://usaweatherfinder.com/index.php?a=stats&u=<?php echo $USAWeatherFinder; ?>" title="<?php echo $USAWeatherFinder; ?>'s Weather Finder" target="_blank">
            <img src="https://usaweatherfinder.com/button.php?u=<?php echo $USAWeatherFinder; ?>" alt="USA Weather Finder" border="0" />
          </a>
        </li>
      <?php
} ?>
    </ul>
  </div>
</div>

