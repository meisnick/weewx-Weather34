<?php
/* 
-----------------
Language Translation File for HOMEWEATHERSTATION Template
Language: Swedish
Translated by : Mats Ahlklo, metzallo@gmail.com 2017-05-01
Developed By: Lightmaster/Brian Underdown/Erik M Madsen
January  2017
Revised: 2019
-----------------
*/
# -----------------------------------------------------
# Day / Months do not edit
# -----------------------------------------------------
$day        = date("l");
$day2       = date("l", time() + 86400);
$daynum     = date("j");
$monthtrans = date("F");
$year       = date("Y");
# -----------------------------------------------------
# -----------------------------------------------------
setlocale(LC_TIME, "sv_SE.UTF-8");
$lang                           = array();
// Menu
$lang['Settings']               = 'Inställningar';
$lang['Layout']                 = 'Byta layout';
$lang['Lighttheme']             = 'Ljust tema';
$lang['Darktheme']              = 'Mörkt tema';
$lang['Nonmetric']              = 'US (F) ';
$lang['Metric']                 = 'Metriskt (C)';
$lang['UKmetric']               = 'UK (MPH - Metriskt) ';
$lang['Scandinavia']            = 'Scandinavien (M/S)';
$lang['Worldwideearthquakes']   = 'Jordskalv världen runt';
$lang['Toggle']                 = 'Växla till fullskärm ';
$lang['Contactinfo']            = 'Station & kontakt Info';
$lang['Templateinfo']           = 'Template information';
$lang['language']               = 'Välj språk';
$lang['Weatherstationinfo']     = 'Väderstations info';
$lang['Webdesigninfo']          = 'Webdesign info';
$lang['Contact']                = 'Contact';
//days
$lang['Monday']                 = 'Monday';
$lang['Tuesday']                = 'Tuesday';
$lang['Wednesday']              = 'Wednesday';
$lang['Thursday']               = 'Thursday';
$lang['Friday']                 = 'Friday ';
$lang['Saturday']               = 'Saturday';
$lang['Sunday']                 = 'Sunday';
//months
$lang['January']                = 'January';
$lang['Febuary']                = 'Febuary';
$lang['March']                  = 'March';
$lang['April']                  = 'April';
$lang['May']                    = 'May';
$lang['June']                   = 'June';
$lang['July']                   = 'July';
$lang['August']                 = 'August';
$lang['September']              = 'September';
$lang['October']                = 'October';
$lang['November']               = 'November';
$lang['December']               = 'December';
//temperature
$lang['Temperature']            = 'Temperatur';
$lang['Feelslike']              = 'Känns';
$lang['Humidity']               = 'Fuktighet';
$lang['Dewpoint']               = 'Daggpunkt';
$lang['Trend']                  = 'Trend ';
$lang['Heatindex']              = 'Värme index';
$lang['Apparent']               = 'Skenbar';
$lang['Windchill']              = 'Kyleffekt ';
$lang['IndoorTemp']             = 'Inomhustemperatur';
$lang['Tempfactors']            = 'Temp faktor';
$lang['Nocautions']             = 'Inga varningar';
$lang['Wetbulb']                = 'Våttemperatur';
$lang['dry']                    = '& torr';
$lang['verydry']                = '& mycket torrt';
//new feature temperature feels
$lang['FreezingCold']           = 'Extremt kallt';
$lang['FeelingVeryCold']        = 'Väldigt kallt';
$lang['FeelingCold']            = 'Kallt';
$lang['FeelingCool']            = 'Svalt';
$lang['FeelingComfortable']     = 'Komfortabelt';
$lang['FeelingWarm']            = 'Varmt';
$lang['FeelingHot']             = 'För varmt';
$lang['FeelingUncomfortable']   = 'Obekvämt varmt';
$lang['FeelingVeryHot']         = 'Väldigt hett';
$lang['FeelingExtremelyHot']    = 'Extremt hett';
//wind
$lang['Windspeed']              = 'Vind hast.';
$lang['Gust']                   = 'Vind stöt';
$lang['Direction']              = 'Vind riktn.';
$lang['Gusting']                = 'Vindbyar på';
$lang['Blowing']                = 'Blåser med';
$lang['Wind']                   = 'Vind';
$lang['Wind Run']               = 'Vind Run';
// Wind phrases for Beaufort scale for windspeed area
$lang['Calm']                   = 'Stiltje';
$lang['Lightair']               = 'Nästan siltje';
$lang['Lightbreeze']            = 'Lätt bris';
$lang['Gentelbreeze']           = 'Måttlig bris';
$lang['Moderatebreeze']         = 'Frisk bris';
$lang['Freshbreeze']            = 'Styv bris';
$lang['Strongbreeze']           = 'Hård bris';
$lang['Neargale']               = 'Styv kuling';
$lang['Galeforce']              = 'Hård kuling';
$lang['Stronggale']             = 'Halv storm';
$lang['Storm']                  = 'Storm';
$lang['Violentstorm']           = 'Svår storm';
$lang['Hurricane']              = 'Orkan';
// Wind phrases from Beaufort scale for current conditions area
$lang['CalmConditions']         = 'Lugnt';
$lang['LightBreezeattimes']     = 'Lätt bris';
$lang['MildBreezeattimes']      = 'Måttlig bris';
$lang['ModerateBreezeattimes']  = 'Frisk bris';
$lang['FreshBreezeattimes']     = 'Styv bris';
$lang['StrongBreezeattimes']    = 'Hård bris';
$lang['NearGaleattimes']        = 'Styv kuling';
$lang['GaleForceattimes']       = 'Hård kuling';
$lang['StrongGaleattimes']      = 'Halv storm';
$lang['StormConditions']        = 'Storm';
$lang['ViolentStormConditions'] = 'Svår storm';
$lang['HurricaneConditions']    = 'Orkan';
$lang['Avg']                    = '<span2> Medelv. </span2>';
//wind direction compass
$lang['Northdir']               = 'Rakt <span>Nordlig<br></span>';
$lang['NNEdir']                 = 'Nord Nord <br><span>Ost</span>';
$lang['NEdir']                  = 'Nord <span> Ost<br></span>';
$lang['ENEdir']                 = 'Ost Nord<br><span>Ost</span>';
$lang['Eastdir']                = "Rakt <span> Ostlig<br></span>";
$lang['ESEdir']                 = 'Ost Syd<br><span>Ost</span>';
$lang['SEdir']                  = 'Syd <span> Ost</span>';
$lang['SSEdir']                 = 'Syd Syd<br><span>Ost</span>';
$lang['Southdir']               = 'Rakt <span> Sydlig</span>';
$lang['SSWdir']                 = 'Syd Syd<br><span>Väst</span>';
$lang['SWdir']                  = 'Syd <span> Väst</span>';
$lang['WSWdir']                 = 'Väst Syd<br><span>Västst</span>';
$lang['Westdir']                = 'Rakt <span> Västlig</span>';
$lang['WNWdir']                 = 'Väst Nord<br><span>Väst</span>';
$lang['NWdir']                  = 'Nord <span> Väst</span>';
$lang['NWNdir']                 = 'Nord Nord<br><span>Väst</span>';
//wind direction avg
$lang['North']                  = 'N';
$lang['NNE']                    = 'NNO';
$lang['NE']                     = 'NO';
$lang['ENE']                    = 'ONO';
$lang['East']                   = 'O ';
$lang['ESE']                    = 'OSO';
$lang['SE']                     = 'SO';
$lang['SSE']                    = 'SSO';
$lang['South']                  = 'S';
$lang['SSW']                    = 'SSV';
$lang['SW']                     = 'SV';
$lang['WSW']                    = 'VSV';
$lang['West']                   = 'V';
$lang['WNW']                    = 'VNV';
$lang['NW']                     = 'NV';
$lang['NWN']                    = 'NVN';
//rain
$lang['raintoday']              = 'Nederbörd idag';
$lang['Rate']                   = 'Intensitet';
$lang['Rainfall']               = 'Regn';
$lang['Precip']                 = 'Nederb.'; // must be short name do not use full precipatation !!!! ///
$lang['Rain']                   = 'Regn';
$lang['Heavyrain']              = 'Kraftigt regn';
$lang['Flooding']               = 'Eventuell översvämmning';
$lang['Rainbow']                = 'Rainbow';
$lang['Windy']                  = 'Windy';
//sun -moon-daylight-darkness
$lang['Sun']                    = 'Sol';
$lang['Moon']                   = 'Måne';
$lang['Sunrise']                = 'Sol uppgång';
$lang['Sunset']                 = 'Sol nedgång';
$lang['Moonrise']               = 'Måne upp ';
$lang['Moonset']                = 'Måne ner';
$lang['Night']                  = 'Natt ';
$lang['Day']                    = 'Dag';
$lang['Nextnewmoon']            = 'Nästa ny måne';
$lang['Nextfullmoon']           = 'Nästa full måne';
$lang['Luminance']              = 'Luminans';
$lang['Moonphase']              = 'Månfas';
$lang['Estimated']              = 'Uppskattad';
$lang['Daylight']               = 'Dagsljus';
$lang['Darkness']               = 'mörker';
$lang['Daysold']                = 'Dagar gammal';
$lang['Belowhorizon']           = 'under<br>horisonten';
$lang['Mintill']                = '<br>min. till';
$lang['Till']                   = 'Till ';
$lang['Minago']                 = ' min. sedan';
$lang['Hrs']                    = ' timmar';
$lang['Min']                    = ' min.';
$lang['TotalDarkness']          = 'Totalt mörker';
$lang['TotalDaylight']          = 'Totalt ljus';
$lang['Below']                  = 'är under horisonten';
$lang['Newmoon']                = 'Nymåne';
$lang['Waxingcrescent']         = 'Tillt. halvmåne 1 kvart.';
$lang['Firstquarter']           = 'Halvmåne 1 kvart.';
$lang['Waxinggibbous']          = 'Tillt. halvmåne 2 kvart.';
$lang['Fullmoon']               = 'Fullmåne';
$lang['Waninggibbous']          = 'Avt. halvmåne 3 kvart.';
$lang['Lastquarter']            = 'Halvmåne 3 kvart.';
$lang['Waningcrescent']         = 'Avt. halvmåne 4 kvart.';
//trends
$lang['Falling']                = 'Fallande';
$lang['Rising']                 = 'Stigande';
$lang['Steady']                 = 'Stabilt';
$lang['Rapidly']                = 'Snabb';
$lang['Temp']                   = 'Temp.';
//Solar-UV
//uv
$lang['Nocaution']              = 'Ingen <color>åtgärd</color> behövs';
$lang['Wearsunglasses']         = 'Använd <color>solglasögon</color> vid ljusa dagar';
$lang['Stayinshade']            = 'Stanna i skuggan mitt på dagen när <color>solen</color> är som starkast';
$lang['Reducetime']             = 'Minska tiden i <color>solen</color> mellan 10 och 16 ';
$lang['Minimize']               = '<color>sun</color> exponering mellan 10 och 16 ';
$lang['Trytoavoid']             = 'Försök undvika <color>sun</color> exponering mellan 10 och 16 ';
//solar
$lang['Poor']                   = 'Ingen <color> <br>solstrålning</color>';
$lang['Low']                    = 'Låg <br><color>solstrålning</color>';
$lang['Moderate']               = 'Måttlig <br><color>solstrålning</color>';
$lang['Good']                   = 'Hög <br><color>solstrålning</color>';
$lang['Solarradiation']         = 'Solstrålning';
//current sky
$lang['Currentsky']             = 'Nuvarande förhållanden';
$lang['Currently']              = 'För närvarande';
$lang['Cloudcover']             = 'Molnigt';
//Notifications
$lang['Nocurrentalert']         = 'Inga väder varningar';
$lang['Windalert']              = 'Vind stöt';
$lang['Tempalert']              = 'Hög temperatur';
$lang['Heatindexalert']         = 'Värme Index varning ';
$lang['Windchillalert']         = 'Kyleffekts varning, vind';
$lang['Dewpointalert']          = 'Obehaglig luftfukt.';
$lang['Dewpointcolderalert']    = 'Daggpunkt känns kallare';
$lang['Feelslikecolderalert']   = 'Känns kallare';
$lang['Feelslikewarmeralert']   = 'Känns varmare';
$lang['Rainratealert']          = 'mm/t<span> regn';
//Earthquake Notifications
$lang['Regional']               = 'Regionala jordbävningar';
$lang['Significant']            = 'Större jordbävningar';
$lang['Nosignificant']          = 'Inga större jordbävningar';
//Main page
$lang['Barometer']              = 'Barometer';
$lang['UVSOLAR']                = 'UV | <span style="color:#f26c4f">Sol data</span>';
$lang['Earthquake']             = 'Jordskalvs data';
$lang['Daynight']               = 'Dagsljus och natt info.';
$lang['SunPosition']            = 'Sun Position';
$lang['Location']               = 'Plats ';
$lang['Hardware']               = 'Hårdvara';
$lang['Rainfalltoday']          = 'Nederbörd idag';
$lang['Windspeed']              = 'Vind hast.';
$lang['Winddirection']          = 'Vind riktning';
$lang['Measured']               = 'Uppmätt idag';
$lang['Forecast']               = 'Väderprognos';
$lang['Forecastahead']          = 'Väderprognos 10 dagar';
$lang['Forecastsummary']        = 'Väderprognos 2 dagar';
$lang['WindGust']               = 'Vind hastighet | Stöt';
$lang['Hourlyforecast']         = 'Prognos per timme ';
$lang['Significantearthquake']  = 'Betydliga jordbävningar';
$lang['Regionalearthquake']     = 'Regionala jordbävningar';
$lang['Average']                = 'Genomsnitt';
$lang['Notifications']          = 'Väder larm';
$lang['Indoor']                 = 'Inomhus';
$lang['Today']                  = 'I Dag';
$lang['Tonight']                = 'I Natt';
$lang['Tomorrow']               = 'I Morgon';
$lang['Tomorrownight']          = 'I Morgon Natt';
$lang['Updated']                = 'Uppdaterad';
$lang['Meteor']                 = 'Meteorskur info';
$lang['Firerisk']               = 'Brandrisk';
$lang['Localtime']              = 'Lokal tid';
$lang['Nometeor']               = 'Inga meteorskurar';
$lang['LiveWebCam']             = 'WebCam';
$lang['Online']                 = 'Online';
$lang['Offline']                = 'Offline';
$lang['Weatherstation']         = 'Weather Station';
$lang['Cloudbase']              = 'Cloudbase';
$lang['uvalert']                = 'Caution High UVINDEX';
$lang['Max']                    = 'Max';
$lang['Min']                    = 'Min';
//earthquake TOP MODULE 10 July 2017
$lang['MicroE']                  = 'Micro Earthquake';
$lang['MinorE']                  = 'Minor Earthquake';
$lang['LightE']                  = 'Light Earthquake';
$lang['ModerateE']               = 'Moderate Earthquake';
$lang['StrongE']                 = 'Strong Earthquake';
$lang['MajorE']                  = 'Major Earthquake';
$lang['GreatE']                  = 'Great Earthquake';
$lang['RegionalE']              = 'Regional';
$lang['Conditions']             = 'Conditions';
$lang['Cloudbase Height']       = 'Cloudbase Height';
$lang['Station']                = 'Station';
$lang['Detailed Forecast']      = 'Detailed Forecast';
$lang['Summary Outlook']        = 'Summary';
//Air Quality
$lang['Hazordous']              = 'Hazardous Conditions';
$lang['VeryUnhealthy']          = 'Very Unhealthy';
$lang['Unhealthy']              = 'Unhealthy Air Quality';
$lang['UnhealthyFS']            = 'Unhealthy For Some';
$lang['Moderate']               = 'Moderate Air Quality ';
$lang['Good']                   = 'Good Air Quality ';
#notification additions
$lang['notifyTitle']            = 'Väder varningar';
$lang['notifyAlert']             = "Alert";
$lang['notifyLowBatteryAlert']   = "Low Battery Alert";
$lang['notifyConsoleLowBattery'] = "Console's battery is low";
$lang['notifyStationLowBattery'] = "Station's battery is low";
$lang['notifyUVIndex']           = "UV-Index Caution";
$lang['notifyUVExposure']        = "Reduce Sun Exposure";
$lang['notifyHeatExaustion']     = "Heat Exhaustion";
$lang['notifyExtremeWind']       = "Extreme Wind Warning";
$lang['notifyGustUpTo']          = "Gusts up to";
$lang['notifySeekShelter']       = "Seek shelter <notifyred><b>immediately</b></notifyred>";
$lang['notifyHighWindWarning']   = "High Wind Warning";
$lang['notifySustainedAvg']      = "Sustained avg";
$lang['notifyWindAdvisory']      = "Wind Advisory";
$lang['notifyFreezing']          = "Below Freezing";
$lang['StationTime']             = 'Stationstid';
$lang['MoonPhase']               = 'Månfas';
$lang['LocalNowcast']            = 'Lokal väderprognos';
$lang['Lightning']               = 'Blixt';
$lang['AirQuality']              = 'Luftkvalitet';
$lang['UVSolar']                 = 'UV & Sol';
$lang['Webcam']                  = 'Webkamera';
$lang['SpaceWeather']            = 'Rymdväder';
$lang['NwsRadar']                = 'NWS Radar';
$lang['TemperatureYear']         = 'Temperatur år';
$lang['WindGustYear']            = 'Vindbyar år';
$lang['RainfallTotals']          = 'Nederbördsmängder';
$lang['WeatherAdvisory']         = 'Vädervarning';

