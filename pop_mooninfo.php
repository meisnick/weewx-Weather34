<?php include_once('w34_i18n.php'); ?>
<?php 
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original legacy code (by agreement) created and developed by Brian Underdown (https://weather34.com)   #
//  for the (now superseeded) original Weather34 Template which is no longer maintained by its creator              #
//  © weather34.com original CSS/SVG/PHP 2015-2019                                                                  #
// 	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                         #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              #
// 	                                                                                                				#
//###################################################################################################################
include_once('w34CombinedData.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }

//get moon rise/set updated 29th July
class Moon{ public static function calculateMoonTimes($month,$day,$year,$lat,$lon){$utrise=$utset=0;$timezone=(int)($lon / 15);$date=self::modifiedJulianDate($month,$day,$year);$date-=$timezone / 24;$latRad=deg2rad($lat);$sinho=0.0023271056;$sglat=sin($latRad);$cglat=cos($latRad);$rise=false;$set=false;$above=false;$hour=0;$ym=self::sinAlt($date,$hour ,$lon,$cglat,$sglat)- $sinho;$above=$ym>0;while($hour<25&&(false==$set||false==$rise)){$yz=self::sinAlt($date,$hour,$lon,$cglat,$sglat)- $sinho;$yp=self::sinAlt($date,$hour + 1,$lon,$cglat,$sglat)- $sinho;$quadout=self::quad($ym,$yz,$yp);$nz=$quadout[0];$z1=$quadout[1];$z2=$quadout[2];$xe=$quadout[3];$ye=$quadout[4];if($nz==1){if($ym<0){$utrise=$hour + $z1;$rise=true;}else{$utset=$hour + $z1;$set=true;}}if($nz==2){if($ye<0){$utrise=$hour + $z2;$utset=$hour + $z1;}else{$utrise=$hour + $z1;$utset=$hour + $z2;}}$ym=$yp;$hour+=2.0;}$retVal=new stdClass();$utrise=self::convertTime($utrise);$utset=self::convertTime($utset);$retVal->moonrise=$rise?mktime($utrise['hrs'],$utrise['min'],0,$month,$day,$year):mktime(0,0,0,$month,$day + 1,$year);$retVal->moonset=$set?mktime($utset['hrs'],$utset['min'],0,$month,$day,$year):mktime(0,0,0,$month,$day + 1,$year);return $retVal;} private static function quad($ym,$yz,$yp){$nz=$z1=$z2=0;$a=0.5 *($ym + $yp)- $yz;$b=0.5 *($yp - $ym);$c=$yz;$xe=-$b /(2 * $a);$ye=($a * $xe + $b)* $xe + $c;$dis=$b * $b - 4 * $a * $c;if($dis>0){$dx=0.5 * sqrt($dis)/ abs($a);$z1=$xe - $dx;$z2=$xe + $dx;$nz=abs($z1)<1?$nz + 1:$nz;$nz=abs($z2)<1?$nz + 1:$nz;$z1=$z1<-1?$z2:$z1;}return array($nz,$z1,$z2,$xe,$ye);} private static function sinAlt($mjd,$hour,$glon,$cglat,$sglat){$mjd+=$hour / 24;$t=($mjd - 51544.5)/ 36525;$objpos=self::minimoon($t);$ra=$objpos[1];$dec=$objpos[0];$decRad=deg2rad($dec);$tau=15 *(self::lmst($mjd,$glon)- $ra);return $sglat * sin($decRad)+ $cglat * cos($decRad)* cos(deg2rad($tau));} private static function degRange($x){$b=$x / 360;$a=360 *($b -(int)$b);$retVal=$a<0?$a + 360:$a;return $retVal;} private static function lmst($mjd,$glon){$d=$mjd - 51544.5;$t=$d / 36525;$lst=self::degRange(280.46061839 + 360.98564736629 * $d + 0.000387933 * $t * $t - $t * $t * $t / 38710000);return $lst / 15 + $glon / 15;} private static function minimoon($t){$p2=6.283185307;$arc=206264.8062;$coseps=0.91748;$sineps=0.39778;$lo=self::frac(0.606433 + 1336.855225 * $t);$l=$p2 * self::frac(0.374897 + 1325.552410 * $t);$l2=$l * 2;$ls=$p2 * self::frac(0.993133 + 99.997361 * $t);$d=$p2 * self::frac(0.827361 + 1236.853086 * $t);$d2=$d * 2;$f=$p2 * self::frac(0.259086 + 1342.227825 * $t);$f2=$f * 2;$sinls=sin($ls);$sinf2=sin($f2);$dl=22640 * sin($l);$dl+=-4586 * sin($l - $d2);$dl+=2370 * sin($d2);$dl+=769 * sin($l2);$dl+=-668 * $sinls;$dl+=-412 * $sinf2;$dl+=-212 * sin($l2 - $d2);$dl+=-206 * sin($l + $ls - $d2);$dl+=192 * sin($l + $d2);$dl+=-165 * sin($ls - $d2);$dl+=-125 * sin($d);$dl+=-110 * sin($l + $ls);$dl+=148 * sin($l - $ls);$dl+=-55 * sin($f2 - $d2);$s=$f +($dl + 412 * $sinf2 + 541 * $sinls)/ $arc;$h=$f - $d2;$n=-526 * sin($h);$n+=44 * sin($l + $h);$n+=-31 * sin(-$l + $h);$n+=-23 * sin($ls + $h);$n+=11 * sin(-$ls + $h);$n+=-25 * sin(-$l2 + $f);$n+=21 * sin(-$l + $f);$L_moon=$p2 * self::frac($lo + $dl / 1296000);$B_moon=(18520.0 * sin($s)+ $n)/ $arc;$cb=cos($B_moon);$x=$cb * cos($L_moon);$v=$cb * sin($L_moon);$w=sin($B_moon);$y=$coseps * $v - $sineps * $w;$z=$sineps * $v + $coseps * $w;$rho=sqrt(1 - $z * $z);$dec=(360 / $p2)* atan($z / $rho);$ra=(48 / $p2)* atan($y /($x + $rho));$ra=$ra<0?$ra + 24:$ra;return array($dec,$ra);} private static function frac($x){$x-=(int)$x;return $x<0?$x + 1:$x;} private static function modifiedJulianDate($month,$day,$year){if($month<=2){$month+=12;$year--;}$a=10000 * $year + 100 * $month + $day;$b=0;if($a<=15821004.1){$b=-2 *(int)(($year + 4716)/ 4)- 1179;}else{$b=(int)($year / 400)-(int)($year / 100)+(int)($year / 4);}$a=365 * $year - 679004;return $a + $b +(int)(30.6001 *($month + 1))+ $day;} private static function convertTime($hours){include('settings.php');$hrs=(int)($hours * 60 + 0.5)/ 60.0;$h=(int)($hrs);$m=(int)(60 *($hrs - $h)+ 0.5);return array('hrs'=>$h + $moonadj,'min'=>$m);} } $Moon=Moon::calculateMoonTimes($months,$days,$years,$lat,$lon); $MoonRise=$Moon->moonrise; $MoonSet=$Moon->moonset; $MoonRise=date($MoonRise); $MoonSet=date($MoonSet); class MoonPhase{ private $timestamp; private $phase; private $illum; private $age; private $dist; private $angdia; private $sundist; private $sunangdia; private $synmonth; private $quarters=null; function __construct($pdate=null){if(is_null($pdate))$pdate=time();$epoch=2444238.5;$elonge=278.833540;$elongp=282.596403;$eccent=0.016718;$sunsmax=1.495985e8;$sunangsiz=0.533128;$mmlong=64.975464;$mmlongp=349.383063;$mlnode=151.950429;$minc=5.145396;$mecc=0.054900;$mangsiz=0.5181;$msmax=384401;$mparallax=0.9507;$synmonth=29.53058868;$zenith=90+(50/60);$this->synmonth=$synmonth;$lunatbase=2423436.0;$this->timestamp=$pdate;$pdate=$pdate / 86400 + 2440587.5;$Day=$pdate - $epoch;$N=$this->fixangle((360 / 365.2422)* $Day);$M=$this->fixangle($N + $elonge - $elongp);$Ec=$this->kepler($M,$eccent);$Ec=sqrt((1 + $eccent)/(1 - $eccent))* tan($Ec / 2);$Ec=2 * rad2deg(atan($Ec));$Lambdasun=$this->fixangle($Ec + $elongp);$F=((1 + $eccent * cos(deg2rad($Ec)))/(1 - $eccent * $eccent));$SunDist=$sunsmax / $F;$SunAng=$F * $sunangsiz;$ml=$this->fixangle(13.1763966 * $Day + $mmlong);$MM=$this->fixangle($ml - 0.1114041 * $Day - $mmlongp);$MN=$this->fixangle($mlnode - 0.0529539 * $Day);$Ev=1.2739 * sin(deg2rad(2 *($ml - $Lambdasun)- $MM));$Ae=0.1858 * sin(deg2rad($M));$A3=0.37 * sin(deg2rad($M));$MmP=$MM + $Ev - $Ae - $A3;$mEc=6.2886 * sin(deg2rad($MmP));$A4=0.214 * sin(deg2rad(2 * $MmP));$lP=$ml + $Ev + $mEc - $Ae + $A4;$V=0.6583 * sin(deg2rad(2 *($lP - $Lambdasun)));$lPP=$lP + $V;$NP=$MN - 0.16 * sin(deg2rad($M));$y=sin(deg2rad($lPP - $NP))* cos(deg2rad($minc));$x=cos(deg2rad($lPP - $NP));$Lambdamoon=rad2deg(atan2($y,$x))+ $NP;$BetaM=rad2deg(asin(sin(deg2rad($lPP - $NP))* sin(deg2rad($minc))));$MoonAge=$lPP - $Lambdasun;$MoonPhase=(1 - cos(deg2rad($MoonAge)))/ 2;$MoonDist=($msmax *(1 - $mecc * $mecc))/(1 + $mecc * cos(deg2rad($MmP + $mEc)));$MoonDFrac=$MoonDist / $msmax;$MoonAng=$mangsiz / $MoonDFrac;$this->phase=$this->fixangle($MoonAge)/ 360;$this->illum=$MoonPhase;$this->age=$synmonth * $this->phase;$this->dist=$MoonDist;$this->angdia=$MoonAng;$this->sundist=$SunDist;$this->sunangdia=$SunAng;} private function fixangle($a){return($a - 360 * floor($a / 360));} private function kepler($m,$ecc){$epsilon=0.000001;$e=$m=deg2rad($m);do{$delta=$e - $ecc * sin($e)- $m;$e-=$delta /(1 - $ecc * cos($e));}while(abs($delta)>$epsilon);return $e;} private function meanphase($sdate,$k){$t=($sdate - 2415020.0)/ 36525;$t2=$t * $t;$t3=$t2 * $t;$nt1=2415020.75933 + $this->synmonth * $k + 0.0001178 * $t2 - 0.000000155 * $t3 + 0.00033 * sin(deg2rad(166.56 + 132.87 * $t - 0.009173 * $t2));return $nt1;} private function truephase($k,$phase){$apcor=false;$k+=$phase;$t=$k / 1236.85;$t2=$t * $t;$t3=$t2 * $t;$pt=2415020.75933 + $this->synmonth * $k + 0.0001178 * $t2 - 0.000000155 * $t3 + 0.00033 * sin(deg2rad(166.56 + 132.87 * $t - 0.009173 * $t2));$m=359.2242 + 29.10535608 * $k - 0.0000333 * $t2 - 0.00000347 * $t3;$mprime=306.0253 + 385.81691806 * $k + 0.0107306 * $t2 + 0.00001236 * $t3;$f=21.2964 + 390.67050646 * $k - 0.0016528 * $t2 - 0.00000239 * $t3;if($phase<0.01||abs($phase - 0.5)<0.01){$pt+=(0.1734 - 0.000393 * $t)* sin(deg2rad($m))+ 0.0021 * sin(deg2rad(2 * $m))- 0.4068 * sin(deg2rad($mprime))+ 0.0161 * sin(deg2rad(2 * $mprime))- 0.0004 * sin(deg2rad(3 * $mprime))+ 0.0104 * sin(deg2rad(2 * $f))- 0.0051 * sin(deg2rad($m + $mprime))- 0.0074 * sin(deg2rad($m - $mprime))+ 0.0004 * sin(deg2rad(2 * $f + $m))- 0.0004 * sin(deg2rad(2 * $f - $m))- 0.0006 * sin(deg2rad(2 * $f + $mprime))+ 0.0010 * sin(deg2rad(2 * $f - $mprime))+ 0.0005 * sin(deg2rad($m + 2 * $mprime));$apcor=true;}else if(abs($phase - 0.25)<0.01||abs($phase - 0.75)<0.01){$pt+=(0.1721 - 0.0004 * $t)* sin(deg2rad($m))+ 0.0021 * sin(deg2rad(2 * $m))- 0.6280 * sin(deg2rad($mprime))+ 0.0089 * sin(deg2rad(2 * $mprime))- 0.0004 * sin(deg2rad(3 * $mprime))+ 0.0079 * sin(deg2rad(2 * $f))- 0.0119 * sin(deg2rad($m + $mprime))- 0.0047 * sin(deg2rad($m - $mprime))+ 0.0003 * sin(deg2rad(2 * $f + $m))- 0.0004 * sin(deg2rad(2 * $f - $m))- 0.0006 * sin(deg2rad(2 * $f + $mprime))+ 0.0021 * sin(deg2rad(2 * $f - $mprime))+ 0.0003 * sin(deg2rad($m + 2 * $mprime))+ 0.0004 * sin(deg2rad($m - 2 * $mprime))- 0.0003 * sin(deg2rad(2 * $m + $mprime));if($phase<0.5)$pt+=0.0028 - 0.0004 * cos(deg2rad($m))+ 0.0003 * cos(deg2rad($mprime));else $pt+=-0.0028 + 0.0004 * cos(deg2rad($m))- 0.0003 * cos(deg2rad($mprime));$apcor=true;}if(!$apcor)return false;return $pt;} private function phasehunt(){$sdate=$this->utctojulian($this->timestamp);$adate=$sdate - 45;$ats=$this->timestamp - 86400 * 45;$yy=(int)gmdate('Y',$ats);$mm=(int)gmdate('n',$ats);$k1=floor(($yy +(($mm - 1)*(1 / 12))- 1900)* 12.3685);$adate=$nt1=$this->meanphase($adate,$k1);while(true){$adate+=$this->synmonth;$k2=$k1 + 1;$nt2=$this->meanphase($adate,$k2);if(abs($nt2 - $sdate)<0.75)$nt2=$this->truephase($k2,0.0);if($nt1<=$sdate&&$nt2>$sdate)break;$nt1=$nt2;$k1=$k2;}$data=array($this->truephase($k1,0.0),$this->truephase($k1,0.25),$this->truephase($k1,0.5),$this->truephase($k1,0.75),$this->truephase($k2,0.0),$this->truephase($k2,0.25),$this->truephase($k2,0.5),$this->truephase($k2,0.75));$this->quarters=array();foreach($data as $v)$this->quarters[]=($v - 2440587.5)* 86400;}private function utctojulian($ts){return $ts / 86400 + 2440587.5;} private function get_phase($n){if(is_null($this->quarters))$this->phasehunt();return $this->quarters[$n];} function phase(){return $this->phase;}function illumination(){return $this->illum;} function age(){return $this->age;} function distance(){return $this->dist;} function diameter(){return $this->angdia;} function sundistance(){return $this->sundist;} function sundiameter(){return $this->sunangdia;} function new_moon(){return $this->get_phase(0);} function first_quarter(){return $this->get_phase(1);} function full_moon(){return $this->get_phase(2);} function last_quarter(){return $this->get_phase(3);} function next_new_moon(){return $this->get_phase(4);} function next_first_quarter(){return $this->get_phase(5);} function next_full_moon(){return $this->get_phase(6);} function next_last_quarter(){return $this->get_phase(7);} function phase_name(){$names=array('New Moon','Waxing Crescent','First Quarter','Waxing Gibbous','Full Moon','Waning Gibbous','Third Quarter','Waning Crescent','New Moon');return $names[ floor(($this->phase + 0.0625)* 8)];} } ?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('MoonPhaseInformation', 'Moon Phase Information'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
@font-face{font-family:weathertext;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype")}
html,body{background-color:var(--pop-bg);margin:0;padding:0;font-size:13px;font-family:"weathertext",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:var(--pop-ink);}
[data-theme="light"] html,[data-theme="light"] body{color:#000;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));grid-gap:5px;align-items:stretch;color:var(--pop-ink);padding:4px 44px 4px 4px;box-sizing:border-box}
[data-theme="light"] .grid{color:#000;}
.grid>article{border:1px solid rgba(245,247,252,.02);box-shadow:2px 2px 6px 0 rgba(0,0,0,.6);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;background:hsla(233,12%,13%,.5);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:var(--pop-ink)}
[data-theme="light"] .grid>article{border:1px solid rgba(0,0,0,.08);box-shadow:2px 2px 6px 0 rgba(0,0,0,.1);background:#fff;color:#000;}
.grid>article img{max-width:100%}
.weather34darkbrowser{position:relative;background:0;width:97%;height:30px;margin:auto;margin-top:-5px;margin-left:0;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px;margin-right:44px}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-link);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 10px 0 auto;font-family:arial;height:20px}
blue{color:var(--pop-blue)}orange{color:var(--pop-cyan)}orange1{position:relative;color:var(--pop-cyan);margin:0 auto;text-align:center;margin-left:5%;font-size:1.1rem}green{color:var(--pop-green-dim)}red{color:var(--pop-red)}red6{color:var(--pop-red-dark)}value{color:var(--pop-value)}yellow{color:var(--pop-yellow-dim)}purple{color:var(--pop-purple)}meteotextshowertext{font-size:1.2rem;color:var(--pop-cyan)}meteorsvgicon{color:var(--pop-ink)}
.moonphasesvg{align-items:right;justify-content:center;display:flex;max-height:120px}
.moonhead{margin-bottom:12px}
.moonphasetext{font-size:.8rem;color:var(--pop-ink);position:absolute;display:inline;left:125px;top:100px}
[data-theme="light"] .moonphasetext{color:#000;}
moonphaseriseset{font-size:.75rem}
credit{position:relative;font-size:.7em;top:10%}
.actualt{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:teal;padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:#fff;align-items:center;justify-content:center;margin-bottom:10px;top:0}
.actualw{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74,99,111,.1);padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:#aaa;align-items:center;justify-content:center;margin-bottom:10px;top:0}
[data-theme="light"] .actualw{color:#555;}
a{color:#aaa;text-decoration:none}
a:hover{color:#fff}
[data-theme="light"] a{color:#007a8c;}
[data-theme="light"] a:hover{color:#005a66;}
.moonage-val{color:#ff8841;}
[data-theme="light"] .moonage-val{color:#b05000;}
  </style>
</head>
<body>
<div class="weather34darkbrowser" url="<?php echo htmlspecialchars(w34t('MoonPhaseInformation', 'Moon Phase Information')); ?>"></div>
<main class="grid">
  <article>       
<div class="moonhead"><?php echo $info;?> <?php echo w34t('CurrentMoonPhase', 'Current Moon Phase'); ?></div>
<div id="weather34moonphases" class="moonphasesvg"></div>
<svg id="weather34 simple moonphase"><circle cx="50" cy="50" r="49.5" fill="rgba(86, 95, 103, .4)"/><path id="weather34shape" fill="rgba(230, 232, 239, .5)"/></svg>
<script> //simple moonphase for weather34
weather34Moon();function weather34Moon() {var day = Date.now() / 86400000;var referenceweather34Moon = Date.UTC(2018, 0, 17, 2, 17, 0, 0);
var refweather34Day = referenceweather34Moon / 86400000;var phase = (day - refweather34Day) %29.530588853*1.008;var s=String;
switch (Math.round(phase / 3.75)){}document.getElementById("weather34moonphases");
var weather34moonCurve;var lf=Math.min(3-4*(phase/30),1);var lc=Math.abs(lf*50);	var lb=(lf<0) ? "0" : "1";
var rf=Math.min(3+4*((phase-30)/30),1);	var rc=Math.abs(rf*50);	var rb=(rf<0) ? "0" : "1";weather34moonCurve="M 50,0 "+ "a "+s(lc)+",50 0 0 "+lb+" 0,100 "+ "a "+s(rc)+",50 0 0 "+rb+" 0,-100";
document.getElementById("weather34shape").setAttribute("d",weather34moonCurve);}</script> 
   <div class=moonphasetext>    
<?php echo " ";{$day = w34_date('l jS F Y');if($day===w34_date("l jS F Y",strtotime('2019-7-2'))){echo 'Solar <orange>Eclipse</orange>';}else if($day===w34_date("l jS F Y",strtotime('2019-7-16'))){echo 'Lunar <orange>Eclipse</orange>';}else if($day===w34_date("l jS F Y",strtotime('2019-7-17'))){echo 'Lunar <orange>Eclipse</orange>';}else if($day===w34_date("l jS F Y",strtotime('2019-12-26'))){echo 'Solar <orange>Eclipse</orange>';}		
// weather34 moonphase no scraping its calculated from the livedata !
	else echo "<aqivalue1>".w34_moonphase($weather['moonphase'])." </aqivalue1>";}?>             
<br>
<?php echo " " . w34t('Luminance', 'Luminance') . " <orange>" .$weather['luminance']."</orange>%";?>            
</div>    


  
</article>  
  
  <article>
 <moonphaseriseset>
  <?php echo $info;?> <?php echo w34t('MoonRiseSetInfo', 'Moon Rise/Set Information'); ?><br><br>
<svg id="i-ban" viewBox="0 0 32 32" width="10" height="10" fill="#39739f" stroke="#39739f" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('Moonrise', 'Moon Rise'); ?>:
<?php
$mr_clean = trim(strip_tags($weather['moonrise']));
if (preg_match('/(\d{1,2}:\d{2}(?:\s*[ap]m)?)$/i', $mr_clean, $m)) {
    echo " " . $m[1] . " ";
} else {
    echo " " . w34_words($weather['moonrise'], array('Rise' => 'MoonRiseShort', 'In Transit' => 'InTransit')) . " ";
}
?>
<svg id="i-chevron-top" viewBox="0 0 32 32" width="10" height="10" fill="none" stroke="#ff8841" stroke-linecap="round" stroke-linejoin="round" stroke-width="10%"><path d="M30 20 L16 8 2 20" /></svg>
</span><br>
<svg id="i-ban" viewBox="0 0 32 32" width="10" height="10" fill="#D46842" stroke="#D46842" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%"><circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('Moonset', 'Moon Set'); ?>: 
<?php
$ms_clean = trim(strip_tags($weather['moonset']));
if (preg_match('/(\d{1,2}:\d{2}(?:\s*[ap]m)?)$/i', $ms_clean, $m)) {
    echo " " . $m[1] . " ";
} else {
    echo " " . w34_words($weather['moonset'], array('Set' => 'MoonSetShort', 'In Transit' => 'InTransit')) . " ";
}
?>
<svg id="i-chevron-bottom" viewBox="0 0 32 32" width="10" height="10" fill="none" stroke="#ff8841" stroke-linecap="round" stroke-linejoin="round" stroke-width="10%"><path d="M30 12 L16 24 2 12" /></svg>
</span><br>
<?php  // full/new moon for homeweather station  // ?>

<svg id="i-ban" viewBox="0 0 32 32" width="10" height="10" fill="rgba(255, 136, 65, 1.00)" stroke="rgba(255, 136, 65, 1.00)" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
<?php echo w34t('Nextfullmoon', 'Next Full Moon'); ?>: <?php  // homeweatherstation fullmoon info output 18th Aug
$now1 =time();$moon1 = new MoonPhase();
echo "";
if ($now1 < $moon1->full_moon()) 
{echo w34_date('D jS-M-Y', $moon1->full_moon() );}
else echo w34_date('D jS-M-Y', $moon1->next_full_moon() );
?></span><br>
<svg id="i-ban" viewBox="0 0 32 32" width="10" height="10" fill="#777" stroke="#777" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /> <path d="M6 6 L26 26" /></svg>
<?php echo w34t('Nextnewmoon', 'Next New Moon'); ?>: <?php $moon=new MoonPhase();$nextnewmoon=w34_date('D jS-M-Y',$moon->next_new_moon());echo $nextnewmoon;?>
</span><br /><svg id="i-ban" viewBox="0 0 32 32" width="10" height="10" fill="rgba(154, 186, 47, 1.00)" stroke="rgba(154, 186, 47, 1.00)" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> 
<?php // homeweatherstation create an instance of the age of moon
$moon = new MoonPhase();$moonage =round($moon->age(),2);echo w34f('MoonCycleAge', 'Current moon cycle is %s days old', "<span class='moonage-val'>" . round($moonage,0) . "</span>");?>
               
  
  
   </article>  
  
  <article>
   <?php echo $info;?> <?php echo w34t('MoonFacts', 'Moon Facts:'); ?> <orange><?php echo w34t('DidYouKnow', 'Did you know?'); ?></orange><br><br>
            <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('MoonFact1', 'The Moon formed approximately 4.5 billion years ago.'); ?><br>
  <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('MoonFact2', 'High and low tides are caused by the Moon’s gravitational pull.'); ?><br>
  <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('MoonFact3', 'The Moon orbits the Earth approximately every 27.3 days.'); ?> <br>
  <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('MoonFact4', 'Where sunlight hits the Moon’s surface, temperatures can reach 260&deg;F (127&deg;C).'); ?><br>
<svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg> <?php echo w34t('MoonFact5', 'On the dark side of the Moon, temperatures can drop to -280&deg;F (-173&deg;C).'); ?><br>

  </article> 
  
<article>
   <?php echo $info ;?> <?php echo w34t('MoonPhotoGuide', 'Moon Photography Guide'); ?><br><br> <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto1', 'Use a tripod'); ?>
 <br>
 <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto2', 'Use a zoom lens'); ?>
 
 <br>
 <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto3', 'Measure the exposure'); ?> 
 <br>
 <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto4', 'Avoid ambient nearby lighting'); ?> 
 <br>
 <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto5', 'Use a remote shutter release'); ?> 
 <br>
 <svg id="i-ban" viewBox="0 0 32 32" width="8" height="8" fill="#3b9cac" stroke="#3b9cac" stroke-linecap="round" stroke-linejoin="round" stroke-width="6.25%">
<circle cx="16" cy="16" r="14" /><path d="M6 6 L26 26" /></svg>
 <?php echo w34t('MoonPhoto6', 'Always shoot in RAW for post-processing'); ?>              
  </article>    
  <article>
   <?php echo $info ;?> <?php echo w34t('RadioHamGuide', 'Radio Ham Guide'); ?> (<orange>EME</orange>)<br><br>
   <?php echo w34t('MoonEmeGuide', 'Earth–Moon–Earth communication (<orange>EME</orange>), also known as Moon bounce, is a radio communications technique that relies on the propagation of radio waves from an Earth-based transmitter directed via reflection from the surface of the Moon back to an Earth-based receiver using VHF and UHF amateur radio bands.'); ?>
  </article> 
  <article>
   <div class="actualt">&nbsp;&nbsp; &copy; <?php echo w34t('Information', 'Information'); ?></div>  
  <?php echo $info?> <?php echo w34f('CreditWeather34', 'CSS/SVG/PHP scripts were developed by %s for use in the weather34 template', '<a href="https://weather34.com" title="weather34.com" target="_blank" style="font-size:9px;">weather34.com</a>'); ?> &copy; 2015-<?php echo date('Y');?></article> 
</main>
</body>
</html>
