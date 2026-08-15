<?php 
//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
error_reporting(0);
$copyYear = 2015;$curYear = date('Y');$copyrightcredit='&copy; weather34.com original CSS/SVG/PHP '.$copyYear . (($copyYear != $curYear) ? '-' . $curYear : 'Copyright');
// Speed
function ktsTokmh(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(1.852*$weather[$field],1);
}

function ktsTomph(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(1.150779*$weather[$field],1);
}

function ktsToms(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.514444*$weather[$field],2);
}

function mphTokmh(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(1.609344*$weather[$field],1);
}

function mphTokts(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.868976*$weather[$field],1);
}

function mphToms(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.44704*$weather[$field],1);
}

function kmhTokts(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.5399568*$weather[$field],1);
}

function kmhTomph(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.621371*$weather[$field],1);
}

function kmhToms(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.2777778*$weather[$field],1);
}

function msTokmh(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(3.6*$weather[$field],1);
}

function msTokts(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = round((float)(1.943844*$weather[$field]), 1);
}

function msTomph(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(2.236936*$weather[$field], 1);
}

function anyWtomph($field){
	if($weather["wind_units"]=='mph') return $field;
	if($weather["wind_units"]=='kts') return number_format(1.150779*$field,1);
	if($weather["wind_units"]=='kmh') return number_format(0.621371*$field,1);
	if($weather["wind_units"]=='ms')  return number_format(2.236936*$field,1);
}

function anyWtoms($field){
	if($weather["wind_units"]=='ms')  return $field;
	if($weather["wind_units"]=='kts') return number_format(0.514444*$field,1);
	if($weather["wind_units"]=='kmh') return number_format(0.2777778*$field,1);
	if($weather["wind_units"]=='mph') return number_format(0.44704*$field,1);
}

// Temperature
function cToF(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = cToFDirect($weather[$field]);
}

function fToC(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = fToCDirect($weather[$field]);
}

function cToFrel(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = round((float)((9/5)*($weather[$field])), 1);
}

function fToCrel(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = round((float)((5/9)*($weather[$field])), 1);
}

function fToCDirect($field){
	return round((float)((5/9)*($field-32)), 1);
}

function cToFDirect($field){
	return  number_format((float)$field*(9/5)+32,1);
}

// Pressure
function mbToin(&$weather, $field, $conversion){
	if(!isset($weather[$field])) return;
	$weather[$field] = round((float)(0.02952999*$weather[$field] * $conversion), 2);
}

function inTomb(&$weather, $field, $conversion){
	if(!isset($weather[$field])) return;
	$weather[$field] = round((float)(33.86388158*$weather[$field] * $conversion), 2);
}

// Depth
function mmToin(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] = number_format(0.03937008*$weather[$field], 2);
}

function inTomm(&$weather, $field){
	if(!isset($weather[$field])) return;
	$weather[$field] =number_format(25.4*$weather[$field], 1);
}

// Calculates "real feel" heat index valid only at lower temperatures (up to 79 F)
function heatIndexLow($t, $rh) {
	// Assumes Fahrenheit
	return 0.5 * ($t + 61.0 + (($t - 68.0) * 1.2) + ($rh * 0.094));
}

// Calculates "real feel" heat index valid only at higher temperatures (beginning around 79-80 F), the traditional heat index formula
function heatIndexHigh($t, $rh) {
	// Assumes Fahrenheit
	$heatIndex = -42.379 + 2.04901523 * $t + 10.1433127*$rh - .22475541*$t*$rh - .00683783 *$t * $t - .05481717 * $rh * $rh + .00122874*$t*$t*$rh + .00085282 *$t * $rh *$rh - .00000199 *$t *$t *$rh * $rh;

	// Adjustment formula, adding or subtracting as much as a couple degrees at extreme ends of temperature/humidity ranges
	$a = 0;
	if ($rh < 13 && ($t >= 80 && $t <= 112)) {
		$a=((13 - $rh ) / 4) * sqrt((17-abs($t - 95))/17);
		$a = -$a;
	};
	if ($rh > 85 && ($t >= 80 && $t <= 87)) {
		$a=(($rh - 85)/10) * ((87 - $t) / 5);
	};

	$heatIndex += $a;
	return $heatIndex;
}

// Ruthfusz heat index formula
// http://www.wpc.ncep.noaa.gov/html/heatindex_equation.shtml
function heatIndex($temp, $rh) {
	global $weather;
	$t = anyToF($temp);

	// First try simple formula, valid when calculated heat index < 80 degrees F
	$heatIndex = heatIndexLow($t, $rh);

	// If too warm, do the complicated formula instead
	if ($heatIndex > 80)
	{
		$heatIndex = heatIndexHigh($t, $rh);
	}

if ($weather["temp_units"] == 'C'){
	$heatIndex = fToCDirect($heatIndex);
}
	return round($heatIndex, 1);
}

function getUpdatedString($datetime) {
	global $showDate, $dateFormat, $timeFormat;
	
	if ($showDate) {
		return '<div class="updatedtime"><span>' . date($dateFormat) . '</span><br />' . date($timeFormat) . '</div>';
	}
	else {
		return '<div class="updatedtime"><span>Updated</span><br />' . date($timeFormat) . '</div>';
	}
}

function anyToC($field){
	global $weather;
	if ($weather["temp_units"] == 'C'){
		return $field;
	} else {
		return fToCDirect($field);
	}
}

function anyToF($field){
	global $weather;
	if ($weather["temp_units"] == 'F') {
		return $field;
	} else {
		return cToFDirect ($field);
	}
}

function distance($lat, $lon, $lati, $longi) {
	$lat1 = deg2rad($lati);
	$lat2 = deg2rad($lat);
	$long1 = deg2rad($longi);
	$long2 = deg2rad($lon);

	// Great circle calculation uses the radius of earth, 6371 km
	return 6371 * acos(sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($long2-$long1));
}

$offline='<svg id=i-info viewBox="0 0 32 32" width=6 height=6 fill=#ff8841 stroke=#ff8841 stroke-linecap=round stroke-linejoin=round stroke-width=6.25%><path d="M16 14 L16 23 M16 8 L16 10" /><circle cx=16 cy=16 r=14 />
</svg>';
$online='<svg id=i-info viewBox="0 0 32 32" width=6 height=6 fill=#9aba2f stroke=#9aba2f stroke-linecap=round stroke-linejoin=round stroke-width=6.25%><path d="M16 14 L16 23 M16 8 L16 10" /><circle cx=16 cy=16 r=14 />
</svg>';
