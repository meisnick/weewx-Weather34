<?php 
	####################################################################################################
	#	HOME WEATHER STATION TEMPLATE by BRIAN UNDERDOWN 2015-2016-2017                                #
	#	CREATED FOR HOMEWEATHERSTATION TEMPLATE at 													   #
	#   https://weather34.com/homeweatherstation/index.html 										   # 
	# 	WEATHER STATION TEMPLATE 2015-2017                 											   #
	# 	                                                                                               #
	#   https://www.weather34.com 	                                                                   #
	####################################################################################################
//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
include('weather34skydata.php');
include('common.php');
include_once('w34_i18n.php');
header('Content-type: text/html; charset=utf-8');?>
<?php 
// homeweatherstation sun & moon information kind of a unique approach  // updated september 4th 2016 //
//homeweatherstation calculate sunrise/set times and differences
		//date_default_timezone_set($TZ);
        $result = date_sun_info( time(), $lat, $lon );	
		// homeweatherstation sun hours graphic beta August 8th 2016//
		$sunr=date_sunrise(time(), SUNFUNCS_RET_STRING, $lat,$lon, $rise_zenith, $UTC_offset);
		$suns=date_sunset(time(), SUNFUNCS_RET_STRING, $lat,$lon, $set_zenith, $UTC_offset);
		$sunr1=date_sunrise(strtotime('+1 day', time()), SUNFUNCS_RET_STRING, $lat,$lon, $rise_zenith, $UTC_offset);
		$suns1=date_sunset(strtotime('+1 day', time()), SUNFUNCS_RET_STRING, $lat,$lon, $set_zenith, $UTC_offset);		
		$suns2 =date( 'G.i', $result['sunset'] );
		$sunr2 =date( 'G.i', $result['sunrise']  );
		$sunrisehour =date( 'G', $result['sunrise'] );
		$sunsethour =date( 'G', $result['sunset'] );
		$now  =date('G.i');
		//$diff3 = $now - $sunr ;	// alternative hours from		
		$diff4 = $suns2 - $sunr2 ; // hours of daylight
		//$diff5 = $suns - $now ; // alternative hours till				    	
        // homeweatherstation to sunset	(1)
        $startdate1=$now; 
        $enddate1=$suns; 
        $diff=strtotime($enddate1)-strtotime($startdate1); 
        $temp=$diff/86400; 
        $days1=floor($temp);  $temp=24*($temp-$days1); 
        $hours1=floor($temp);  $temp=60*($temp-$hours1); 
        $minutes1=floor($temp);  $temp=60*($temp-$minutes1);
        $seconds1=floor($temp); 		
		 // homeweatherstation from sunrise	(2)
        $startdate2=$sunr; 
        $enddate2=$now; 
        $diff=strtotime($enddate2)-strtotime($startdate2); 
        $temp=$diff/86400; 
        $days2=floor($temp);  $temp=24*($temp-$days2); 
        $hours2=floor($temp);  $temp=60*($temp-$hours2); 
        $minutes2=floor($temp);  $temp=60*($temp-$minutes2);
        $seconds2=floor($temp); 		
		 // homeweatherstation to sunrise after (00:00) midnight (3)	
        $startdate3=$now; 
        $enddate3=$sunr; 
        $diff=strtotime($enddate3)-strtotime($startdate3); 
        $temp=$diff/86400; 
        $days3=floor($temp);  $temp=24*($temp-$days3); 
        $hours3=floor($temp);  $temp=60*($temp-$hours3); 
        $minutes3=floor($temp);  $temp=60*($temp-$minutes3);
        $seconds3=floor($temp); 		
		 // homeweatherstation daylight (4)	
        $startdate4=$sunr; 
        $enddate4=$suns; 
        $diff=strtotime($enddate4)-strtotime($startdate4); 
        $temp=$diff/86400; 
        $days4=floor($temp);  $temp=24*($temp-$days4); 
        $hours4=floor($temp);  $temp=60*($temp-$hours4); 
        $minutes4=floor($temp);  $temp=60*($temp-$minutes4);
		$seconds4=floor($temp); 		
		// homeweatherstation daylight (5)	
        $startdate5=$suns; 
        $enddate5=$sunr; 
        $diff=strtotime($enddate5)-strtotime($startdate5); 
        $temp=$diff/86400; 
        $days5=floor($temp);  $temp=24*($temp-$days5); 
        $hours5=floor($temp);  $temp=60*($temp-$hours5); 
        $minutes5=floor($temp);  $temp=60*($temp-$minutes5);
		$seconds5=floor($temp);		
		// homeweatherstation daylight (6)	
        $startdate6=$now; 
        $enddate6=$sunr; 
        $diff=strtotime($enddate6)-strtotime($startdate6); 
        $temp=$diff/86400; 
        $days6=floor($temp);  $temp=24*($temp-$days6); 
        $hours6=floor($temp);  $temp=60*($temp-$hours6); 
        $minutes6=floor($temp);  $temp=60*($temp-$minutes6);
		$seconds6=floor($temp); 		
		// includes conversions added 21st Aug 2016
        $minutesdarkness = sprintf("%02d", $minutes5);
        $minutesdaylight = sprintf("%02d",$minutes4);
        $minutesdayleft = sprintf("%02d",$minutes1);
        $minutesdarkleft = sprintf("%02d",$minutes3);
        $minutesagosunrise = sprintf("%02d",$minutes2);			
		// drive the pie with daylight hours 6th september 2016	
        $dayl1 =$hours4 ;
        $dayl2 =$minutesdaylight;
        $dayl3 = '.' ;
        $daylighthourstoday= $dayl1.$dayl3 .$dayl2 ;
        // drive the pie with darkness hours 6th september 2016			
        $dark1 =$hours5 ;
        $dark2 =$minutesdarkness;
        $dark3 = '.' ;
        $darkhourstonight= $dark1.$dark3 .$dark2 ; 				
		// end homeweatherstation calculations		 
