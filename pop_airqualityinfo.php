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
include_once('w34CombinedData.php');include('common.php'); $weather["cloudbase3"] = round((anyToC($weather["temp"]) - anyToC($weather["dewpoint"])) * 1000 /2.4444) ;
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
$locationinfo='<svg id="i-location2" viewBox="0 0 32 32" width="15px" height="15px" fill="none" stroke="rgba(255, 124, 57, 1.000)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
<circle cx="16" cy="11" r="4" /><path d="M24 15 C21 22 16 30 16 30 16 30 11 22 8 15 5 8 10 2 16 2 22 2 27 8 24 15 Z" /></svg>';
$alert="<svg id='firealert' viewBox='0 0 32 32' width='18px' height='18px' fill='none' stroke='rgba(255, 124, 57, 1.000)' stroke-linecap='round' stroke-linejoin='round' stroke-width='2'>
<path d='M16 3 L30 29 2 29 Z M16 11 L16 19 M16 23 L16 25' /></svg>";
//weather AIR QUALITY / CLOUDBASE MODULE APRIL 2018
?>
<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title>Weather34 Air Quality</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype")}
body,html{background-color:var(--pop-bg);font-size:13px;font-family:weathertext2,Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:var(--pop-ink);margin:0;padding:0;}
[data-theme="light"] body,[data-theme="light"] html{color:var(--pop-value);}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,2fr));grid-gap:5px;align-items:stretch;color:var(--pop-ink);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;padding:4px 44px 4px 4px;box-sizing:border-box;}
[data-theme="light"] .grid{color:var(--pop-value);}
.grid>article{border:1px solid rgba(245,247,252,0.02);box-shadow:2px 2px 6px 0 rgba(0,0,0,.3);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;height:160px;background:hsla(233,12%,13%,.5);}
[data-theme="light"] .grid>article{border:1px solid rgba(0,0,0,0.08);box-shadow:2px 2px 6px 0 rgba(0,0,0,.1);background:#fff;}
.weather34darkbrowser{position:relative;background:0;width:97%;height:30px;margin:auto;margin-top:-5px;margin-left:0;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px;margin-right:44px;}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-link);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 44px 0 auto;font-family:arial;height:20px;}
blue{color:var(--pop-blue)}orange{color:var(--pop-cyan)}orange1{position:relative;color:var(--pop-cyan);margin:0 auto;text-align:center;margin-left:5%;font-size:1.1rem}green{color:var(--pop-green-dim)}red{color:var(--pop-red)}red6{color:var(--pop-red-dark)}value{color:var(--pop-value)}yellow{color:var(--pop-yellow-dim)}purple{color:var(--pop-purple)}meteotextshowertext{font-size:1.2rem;color:var(--pop-cyan)}meteorsvgicon{color:var(--pop-ink)}
.weather34aqi{color:#ff8841;position:absolute;margin-left:36px;margin-top:17px;font-size:12px;width:20px;font-family:-apple-system,BlinkMacSystemFont,weathertext2,Roboto,Helvetica,Arial,sans-serif;max-height:100px;line-height:10px;font-weight:400}
.weather34aqi span{color:#777;font-family:weathertext2,arial,sans-serif;font-size:12px}
maroon{color:var(--pop-maroon)}orange{color:var(--pop-orange)}yellow{color:var(--pop-yellow)}green{color:var(--pop-green)}grey{color:var(--pop-green-dim)}
aqiimage1{position:absolute;left:-5px;top:-2px}.extraqiinfo{position:relative;top:0;font-size:.9em;color:#aaa;left:120px;width:100px}
[data-theme="light"] .extraqiinfo{color:#000;}
.extraqiinfo2{position:relative;top:0;font-size:12px;color:#aaa;left:0;width:100px}
airvalue{position:relative;top:5px;left:-10px;font-size:24px}airvalue0{position:relative;top:5px;left:-10px;font-size:24px}
span11{position:relative;top:50px;font-size:14px;line-height:14px;width:200px;margin-left:20px}
smalluvunit{position:absolute;display:inline;font-size:.8rem;font-family:Arial,Helvetica,system;vertical-align:top}
.air0,.air100,.air150,.air200,.air300,.air50{position:relative;font-family:weathertext2,Arial,Helvetica,system;width:8rem;height:5.68rem;font-size:1.5rem;padding-top:0;color:#fff;border-bottom:15px solid rgba(56,56,60,1);display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px}
.air0{background:rgba(144,177,42,1)}.air50{background:rgba(230,161,65,1)}.air100{background:rgba(255,124,57,.8)}.air150{background:rgba(211,93,78,.8)}.air200{background:#a475cb}.air250{background:#a475cb}.air300{background:#a475cb}
pm25{position:relative;font-family:weathertext2,Arial,Helvetica,system;font-size:.5rem;vertical-align:super}
.actualt{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:teal;padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:var(--pop-badge-text);border-bottom:2px solid rgba(56,56,60,1);align-items:center;justify-content:center;margin-bottom:10px;top:0}
.actualw{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74,99,111,.1);padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:#aaa;border-bottom:2px solid rgba(56,56,60,1);align-items:center;justify-content:center;margin-bottom:10px;top:0}
aqred{color:#d35d4e}aqpurple{color:rgba(151,88,190,1)}aqorange{color:#d05f2d}aqyellow{color:#e6a141}aqgreen{color:#90b12a}
div.output small1{display:block;margin:0;padding:0;font-size:.9em;color:rgba(255,255,255,.8);font-weight:500}
.indoorsvgnest{position:relative;margin:0 auto;display:flex;align-items:center;justify-content:center;margin-top:-10px}
.indoorsvgnestvalue{margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:1.85em;font-family:weathertext2;position:absolute}
.indoorsvgnestvalue1{margin:0 auto;align-items:left;justify-content:left;font-size:1.2em;font-family:weathertext2;position:absolute}
.weather34indoortrendup{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#d35d4e;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoortrenddown{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoortrendsteady{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#90b12a;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;margin-left:65px;top:128px}
.weather34indoorcomfort{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#90b12a;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoorhumid{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:.89rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoordry{font-family:weathertext2,Arial,Helvetica,system;width:4rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#d35d4e;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;margin-left:65px;top:128px}
.weather34indoorwarm{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#ff832f;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoorcold{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
.weather34indoormild{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:rgba(230,161,65,1);border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;margin-left:65px;top:128px}
.weather34indoorhot{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.8rem;padding-top:0;color:#fff;background:rgba(211,93,78,1);border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:2px;-moz-border-radius:2px;-o-border-radius:2px;border-radius:2px;position:absolute;margin-left:65px;top:128px}
.weather34indoorpm{font-family:weathertext2,Arial,Helvetica,system;width:3.2rem;height:1rem;font-size:.65rem;padding-top:0;color:#fff;background:#00a4b4;border:0;display:flex;align-items:center;justify-content:center;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;position:absolute;margin-left:65px;top:128px}
div.output{width:90px;height:90px;position:absolute;left:70px;top:88px;color:var(--pop-badge-text);text-transform:uppercase;text-align:center;margin:0 auto;display:flex;align-items:center;justify-content:center}
div.output small{display:block;margin:0;padding:0;font-size:1em;color:var(--pop-link);margin-left:10px;margin-top:-5px}
.trendicon{margin:0 auto;display:flex;margin-top:37px;align-items:center;justify-content:center;font-size:1.85em;font-family:weathertext2;position:absolute}
  </style>

<?php  // PURPLE AIR additional conversion script included by Andrew Billits 24 April 2018
function pm25_to_aqi($pm25){
	if ($pm25 > 500.5) {
	  $aqi = 500;
	} else if ($pm25 > 350.5 && $pm25 <= 500.5 ) {
	  $aqi = map($pm25, 350.5, 500.5, 400, 500);
	} else if ($pm25 > 250.5 && $pm25 <= 350.5 ) {
	  $aqi = map($pm25, 250.5, 350.5, 300, 400);
	} else if ($pm25 > 150.5 && $pm25 <= 250.5 ) {
	  $aqi = map($pm25, 150.5, 250.5, 200, 300);
	} else if ($pm25 > 55.5 && $pm25 <= 150.5 ) {
	  $aqi = map($pm25, 55.5, 150.5, 150, 200);
	} else if ($pm25 > 35.5 && $pm25 <= 55.5 ) {
	  $aqi = map($pm25, 35.5, 55.5, 100, 150);
	} else if ($pm25 > 12 && $pm25 <= 35.5 ) {
	  $aqi = map($pm25, 12, 35.5, 50, 100);
	} else if ($pm25 > 0 && $pm25 <= 12 ) {
	  $aqi = map($pm25, 0, 12, 0, 50);
	}
	return $aqi;
}
function map($value, $fromLow, $fromHigh, $toLow, $toHigh){
    $fromRange = $fromHigh - $fromLow;
    $toRange = $toHigh - $toLow;
    $scaleFactor = $toRange / $fromRange;

    // Re-zero the value within the from range
    $tmpValue = $value - $fromLow;
    // Rescale the value to the to range
    $tmpValue *= $scaleFactor;
    // Re-zero back to the to range
    return $tmpValue + $toLow;
}
$json_string             = file_get_contents("jsondata/pu.txt");
$parsed_json             = json_decode($json_string);
$hasPurple               = isset($parsed_json->results[0]->PM2_5Value); // no PurpleAir feed -> no card (was a fake 0.0 Good)
//$aqiweather["aqi"]       = $parsed_json->{'results'}[1]->{'PM2_5Value'};
$aqiweather["aqi"]       = number_format(pm25_to_aqi(($parsed_json->{'results'}[0]->{'PM2_5Value'} + $parsed_json->{'results'}[1]->{'PM2_5Value'}) / 2),1);
$aqiweather["aqiozone"]  = 'N/A';
$aqiweather["time2"]     = $parsed_json->{'results'}[0]->{'LastSeen'};
$aqiweather["time"]      = date("H:i",$aqiweather["time2"]);
$aqiweather["city"]      = $parsed_json->{'results'}[0]->{'ID'};
$aqiweather["label"]     = $parsed_json->{'results'}[0]->{'Label'};
$a="";if($aqiweather["aqi"]==$a){$aqiweather["aqi"] = "0" ;}


$heatindexwu='<svg "weather34 heatindex icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
?>
<?php 



$airqualitybreeze='<svg "weather34 aq icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';


$humiditybreeze='<svg "weather34 aq icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';

$feelslikewu='<svg "weather34feels wu icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#d05f2d" stroke="#d05f2d" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#ff832f" stroke="#ff832f" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#e6a141" stroke="#e6a141" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#90b12a" stroke="#90b12a" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';
$windchillwu='<svg "weather34 windchill wu icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path fill="#009bab" stroke="#009bab" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg>';




?>

</head>
<body>

<div class="weather34darkbrowser" url="Air Quality"></div>
  
<main class="grid">
<?php if ($hasPurple): ?>
<article>
<div class=actualt>Purple Air Quality </div> 
  <div class=indoorsvgnest> 
 <div class="output">
           <small><?php 
if($aqiweather["aqi"] >300)echo "Hazardous";
else if($aqiweather["aqi"] >200)echo "Very Unhealthy";
else if($aqiweather["aqi"] >150)echo "Unhealthy";
else if($aqiweather["aqi"] >100)echo "Unhealthy<br>For Some";
else if($aqiweather["aqi"] >50)echo "Moderate ";
else if($aqiweather["aqi"] >=0)echo "Good ";
?> </small>           
        </div>      
<svg id="indoor air quality weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>    
<div class=indoorsvgnestvalue> <?php echo $aqiweather["aqi"];?></div>
<div class="trendicon"><svg "weather34 aq icon" width="10px" height="10px" viewBox="0 0 250 250" version="3.4.2019" >
<path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke="#90b12a" stroke-width="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" opacity="1.00" d=" M 94.24 33.23 C 106.10 23.71 121.88 18.18 137.10 20.91 C 150.77 23.09 162.89 30.83 172.86 40.14 C 185.30 51.88 198.09 63.70 213.46 71.52 C 222.73 76.23 233.65 79.13 243.92 76.10 C 245.97 75.67 248.52 74.29 250.00 76.59 L 250.00 77.41 C 243.65 83.99 235.05 88.09 226.37 90.62 C 211.92 94.57 196.20 91.32 183.71 83.32 C 173.86 77.45 166.16 68.84 157.60 61.36 C 148.96 53.84 139.88 46.65 129.66 41.38 C 120.64 36.87 110.14 33.45 100.05 36.12 C 97.82 36.48 95.53 37.70 93.28 36.81 C 92.94 35.46 92.75 33.94 94.24 33.23 Z" /><path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke="#90b12a" stroke-width="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke-width="0.09375" opacity="1.00" d=" M 40.96 70.24 C 55.35 59.16 73.48 51.97 91.86 53.82 C 112.91 55.25 131.31 67.49 146.46 81.37 C 160.43 94.60 174.85 107.67 191.64 117.27 C 205.48 125.28 221.84 130.62 237.92 127.34 C 240.94 127.12 244.23 125.18 247.05 127.08 C 247.03 127.74 246.99 129.04 246.97 129.70 C 232.18 142.37 212.89 151.02 193.10 149.31 C 176.62 148.37 161.34 140.68 148.46 130.76 C 141.24 125.29 134.74 118.98 128.15 112.80 C 113.51 99.62 97.87 86.88 79.42 79.47 C 68.23 74.90 55.71 73.38 43.88 76.15 C 41.67 76.37 38.79 77.88 37.14 75.68 C 36.36 73.01 39.31 71.67 40.96 70.24 Z" />
<path <path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke="#90b12a" stroke-width="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke-width="0.09375" opacity="1.00" d=" M 3.50 118.53 C 15.56 108.44 30.33 100.94 46.17 99.59 C 64.81 97.72 83.26 104.85 98.11 115.75 C 106.84 121.93 114.50 129.44 122.30 136.71 C 134.76 147.91 147.93 158.61 162.99 166.12 C 173.19 171.17 184.49 174.68 195.98 174.06 C 201.25 173.92 206.34 172.21 211.61 171.97 C 212.00 172.54 212.79 173.68 213.18 174.25 C 211.39 177.16 208.29 178.78 205.59 180.71 C 195.79 187.36 184.84 192.67 173.06 194.55 C 161.43 196.13 149.37 194.93 138.42 190.57 C 122.59 184.47 109.28 173.47 97.23 161.77 C 84.46 149.94 71.03 138.53 55.65 130.21 C 44.46 124.18 31.89 119.80 19.01 120.66 C 14.33 120.76 9.80 122.01 5.19 122.66 C 3.05 123.04 1.43 119.77 3.50 118.53 Z" /><path <path fill="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke="#90b12a" stroke-width="<?php 
if($aqiweather["aqi"]>300)echo "#99020d";
else if($aqiweather["aqi"]>200)echo "#a968b3";
else if($aqiweather["aqi"]>150)echo "#d35d4e";
else if($aqiweather["aqi"]>100)echo "#d05f2d";
else if ($aqiweather["aqi"]>50) echo "#e6a141";
else if ($aqiweather["aqi"]>=0) echo "#90b12a";
?>" stroke-width="0.09375" opacity="1.00" d=" M 26.44 157.60 C 40.14 154.39 54.65 158.24 66.35 165.64 C 77.14 172.14 85.34 181.89 94.99 189.83 C 106.04 199.15 118.02 208.17 132.13 212.13 C 139.32 214.27 147.04 213.87 154.14 211.59 C 156.28 210.49 158.15 213.79 156.18 215.09 C 147.11 222.60 135.83 227.63 124.02 228.51 C 106.54 229.76 90.07 220.91 77.61 209.29 C 63.90 196.42 49.84 183.15 32.41 175.48 C 23.21 171.33 12.55 170.13 2.91 173.51 C 1.29 174.56 -1.18 171.20 0.84 170.21 C 8.51 164.54 17.09 159.79 26.44 157.60 Z" /></svg></div>
</div>
</div></div>
</article>
<?php endif; ?>
  <article>
  <div class=actualt>&nbsp;&nbsp Particle Info</div>
  <div class=indoorsvgnest> 
<svg id="indoor air quality weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="#44a6b5" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>    
<div class=indoorsvgnestvalue> <pm25>PM</pm25>2.5</div>
<div class="trendicon"><?php echo $windchillwu;?></div></div>
<div class="extraqiinfo">
<?php $st_str = trim((!empty($aqiweather["city"]) ? $aqiweather["city"] : "") . " " . (!empty($aqiweather["state"]) ? $aqiweather["state"] : "")); if ($st_str !== "") echo "Station ID: " . htmlspecialchars($st_str) . "<br>"; ?>
<?php echo "Updated:<blue> " .$aqiweather["time"] ?></blue>
</div>


</div></div>
  </article>
   <article>
   <div class=actualt>&nbsp;&nbsp Guide</div>   
   <li><green>0-50 GOOD</green></li>
           <li><yellow>50+ MODERATE</yellow></li>
           <li><orange>100+ Unhealthy for Sensitive Groups</orange></li>
           <li><red>150+ Unhealthy </red>(Precautions Required)</li>
           <li><purple>200+ Very Unhealthy</purple> (Critical Conditions)</li>
           <li><maroon>300+ Hazardous</maroon> (Life Threatening)</li>
   
  </article> 
  
  
   <article>
  
  
<div class=actualt>Cloudbase Height </div> 



<div class=indoorsvgnest> 
 <div class="output"><small> Estimated </small> 
 
 
 
      
        </div>

  
<svg id="indoor air quality weather34" width="120px" height="120px" viewBox="0 0 600 600" version="1.1" >
<path fill="#4b545c" opacity="1.00" d=" M 277.33 44.31 C 278.81 43.77 280.41 44.04 281.96 43.97 C 292.95 44.05 303.94 43.96 314.93 44.00 C 316.43 43.98 317.71 44.94 319.14 45.28 C 370.84 48.81 421.06 69.16 461.23 101.80 C 484.64 121.11 504.91 144.32 520.29 170.51 C 529.82 187.64 538.71 205.33 544.10 224.25 C 549.63 241.34 553.03 259.10 554.58 276.97 C 554.34 279.46 556.30 281.50 556.02 284.00 C 555.96 294.97 556.03 305.95 556.00 316.92 C 556.01 318.75 554.62 320.26 554.65 322.09 C 553.77 334.85 551.54 347.48 548.62 359.93 C 544.55 375.59 539.76 391.16 532.66 405.74 C 529.53 413.86 524.89 421.23 520.80 428.87 C 497.75 467.88 464.45 500.92 424.85 523.03 C 393.09 540.82 357.49 551.88 321.18 554.73 C 319.46 555.22 317.85 556.22 316.00 556.02 C 305.03 555.96 294.05 556.03 283.08 556.00 C 281.24 556.01 279.73 554.61 277.90 554.63 C 220.65 549.93 165.37 524.80 123.95 485.05 C 99.37 461.65 79.41 433.39 65.94 402.23 C 56.79 382.45 51.18 361.22 47.48 339.80 C 46.82 333.22 45.44 326.71 45.37 320.08 C 45.43 318.24 43.96 316.76 44.00 314.93 C 43.99 302.98 44.00 291.03 44.00 279.08 C 46.47 268.72 46.73 257.91 49.38 247.55 C 61.60 188.21 95.92 133.80 144.07 97.05 C 178.88 70.12 220.79 52.43 264.41 46.46 C 268.71 45.70 273.16 45.68 277.33 44.31 M 276.43 80.50 C 242.35 84.05 209.20 95.83 180.44 114.44 C 132.01 145.51 96.52 196.15 84.41 252.45 C 78.65 278.28 77.90 305.16 81.43 331.36 C 86.10 364.11 98.21 395.82 116.76 423.22 C 129.29 441.93 144.79 458.60 162.32 472.70 C 198.21 501.45 243.29 518.62 289.27 520.58 C 332.32 522.81 375.94 512.08 412.93 489.90 C 444.45 471.19 471.17 444.45 489.90 412.94 C 510.51 378.44 521.45 338.21 520.78 298.01 C 520.70 252.89 505.95 207.98 479.55 171.43 C 448.08 127.28 399.81 95.37 346.72 84.21 C 323.71 79.09 299.85 78.21 276.43 80.50 Z" />
<path fill="#1f1f1f" opacity="1.00" d=" M 276.43 80.50 C 299.85 78.21 323.71 79.09 346.72 84.21 C 399.81 95.37 448.08 127.28 479.55 171.43 C 505.95 207.98 520.70 252.89 520.78 298.01 C 521.45 338.21 510.51 378.44 489.90 412.94 C 471.17 444.45 444.45 471.19 412.93 489.90 C 375.94 512.08 332.32 522.81 289.27 520.58 C 243.29 518.62 198.21 501.45 162.32 472.70 C 144.79 458.60 129.29 441.93 116.76 423.22 C 98.21 395.82 86.10 364.11 81.43 331.36 C 77.90 305.16 78.65 278.28 84.41 252.45 C 96.52 196.15 132.01 145.51 180.44 114.44 C 209.20 95.83 242.35 84.05 276.43 80.50 M 286.41 150.46 C 242.51 154.25 200.72 178.50 176.17 215.17 C 162.81 234.07 154.66 256.34 151.24 279.17 C 147.21 309.14 152.07 340.35 165.68 367.40 C 176.33 388.48 191.81 407.16 210.97 421.04 C 249.80 450.25 303.35 458.22 349.12 442.07 C 379.21 431.96 405.62 411.69 423.60 385.58 C 448.45 350.20 456.72 303.60 445.26 261.87 C 442.59 251.03 438.74 240.45 433.33 230.67 C 425.90 216.47 416.41 203.29 404.75 192.24 C 373.92 161.88 329.44 146.38 286.41 150.46 Z" />
<path fill="
<?php 
if ($weather["cloudbase3"]<1500){echo "#44a6b5";}
else if ($weather["cloudbase3"]>=4000){echo "#d05f2d";}
else if ($weather["cloudbase3"]>=1500){echo "#e6a141";}

?>


" opacity="1.00" d=" M 286.41 150.46 C 329.44 146.38 373.92 161.88 404.75 192.24 C 416.41 203.29 425.90 216.47 433.33 230.67 C 438.74 240.45 442.59 251.03 445.26 261.87 C 456.72 303.60 448.45 350.20 423.60 385.58 C 405.62 411.69 379.21 431.96 349.12 442.07 C 303.35 458.22 249.80 450.25 210.97 421.04 C 191.81 407.16 176.33 388.48 165.68 367.40 C 152.07 340.35 147.21 309.14 151.24 279.17 C 154.66 256.34 162.81 234.07 176.17 215.17 C 200.72 178.50 242.51 154.25 286.41 150.46 Z" />
</svg>    
<div class="indoorsvgnestvalue1"> 
<?php if ($weather["cloudbase3"]>0){echo $weather["cloudbase3"]."<smalluvunit>ft</smalluvunit><br>".round($weather["cloudbase3"]*0.3048,0)."<smalluvunit>m</smalluvunit>";}?>
</div>
<div class="trendicon"><?php echo $humiditybreeze;?></div></div>
</div>


  </article> 
  
  
  
  <article>
   <div class=actualt> &copy; Information</div> 
   Air quality guideline is an annual mean concentration guideline for particulate matter from the World Health Organization. The guideline stipulates that PM2.5 not exceed 10 μg/m3 annual mean, or 25 μg/m3 24-hour mean; and that PM10 not exceed 20 μg/m3 annual mean, or 50 μg/m3 24-hour mean.
   
  </article>   
   

</main>
</body>
</html>
