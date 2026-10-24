 <?php //original weather34 script original css/svg/php by weather34 2015-2019 // 
include_once('w34CombinedData.php');include('settings1.php');date_default_timezone_set($TZ);
$json_string = file_get_contents("jsondata/al.txt");
$parsed_json = json_decode($json_string);
$alerttype = $parsed_json->{'warnings'}->{"regions"}[0]->{"today"}[0]->{"awareness"}->{"awareness_type"}->{"description"} ?? null;
$alertlevel = $parsed_json->{'warnings'}->{"regions"}[0]->{"today"}[0]->{"awareness"}->{"awareness_level"}->{"colour"} ?? null;
$alerttime = $parsed_json->{'warnings'}->{"regions"}[0]->{"today"}[0]->{"period"}->{"until"} ?? null;
$alertexp = $alerttime; 
$alertissued = $parsed_json->{'warnings'}->{"regions"}[0]->{"today"}[0]->{"period"}->{"from"} ?? null;
//$alertlevel = "Red";
?>

<?php 
if ($position6=="forecast3wularge.php" || $position6=="forecast3wu.php"){

$json='jsondata/wu.txt';
$weather34wuurl=file_get_contents($json);
$parsed_weather34wujson = json_decode($weather34wuurl,false);
{    
	 //thunder
	 $wuskythunder1 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[1];
	 $wuskythunder2 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[2];
	 $wuskythunder3 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[3];
	 $wuskythunder4 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[4];
	 $wuskythunder5 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[5];
	 $wuskythunder6 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[6];
	 $wuskythunder7 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[7];	
	 //rain
	 $wuskyrain1 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[1];
	 $wuskyrain2 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[2];
	 $wuskyrain3 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[3];
	 $wuskyrain4 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[4];
	 $wuskyrain5 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[5];
	 $wuskyrain6 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[6];
	 $wuskyrain7 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[7];	 
	 //snow
	 $wuskysnow1 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[1];
	 $wuskysnow2 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[2];
	 $wuskysnow3 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[3];
	 $wuskysnow4 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[4];
	 $wuskysnow5 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[5];
	 $wuskysnow6 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[6];
	 $wuskysnow7 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[7];
	 
	 }}?>

<div class="mod-advisory">
<div class="wulargeforecasthome"><div class="wulargediv">
<div class="eqcirclehomeregional"><div class="eqtexthomeregional">
<?php
if (($wuskythunder1 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue> 
   </spanelightning></div></div></div>';}
    else if (($wuskythunder2 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder3 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder4 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder5 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder6 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder7 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
  //forecast3 wu 
    else if (($wuskythunder1 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder2 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder3 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder4 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder5 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder6 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskythunder7 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalert.'</alertadvisory><alertvalue>Expect<orange> Thunder Storms</orange> This Week</alertvalue>
   </spanelightning></div></div></div>';}  
   
    //WU SNOW
  else if (($wuskysnow1 ?? null)>0 && $position6=="forecast3wularge.php")  {echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}    
  else if (($wuskysnow2 ?? null)>0 && $position6=="forecast3wularge.php")  {echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';} 
   else if (($wuskysnow3 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';} 
   else if (($wuskysnow4 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
   else if (($wuskysnow5 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}   
   else if (($wuskysnow6 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
   else if (($wuskysnow7 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}      
   
  //forecast3 wu snow
  else if (($wuskysnow1 ?? null)>0 && $position6=="forecast3wu.php" )  {echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}    
  else if (($wuskysnow2 ?? null)>0 && $position6=="forecast3wu.php" )  {echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';} 
   else if (($wuskysnow3 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';} 
   else if (($wuskysnow4 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
   else if (($wuskysnow5 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}   
   else if (($wuskysnow6 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
   else if (($wuskysnow7 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning>'.$snowalert.'<alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Snow Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}   
   
   //weather34 wu alerts rain  
   else if (($wuskyrain1 ?? null)>0 && $position6=="forecast3wularge.php")
  {echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain2 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain3 ?? null)>0 && $position6=="forecast3wularge.php") {echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain4 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain5 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain7 ?? null)>0 && $position6=="forecast3wularge.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}   
    //forecast3 wu rain
    else if (($wuskyrain1 ?? null)>0 && $position6=="forecast3wu.php")
  {echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain2 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain3 ?? null)>0 && $position6=="forecast3wu.php" ) {echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain4 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain5 ?? null)>0 && $position6=="forecast3wu.php" ){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}
    else if (($wuskyrain7 ?? null)>0 && $position6=="forecast3wu.php"){echo '<spanelightning><alertadvisory>'.$newalertcold.'</alertadvisory><alertvalue> Expect <blue>Rain Showers</blue> This Week </alertvalue>
   </spanelightning></div></div></div>';}  
  // forecast3ds.php (DarkSky) removed — API shut down
  else if (strpos($alertlevel ?? '','LightGreen') !== false)
  {echo '<spanelightning><alertadvisory><a alt="Alerts" title="Alerts" href="meteoalarmalerts.php" data-lity>'.$newalertgreen.'</a></alertadvisory><alertvalue> Currently <lightgreen>No Alerts</lightgreen></alertvalue>
  </spanelightning></div></div></div>';}  
 //WEATHER34 solar eclipse events and no alerts 
 else {echo '<spanelightning><alertvalue>'.$eclipse_default.'</alertvalue></spanelightning></div></div></div>';}   
 
  ?></div></div><!-- /mod-advisory -->