# Synced from lang.en.php (keys added for newer modules)
$lang['Active']                   = 'Aktiv';
$lang['AIndex']                   = 'A-index';
$lang['AuroraAGuide']             = 'är en indikator på möjligen förbättrade VHF-radioförbindelser över avstånd på 1000 miles eller mer. Vid kraftig solaktivitet möjliggör frekvenser från 28 till 433 MHz eller högre radioförbindelser på mellersta och höga breddgrader.';
$lang['AuroraHamGuide']           = 'Norrskensförbindelser kan användas av VHF-radioamatörer. Norrskens-scatterutbredning möjliggör förbindelser mellan radioamatörer.';
$lang['AuroraHamGuide2']          = 'Norrskens-scatterförbindelser med specialiserade tekniker ger avstånd på 2000 km eller mer på 28 MHz/50 MHz/144 MHz/433 MHz.';
$lang['AuroraKpGuide']            = 'är en bra indikator för att se norrsken. Ju högre Kp-index, desto större sannolikhet. Det uppskattade planetära 3-timmars Kp-indexet kommer från markbaserade magnetometrar.';
$lang['AuroraNowcast']            = 'Norrskens-nowcast';
$lang['DataProvidedBy']           = 'Data tillhandahålls av';
$lang['EstimatedZHR']             = 'Uppskattad ZHR';
$lang['Geo']                      = 'Geo';
$lang['GeomagneticMajorStorm']    = 'Kraftig geomagnetisk storm';
$lang['GeomagneticSevereStorm']   = 'Svår geomagnetisk storm';
$lang['GeomagneticStorm']         = 'Geomagnetisk storm';
$lang['Guide']                    = 'Guide';
$lang['Information']              = 'Information';
$lang['Kp']                       = 'Kp';
$lang['KpGeomagneticForecast']    = 'Kp geomagnetisk prognos';
$lang['KpIndex']                  = 'Kp-index';
$lang['KpPlanetaryIndex']         = 'KP-PLANETÄRT INDEX';
$lang['LastUpdated']              = 'Senast uppdaterad';
$lang['LatestObserved']           = 'Senast observerad';
$lang['MajorStorm']               = 'Kraftig storm';
$lang['MeteorShower']             = 'Meteorskur';
$lang['MeteorShowers']            = 'Meteorskurar';
$lang['MinorGeomagneticStorm']    = 'Mindre geomagnetisk storm';
$lang['MinorStorm']               = 'Mindre storm';
$lang['NoMeteorShower']           = 'Inga meteorskurar';
$lang['NorthernLights']           = 'Norrsken';
$lang['NotActive']                = 'Inte aktiv';
$lang['Peaks']                    = 'Maximum';
$lang['PeakViewingNow']           = 'Bästa sikt nu';
$lang['Quiet']                    = 'Lugnt';
$lang['QuietNoGeomagneticStorm']  = 'Lugnt, ingen geomagnetisk storm';
$lang['QuietNoStorm']             = 'Lugnt, inga stormar';
$lang['Radio']                    = 'Radio';
$lang['RadioAurora']              = 'Radio-norrsken';
$lang['RadioHamGuide']            = 'Radioamatörguide';
$lang['SevereStorm']              = 'Svår storm';
$lang['Solar']                    = 'Sol';
$lang['WeakGeomagneticStorm']     = 'Svag geomagnetisk storm';
$lang['WeakStorm']                = 'Svag storm';
$lang['ViewLine']                 = 'Siktlinje';
$lang['Forecast3Day']             = '3-dygnsprognos';
$lang['HemPower']                 = 'Hem.-effekt';
$lang['Ovation']                  = 'Ovation';
$lang['ForecastUnavailable']      = 'Prognos ej tillgänglig — uppdateras varje timme.';
$lang['ApIndex']                  = 'Ap-index';
$lang['Ap']                       = 'Ap';
$lang['Type']                     = 'Typ';
$lang['Date']                     = 'Datum';
$lang['GeomagneticActivityProbabilities'] = 'Sannolikheter för geomagnetisk aktivitet';
$lang['Level']                    = 'Nivå';
$lang['KpIndexForecast']          = 'Kp-indexprognos (UT)';
$lang['Period']                   = 'Period';
$lang['Issued']                   = 'Utfärdad';
$lang['HemispherePowerUnavailable'] = 'Halvklotseffekt ej tillgänglig — uppdateras var 5:e min.';
$lang['NorthernHemisphereOvation'] = 'Norra halvklotet — Ovation';
$lang['SouthernHemisphereOvation'] = 'Södra halvklotet — Ovation';
$lang['ObservationTimeUTC']       = 'Observationstid UTC';
$lang['NorthGW']                  = 'Norr (GW)';
$lang['SouthGW']                  = 'Söder (GW)';
$lang['G1PlusStorm']              = 'G1+ storm';
$lang['Nowcast6h']                = '6-timmars nowcast';
$lang['Snow6h']                   = 'Snö 6 h';
$lang['Rain6h']                   = 'Regn 6 h';
$lang['Dry']                      = 'Torrt';
$lang['Slight']                   = 'Liten';
$lang['Chance']                   = 'Möjlig';
$lang['Likely']                   = 'Trolig';
$lang['NoHyperlocalNowcast']      = 'Ingen lokal nowcast tillgänglig.';
$lang['NwsWeatherAlerts']         = 'NWS-vädervarningar';
$lang['ActiveNwsWeatherAlerts']   = 'Aktiva NWS-vädervarningar';
$lang['Fetched']                  = 'Hämtad';
$lang['NoActiveWeatherAlerts']    = 'Inga aktiva vädervarningar just nu.';
$lang['Headline']                 = 'Rubrik';
$lang['Effective']                = 'Gäller från';
$lang['Expires']                  = 'Upphör';
$lang['Unknown']                  = 'Okänd';
$lang['AreaForecastDiscussion']   = 'Regional prognosdiskussion';
$lang['KeyMessages']              = 'Huvudbudskap';
$lang['ShortTerm']                = 'Kort sikt';
$lang['LongTerm']                 = 'Lång sikt';
$lang['OutlookTrend']             = 'Trend';
$lang['ShortTermDiscussion']      = 'Diskussion, kort sikt';
$lang['LongTermDiscussion']       = 'Diskussion, lång sikt';
$lang['NoSourceText']             = 'Ingen källtext tillgänglig.';
$lang['NoSummary']                = 'Ingen sammanfattning';
$lang['GeneratedYet']             = 'genererad än.';
$lang['CheckBackgroundScript']    = 'Kontrollera att bakgrundsskriptet körs.';
$lang['ConfirmOllamaStatus']      = 'Kontrollera <span class="hl-orange">Ollama-status</span> på Pi:n.';
$lang['Strikes']                  = 'Nedslag';
$lang['Last3Hrs']                 = 'Senaste 3 h';
$lang['Distance']                 = 'Avstånd';
$lang['LastStrike']               = 'Senaste nedslag';
$lang['LastDistanceAt']           = 'Senaste avstånd kl.';
$lang['AllTimeStrikeTotal']       = 'Nedslag totalt';
$lang['Miles']                    = 'miles';
$lang['Km']                       = 'km';
?>