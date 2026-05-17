<?php
if ($notifications == 'yes') {

    # Check battery levels
    if (($notifyBattery ?? 'yes') == 'yes' && ($weather['consoleLowBattery'] || $weather['stationLowBattery'])) {?>
        <div id="weather34lightningdialog-notify">
            <div class="weather34lightningdialog-box">
                <div class="weather34lightningbackground-alert"></div>
                <div class="header">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="weather34lightningcontents">
                        <div class="left"><?php echo $notifyAlert.' '.$lang['notifyAlert'];?></div>
                        <div class="right"><?php echo date ("D H:i")?></div>
                    </div>
                </div>
                <div class="weather34lightningcontents weather34lightningmain-content">
                    <?php echo $lang['notifyLowBatteryAlert'];?><br/>
                    <notifyred>
                        <?php if ($weather['consoleLowBattery'] && $weather['stationLowBattery']) {
                            echo $lang['notifyConsoleLowBattery'].'<br/>';
                            echo $lang['notifyStationLowBattery'];
                        } else if ($weather['consoleLowBattery']) {
                            echo $lang['notifyConsoleLowBattery'];
                        } else if ($weather['stationLowBattery']){
                            echo $lang['notifyStationLowBattery'];
                        } else {?>
                            Not sure why you're seeing this...
                        <?php }?>
                    </notifyred>
                </div>
            </div>
        </div>
    <?php }

    //WEATHER34 pure css UV-Index pop up alert
    if (($notifyUV ?? 'yes') == 'yes' && $weather["uv"] >= ($notifyUVThreshold ?? 8)){?>
        <div id="weather34lightningdialog-notify">
            <div class="weather34lightningdialog-box">
                <div class="weather34lightningbackground-alert"></div>
                <div class="header">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="weather34lightningcontents">
                        <div class="left"><?php echo $notification.' '.$lang['notifyTitle'];?></div>
                        <div class="right"><?php echo date ("D H:i")?></div>
                    </div>
                </div>
                <div class="weather34lightningcontents weather34lightningmain-content">
                    <?php echo $lang['notifyUVIndex'];?><br/>
                    <?php echo $lang['notifyUVExposure'];?> <notifyorange><?php echo $weather["uv"];?></notifyorange><?php echo $uvisvg;?>
                </div>
            </div>
        </div>
    <?php }

    //WEATHER34 pure css temperature heat index pop up alert
    if(($notifyHeatIndex ?? 'yes') == 'yes' && anyToC($weather["heatindex"]) >= anyToC($notifyHeatIndexThreshold ?? 84)){?>
        <div id="weather34lightningdialog-notify">
            <div class="weather34lightningdialog-box">
                <div class="weather34lightningbackground-alert"></div>
                <div class="header">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="weather34lightningcontents">
                        <div class="left"><?php echo $notification.' '.$lang['notifyTitle'];?></div>
                        <div class="right"><?php echo date ("D H:i")?></div>
                    </div>
                </div>
                <div class="weather34lightningcontents weather34lightningmain-content">
                    <?php echo $lang['Heatindexalert'];?><br/>
                    <?php echo $lang['notifyHeatExaustion'];?> <notifyorange><?php echo $weather["heatindex"];?>&deg;<?php echo $weather["temp_units"];?></notifyorange>
                </div>
            </div>
        </div>
    <?php }

    //WEATHER34 pure css wind gusts of 40kmh to Gale Force pop up alert
    if ($notifyWind == 'yes') {
        if($weather["wind_gust_speed"]*$toKnots>=39.9 || $weather["wind_speed_avg30"]*$toKnots>=21.7){?>
            <div id="weather34lightningdialog-notify">
                <div class="weather34lightningdialog-box">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="header">
                        <div class="weather34lightningbackground-alert"></div>
                        <div class="weather34lightningcontents">
                            <div class="left"><?php echo $notification.' '.$lang['notifyTitle'];?></div>
                            <div class="right"><?php echo date ("D H:i")?></div>
                        </div>
                    </div>
                    <div class="weather34lightningcontents weather34lightningmain-content">
                        <?php if ($weather["wind_gust_speed"]*$toKnots>=99.9 || $weather["wind_speed"]*$toKnots>=99.9) {?>
                            <?php echo $lang['notifyExtremeWind'];?><br/>
                            <?php echo $lang['notifyGustUpTo'];?> <notifyred><?php echo $weather["wind_gust_speed"];?></notifyred> <?php echo $weather["wind_units"];?><br/>
                            <?php echo $lang['notifySeekShelter'];
                        } else if($weather["wind_gust_speed"]*$toKnots>=50 || $weather["wind_speed_avg30"]*$toKnots>=34) {?>
                            <?php echo $lang['notifyHighWindWarning'];?><br/>
                            <?php echo $lang['notifyGustUpTo'];?> <notifyorange><?php echo $weather["wind_gust_speed"];?></notifyorange> <?php echo $weather["wind_units"];?><br/>
                            <?php echo $lang['notifySustainedAvg'];?>: <notifyorange><?php echo $weather["wind_speed_avg30"];?></notifyorange> <?php echo $weather["wind_units"];
                        } else {?>
                            <?php echo $lang['notifyWindAdvisory'];?><br/>
                            <?php echo $lang['notifyGustUpTo'];?> <notifyorange><?php echo $weather["wind_gust_speed"];?></notifyorange> <?php echo $weather["wind_units"];?><br/>
                            <?php echo $lang['notifySustainedAvg'];?>: <notifyorange><?php echo $weather["wind_speed_avg30"];?></notifyorange> <?php echo $weather["wind_units"];
                        }?>
                    </div>
                </div>
            </div>
        <?php }
    }

    //WEATHER34 pure css wind chill pop up alert
    if(($notifyWindChill ?? 'yes') == 'yes' && anyToC($weather["windchill"]) <= anyToC($notifyWindChillThreshold ?? 32)){?>
        <div id="weather34lightningdialog-notify">
            <div class="weather34lightningdialog-box">
                <div class="weather34lightningbackground-alert"></div>
                <div class="header">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="weather34lightningcontents">
                        <div class="left"><?php echo $notification.' '.$lang['notifyTitle'];?></div>
                        <div class="right"><?php echo date ("D H:i")?></div>
                    </div>
                </div>
                <div class="weather34lightningcontents weather34lightningmain-content">
                    <?php echo $freezing.' '.$lang['Windchillalert'];?><br/>
                    <?php echo $lang['notifyFreezing'];?> <notifyblue><?php echo $weather["windchill"];?>&deg;<?php echo $weather["temp_units"];?></notifyblue>
                </div>
            </div>
        </div>
    <?php }

    //WEATHER34 pure css near freezing dewpoint pop up alert
    if(($notifyDewpoint ?? 'yes') == 'yes' && anyToC($weather["dewpoint"]) <= anyToC($notifyDewpointThreshold ?? 32)){?>
        <div id="weather34lightningdialog-notify">
            <div class="weather34lightningdialog-box">
                <div class="weather34lightningbackground-alert"></div>
                <div class="header">
                    <div class="weather34lightningbackground-alert"></div>
                    <div class="weather34lightningcontents">
                        <div class="left"><?php echo $notification.' '.$lang['notifyTitle'];?></div>
                        <div class="right"><?php echo date ("D H:i")?></div>
                    </div>
                </div>
                <div class="weather34lightningcontents weather34lightningmain-content">
                    <?php echo $freezing.$lang['Dewpointcolderalert'];?><br/>
                    <?php echo $lang['notifyFreezing'];?> <notifyblue><?php echo $weather["dewpoint"];?>&deg;<?php echo $weather["temp_units"];?></notifyblue>
                </div>
            </div>
        </div>
    <?php }

}?>
