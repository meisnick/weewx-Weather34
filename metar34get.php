<?php //original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
include('settings.php');include_once('w34CombinedData.php');error_reporting(0); 
$result = date_sun_info(time(), $lat, $lon);
$sunr=date_sunrise(time(), SUNFUNCS_RET_STRING, $lat, $lon, $rise_zenith, $UTC_offset);
$suns=date_sunset(time(), SUNFUNCS_RET_STRING, $lat, $lon, $set_zenith, $UTC_offset);
$suns2 =date('G.i', $result['sunset']);
$sunrs2 =date('G.i', $result['sunrise']);
$now =date('G.i');
 //weather34 wxcheck API aviation metar script May 2018 
$json_string             = file_get_contents("jsondata/me.txt");
$parsed_json             = json_decode($json_string);
$metar34time       = $parsed_json->{'data'}[0]->{'observed'};
$metar34raw       = $parsed_json->{'data'}[0]->{'raw_text'};
$metar34stationid       = $parsed_json->{'data'}[0]->{'icao'};
$metar34stationname       = $parsed_json->{'data'}[0]->{'station'}->{'name'};
$metar34fltcat       = $parsed_json->{'data'}[0]->{'flight_category'} ?? '';

$metar34lat = $parsed_json->{'data'}[0]->{'station'}->{'geometry'}->{'coordinates'}[1];
$metat34lon = $parsed_json->{'data'}[0]->{'station'}->{'geometry'}->{'coordinates'}[0];
$airport1dist = round(distance($lat, $lon, $metar34lat, $metat34lon));
$metar34pressurehg       = $parsed_json->{'data'}[0]->{'barometer'}->{'hg'};	
$metar34pressuremb       = $parsed_json->{'data'}[0]->{'barometer'}->{'mb'};
$metar34conditions         = $parsed_json->{'data'}[0]->{'conditions'}[0]->{'code'};
$metar34conditionstext         = $parsed_json->{'data'}[0]->{'conditions'}[0]->{'text'};
$metar34clouds          = $parsed_json->{'data'}[0]->{'clouds'}[0]->{'code'};
$metar34cloudstext          = $parsed_json->{'data'}[0]->{'clouds'}[0]->{'text'};
$metar34dewpointc          = $parsed_json->{'data'}[0]->{'dewpoint'}->{'celsius'};
$metar34dewpointf          = $parsed_json->{'data'}[0]->{'dewpoint'}->{'fahrenheit'};
$metar34temperaturec          = $parsed_json->{'data'}[0]->{'temperature'}->{'celsius'};
$metar34temperaturef          = $parsed_json->{'data'}[0]->{'temperature'}->{'fahrenheit'};
$metar34humidity          = $parsed_json->{'data'}[0]->{'humidity'}->{'percent'};
$metar34visibility        = $parsed_json->{'data'}[0]->{'visibility'}->{'meters'};
$metar34windir          = $parsed_json->{'data'}[0]->{'wind'}->{'degrees'};
$metar34windspeedmph          = $parsed_json->{'data'}[0]->{'wind'}->{'speed_mph'};
$metar34windspeedkmh          = number_format($metar34windspeedmph*1.60934,0);//kmh
$metar34windspeedkts          = $parsed_json->{'data'}[0]->{'wind'}->{'speed_kts'};
$metar34windspeedms          = number_format($metar34windspeedmph*0.44704,1);
$metar34raininches          = $parsed_json->{'data'}[0]->{'rain_in'};
$metar34rainmm          = number_format($metar34raininches*25.4,2) ;
$metar34visibility=str_replace(',', '', $metar34visibility);
$metar34vismiles        = number_format($metar34visibility*0.000621371,1) ;
$metar34viskm        = number_format($metar34visibility*0.00099999969062399994,1) ;
// icon + description from the shared decoder (metar34wx.php): intensity by token, most-covered cloud layer.
// A report older than 2 h is not current: $metar34stale, and the description reads "Data Offline".
include_once(__DIR__ . '/metar34wx.php');
$metar34node  = metar34_load();
$metar34stale = ($metar34node === null);
$metar34age   = metar34_age();
$_isnight     = ($now > $suns2 || $now < $sunrs2);
list($sky_icon, $sky_desc) = metar34_sky($metar34node, $_isnight);
$sky_desc     = str_replace("\n", ' <br>', $sky_desc);
//end weather34 metar aviation script API	 

?>