?>
<?php 
// current shower from the shared table (meteor34.php): peak / approaching / strongest active
include_once('meteor34.php');
$meteor_default = meteor34_now_text(meteor34_status()) ?? w34t('NoMeteorShower', 'No Meteor Showers');
$meteorOP = ($meteor_default !== w34t('NoMeteorShower', 'No Meteor Showers'));
?>
<div class="updatedtime1"><span><?php if(isset($livedata2)&&file_exists($livedata2)&&time()- filemtime($livedata2)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></span></div>
<div class="mod-moonphase">
<div class="moonphasemoduleposition">
<div class="moonrise1">
<svg id="weather34 moon rise" viewBox="0 0 32 32" width="6" height="6" fill="none" stroke="#ff9350" stroke-linecap="round" stroke-linejoin="round" stroke-width="10%">    <path d="M30 20 L16 8 2 20" /></svg> <?php echo $lang['Moon'];?> <br /><blueu><?php  echo w34_words($weather['moonrise'], array('Rise' => 'MoonRiseShort', 'In Transit' => 'InTransit'));?></blueu>

 
<div class="weather34moonmodulepos">
<div id="weather34moonphases"></div>
<div class="weather34moonmodule">
<svg id="weather34 simple moonphase"><circle cx="50" cy="50" r="49.5" fill="rgba(86, 95, 103, 0.8)"/><path id="weather34shape" fill="currentcolor"/></svg></div>
<script> //simple moonphase for weather34
weather34Moon();function weather34Moon() {var day = Date.now() / 86400000;var referenceweather34Moon = Date.UTC(2018, 0, 17, 2, 17, 0, 0);
var refweather34Day = referenceweather34Moon / 86400000;var phase = (day - refweather34Day) %29.530588853*1.008;var s=String;
switch (Math.round(phase / 3.75)){}document.getElementById("weather34moonphases");
var weather34moonCurve;var lf=Math.min(3-4*(phase/30),1);var lc=Math.abs(lf*50);	var lb=(lf<0) ? "0" : "1";
var rf=Math.min(3+4*((phase-30)/30),1);	var rc=Math.abs(rf*50);	var rb=(rf<0) ? "0" : "1";weather34moonCurve="M 50,0 "+ "a "+s(lc)+",50 0 0 "+lb+" 0,100 "+ "a "+s(rc)+",50 0 0 "+rb+" 0,-100";
document.getElementById("weather34shape").setAttribute("d",weather34moonCurve);}</script>      </div></div>

<div class="fullmoon1">
<svg id="weather34 full moon" viewBox="0 0 32 32" width="6" height="6" fill="#aaa" stroke="#aaa" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%"><circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
<?php echo $lang['Nextfullmoon'];?>	<br /> <div class="nextfullmoon"><value><moonm>
<?php  // homeweatherstation fullmoon info output 18th Aug
$now1 = time();
$moon1 = new MoonPhase();
echo "";
if ($now1 < $moon1->full_moon()) {echo date($dateFormat, (int)$moon1->full_moon() );}
else echo date( $dateFormat, (int)$moon1->next_full_moon() );
?></moonm></value></div>
</div>


<div class="newmoon1">
<svg id="weather34 new moon" viewBox="0 0 32 32" width="6" height="6" fill="#777" stroke="#777" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%"><circle cx="16" cy="16" r="14" /> <path d="M6 6 L26 26" /></svg>
<?php echo $lang['Nextnewmoon'];?> <div class="nextnewmoon"><value><moonm>
<?php // homeweatherstation create an instance of the next new moon
$moon = new MoonPhase();
$nextnewmoon = date( $dateFormat, (int)$moon->next_new_moon() );
//$nextnewmoon = date( 'jS-M', $moon->next_new_moon() );
echo "$nextnewmoon";
?></moonm></value></div>
</div>
<div class="moonset1">
<svg id="weather34 moon set" viewBox="0 0 32 32" width="6" height="6" fill="none" stroke="#f26c4f" stroke-linecap="round" stroke-linejoin="round" stroke-width="10%">
    <path d="M30 12 L16 24 2 12" /></svg>
<?php echo $lang['Moon']?><div class="nextnewmoon">
<?php echo  w34_words($weather['moonset'], array('Set' => 'MoonSetShort'));?> 

</div></div>
<div class="meteorshower"><svg xmlns='http://www.w3.org/2000/svg' width='10px' height='10px' viewBox='0 0 16 16'><path fill='currentcolor' d='M0 0l14.527 13.615s.274.382-.081.764c-.355.382-.82.055-.82.055L0 0zm4.315 1.364l11.277 10.368s.274.382-.081.764c-.355.382-.82.055-.82.055L4.315 1.364zm-3.032 2.92l11.278 10.368s.273.382-.082.764c-.355.382-.819.054-.819.054L1.283 4.284zm6.679-1.747l7.88 7.244s.19.267-.058.534-.572.038-.572.038l-7.25-7.816zm-5.68 5.13l7.88 7.244s.19.266-.058.533-.572.038-.572.038l-7.25-7.815zm9.406-3.438l3.597 3.285s.094.125-.029.25c-.122.125-.283.018-.283.018L11.688 4.23zm-7.592 7.04l3.597 3.285s.095.125-.028.25-.283.018-.283.018l-3.286-3.553z'/></svg>
<?php // homeweather station simple meteor shower output of major shower events  august 18 2016 beetlejuice //
echo w34_meteor($meteor_default);?>
</div>

<?php echo'<div class="weather34moonphasem2">'.w34t('MoonPhase', 'Moon Phase').'<br>'.w34_moonphase($weather["moonphase"]).'</div>
<div class="weather34luminancem2">'.w34t('Luminance', 'Luminance').'<br>'.$weather["luminance"].'% </div>';?>
</div><!-- /mod-moonphase -->
