<?php
/*
-----------------
Language Translation File for HOMEWEATHERSTATION Template
Language: German
Translated by: Stefan Griessner
Developed By: Lightmaster/Brian Underdown/Erik M Madsen
October/November 2016
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
setlocale(LC_TIME, "de_DE.UTF-8");
$lang                           = array();
// Menu
$lang['Settings']               = 'Einstellungen';
$lang['Layout']                 = 'Layout Wechseln';
$lang['Lighttheme']             = 'Helles Theme';
$lang['Darktheme']              = 'Dunkles Theme';
$lang['Nonmetric']              = 'US (F)';
$lang['Metric']                 = 'Metrisch (C)';
$lang['UKmetric']               = 'UK (MPH - Metrisch) ';
$lang['Scandinavia']            = 'Scandinavia(M/S)';
$lang['Worldwideearthquakes']   = 'Weltweites Erdbeben';
$lang['Toggle']                 = 'Ansicht wechseln';
$lang['Contactinfo']            = 'Kontaktinformation';
$lang['Templateinfo']           = 'Vorlageninfo';
$lang['language']               = 'Sprache auswählen';
$lang['Weatherstationinfo']     = 'Wetterstationsinfo';
$lang['Webdesigninfo']          = 'Template Info';
$lang['Contact']                = 'Kontakt';
//days
$lang['Monday']                 = 'Montag';
$lang['Tuesday']                = 'Dienstag';
$lang['Wednesday']              = 'Mittwoch';
$lang['Thursday']               = 'Donnerstag';
$lang['Friday']                 = 'Freitag ';
$lang['Saturday']               = 'Samstag';
$lang['Sunday']                 = 'Sonntag';
//months
$lang['January']                = 'Januar';
$lang['Febuary']                = 'Februar';
$lang['March']                  = 'März';
$lang['April']                  = 'April';
$lang['May']                    = 'Mai';
$lang['June']                   = 'Juni';
$lang['July']                   = 'Juli';
$lang['August']                 = 'August';
$lang['September']              = 'September';
$lang['October']                = 'Oktober';
$lang['November']               = 'November';
$lang['December']               = 'Dezember';
//temperature
$lang['Temperature']            = 'Temperatur';
$lang['Feelslike']              = 'Gefühlt';
$lang['Humidity']               = 'Feuchtigkeit';
$lang['Dewpoint']               = 'Taupunkt';
$lang['Trend']                  = 'Trend ';
$lang['Heatindex']              = 'Hitzeindex';
$lang['Apparent']               = 'Scheinbar';
$lang['Windchill']              = 'Gefühlt ';
$lang['IndoorTemp']             = 'Innentemperatur';
$lang['Tempfactors']            = 'Temperaturfaktor';
$lang['Nocautions']             = 'Vorsichtsmaßnahmen';
$lang['Wetbulb']                = 'Kühlgrenztemperatur';
$lang['dry']                    = '& trocken';
$lang['verydry']                = '& sehr trocken';
//new feature temperature feels
$lang['FreezingCold']           = 'Sehr kalt';
$lang['FeelingVeryCold']        = 'Gefühlt sehr kalt';
$lang['FeelingCold']            = 'Gefühlt kalt';
$lang['FeelingCool']            = 'Gefühlt kühl';
$lang['FeelingComfortable']     = 'Gefühlt komfortabel ';
$lang['FeelingWarm']            = 'Gefühlt warm';
$lang['FeelingHot']             = 'Gefühlt heiss';
$lang['FeelingUncomfortable']   = 'Gefühlt unkomfortabel';
$lang['FeelingVeryHot']         = 'Gefühlt sehr heiss';
$lang['FeelingExtremelyHot']    = 'Gefühlt extrem heiss';
//wind
$lang['Windspeed']              = 'Windgeschwindigkeit';
$lang['Gust']                   = 'Böe';
$lang['Direction']              = 'Richtung';
$lang['Gusting']                = 'in Böeen';
$lang['Blowing']                = 'treibend';
$lang['Wind']                   = 'Wind';
$lang['Wind Run']               = 'Windmenge';
// Wind phrases for Beaufort scale for windspeed area
$lang['Calm']                   = 'Windstill';
$lang['Lightair']               = 'Nahezu Windstill';
$lang['Lightbreeze']            = 'Leichte Brise ';
$lang['Gentelbreeze']           = 'Mässige Brise';
$lang['Moderatebreeze']         = 'Mittlere Brise';
$lang['Freshbreeze']            = 'Auffrischende Brise';
$lang['Strongbreeze']           = 'Starke Brise';
$lang['Neargale']               = 'Steifer Wind';
$lang['Galeforce']              = 'Stürmischer Wind';
$lang['Stronggale']             = 'Stark stürmisch';
$lang['Storm']                  = 'Schwerer Sturm';
$lang['Violentstorm']           = 'Kräftiger Sturm';
$lang['Hurricane']              = 'Orkan';
// Wind phrases from Beaufort scale for current conditions area
$lang['CalmConditions']         = 'Windstill';
$lang['LightBreezeattimes']     = 'Teils leichter Wind ';
$lang['MildBreezeattimes']      = 'Teils leichter Wind ';
$lang['ModerateBreezeattimes']  = 'Teils mässiger Wind';
$lang['FreshBreezeattimes']     = 'Teils frischer Wind';
$lang['StrongBreezeattimes']    = 'Teils starker Wind';
$lang['NearGaleattimes']        = 'Teils steifer Wind';
$lang['GaleForceattimes']       = 'Teils stürmischer Wind';
$lang['StrongGaleattimes']      = 'Teils stark stürmisch';
$lang['StormConditions']        = 'Stürmische Bedingungen';
$lang['ViolentStormConditions'] = 'Heftige Sturmbedingungen';
$lang['HurricaneConditions']    = 'Hurrikanbedingungen';
$lang['Avg']                    = '<span2> Schnitt: </span2>';
//wind direction compass
$lang['Northdir']               = '<span>Nord<br></span>';
$lang['NNEdir']                 = 'Nord Nord <br><span>Ost</span>';
$lang['NEdir']                  = 'Nord <span> Ost<br></span>';
$lang['ENEdir']                 = 'Ost Nord<br><span>Ost</span>';
$lang['Eastdir']                = "<span> Ost<br></span>";
$lang['ESEdir']                 = 'Ost Süd<br><span>Ost</span>';
$lang['SEdir']                  = 'Süd <span> Ost</span>';
$lang['SSEdir']                 = 'Süd Süd<br><span>Ost</span>';
$lang['Süddir']                 = '<span> Süd</span>';
$lang['SSWdir']                 = 'Süd Süd<br><span>West</span>';
$lang['SWdir']                  = 'Süd <span> West</span>';
$lang['WSWdir']                 = 'West Süd<br><span>West</span>';
$lang['Westdir']                = '<span> West</span>';
$lang['WNWdir']                 = 'West Nord<br><span>West</span>';
$lang['NWdir']                  = 'Nord <span> West</span>';
$lang['NWNdir']                 = 'Nord Nord<br><span>West</span>';
//wind direction avg
$lang['North']                  = 'Nord';
$lang['NNE']                    = 'NNO';
$lang['NE']                     = 'NO';
$lang['ENE']                    = 'ONO';
$lang['East']                   = 'Ost ';
$lang['ESE']                    = 'OSO';
$lang['SE']                     = 'SO';
$lang['SSE']                    = 'SSO';
$lang['Süd']                    = 'Süd';
$lang['SSW']                    = 'SSW';
$lang['SW']                     = 'SW';
$lang['WSW']                    = 'WSW';
$lang['West']                   = 'West';
$lang['WNW']                    = 'WNW';
$lang['NW']                     = 'NW';
$lang['NWN']                    = 'NWN';
//rain
$lang['raintoday']              = 'Regen Heute';
$lang['Rate']                   = 'Niederschlagsrate';
$lang['Rainfall']               = 'Regen';
$lang['Precip']                 = 'Nd.'; // must be short name do not use full precipatation !!!! ///
$lang['Rain']                   = 'Regen';
$lang['Heavyrain']              = 'Starker Regen';
$lang['Flooding']               = 'Überschwemmung möglich';
$lang['Rainbow']                = 'Regenbogen';
$lang['Windy']                  = 'Windig';
//sun -moon-daylight-darkness
$lang['Sun']                    = 'Sonne';
$lang['Moon']                   = 'Mond';
$lang['Sunrise']                = 'Sonnenaufgang';
$lang['Sunset']                 = 'Sonnenuntergang';
$lang['Moonrise']               = 'Mondaufgang ';
$lang['Moonset']                = 'Monduntergang';
$lang['Night']                  = 'Nacht ';
$lang['Day']                    = 'Tag';
$lang['Nextnewmoon']            = 'Neumond';
$lang['Nextfullmoon']           = 'Vollmond';
$lang['Luminance']              = 'Luminanz';
$lang['Moonphase']              = 'Mond Phase';
$lang['Estimated']              = 'geschätzt';
$lang['Daylight']               = 'Tageslicht';
$lang['Darkness']               = 'Dunkelheit';
$lang['Daysold']                = 'Tage alt';
$lang['Belowhorizon']           = 'Unterhalb des Horizonts';
$lang['Mintill']                = '<br>Minuten bis';
$lang['Till']                   = 'bis ';
$lang['Minago']                 = ' Minuten vor';
$lang['Hrs']                    = ' Std';
$lang['Min']                    = ' Min';
$lang['TotalDarkness']          = 'Total Dunkelheit';
$lang['TotalDaylight']          = 'Total Tageslicht';
$lang['Below']                  = 'unterhalb';
$lang['Newmoon']                = 'Neumond';
$lang['Waxingcrescent']         = 'zunehmend 1.Viertel';
$lang['Firstquarter']           = 'Erstes Viertel';
$lang['Waxinggibbous']          = 'zunehmend 2.Viertel';
$lang['Fullmoon']               = 'Vollmond';
$lang['Waninggibbous']          = 'abnehmend 3.Viertel';
$lang['Lastquarter']            = 'Letztes Viertel';
$lang['Waningcrescent']         = 'abnehmender Mond';
//trends
$lang['Falling']                = 'fallend';
$lang['Rising']                 = 'steigend';
$lang['Steady']                 = '0';
$lang['Rapidly']                = 'schnell';
$lang['Temp']                   = 'Temp';
//Solar-UV
//uv
$lang['Nocaution']              = 'keine <color>Warnung</color> notwendig';
$lang['Wearsunglasses']         = 'Tragen Sie eine <color>Sonnebrille</color> an hellen Tagen';
$lang['Stayinshade']            = 'Bleiben Sie mittags im Schatten,wenn die <color>Sonne</color> am stärksten ist';
$lang['Reducetime']             = 'Reduzieren Sie die Zeit in der <color>Sonne</color> zwischen 10Uhr-14Uhr ';
$lang['Minimize']               = '<color>Meiden Sie die Sonne</color> zwischen 10Uhr-14Uhr ';
$lang['Trytoavoid']             = 'Meiden Sie die <color>Sonne</color> zwischen 10Uhr-14Uhrm ';
//solar
$lang['Poor']                   = 'Schwache Solar<color> <br>Radiation</color>';
$lang['Low']                    = 'Niedrige Solar<br><color>Radiation</color>';
$lang['Moderate']               = 'Mässige Solar <br><color>Radiation</color>';
$lang['Good']                   = 'Starke Solar <br><color>Radiation</color>';
$lang['Solarradiation']         = 'Solar Radiation';
//current sky
$lang['Currentsky']             = 'Aktuelle Bedingungen';
$lang['Currently']              = 'Gegenwärtig';
$lang['Cloudcover']             = 'Wolkendecke';
//Notifications
$lang['Nocurrentalert']         = 'Keine aktuellen Hinweise';
$lang['Windalert']              = 'Windböen bis';
$lang['Tempalert']              = 'steigende Temperaturen';
$lang['Heatindexalert']         = 'steigender Wärmeindex';
$lang['Windchillalert']         = 'es ist gefühlt frisch';
$lang['Dewpointalert']          = 'unangenehme Feuchtigkeit';
$lang['Dewpointcolderalert']    = 'es ist gefühlt kälter';
$lang['Feelslikecolderalert']   = 'es ist gefühlt kälter';
$lang['Feelslikewarmeralert']   = 'es ist gefühlt wärmer';
$lang['Rainratealert']          = 'per/hr<span> Regen möglich ';
//Earthquake Notifications
$lang['Regional']               = 'Regional Erdbeben';
$lang['Significant']            = 'Bedeutende Erdbeben';
$lang['Nosignificant']          = 'keine bedeutende Erdbeben';
//Main page
$lang['Barometer']              = 'Barometer';
$lang['UVSOLAR']                = 'UV | Solar';
$lang['Earthquake']             = 'Erdbebendaten';
$lang['Daynight']               = 'Tageslicht & Nacht Info';
$lang['SunPosition']            = 'Sun Position';
$lang['Location']               = 'Position ';
$lang['Hardware']               = 'Hardware';
$lang['Rainfalltoday']          = 'Regen heute';
$lang['Windspeed']              = 'Windspeed';
$lang['Winddirection']          = 'Windrichtung';
$lang['Measured']               = 'Gemessen heute';
$lang['Forecast']               = 'Prognose';
$lang['Forecastahead']          = 'Prognose 10 Tage';
$lang['Forecastsummary']        = 'Prognose  Heute';
$lang['WindGust']               = 'Windspeed | Böen';
$lang['Hourlyforecast']         = 'Stündliche Prognose ';
$lang['Significantearthquake']  = 'Bedeutende Erdbeben';
$lang['Regionalearthquake']     = 'Regionale Erdbeben';
$lang['Average']                = 'Durchschnitt';
$lang['Notifications']          = 'Benachrichtigungen';
$lang['Indoor']                 = 'Innen';
$lang['Today']                  = 'Heute';
$lang['Tomorrow']               = 'Morgen';
$lang['Tonight']                = 'Nacht';
$lang['Tomorrownight']          = 'Morgen Nacht';
$lang['Updated']                = 'Updated';
$lang['Meteor']                 = 'Meteor Shower Info';
$lang['Firerisk']               = 'Feuerrisiko';
$lang['Localtime']              = 'Lokale Zeit';
$lang['Nometeor']               = 'Keine Meteor Showers';
$lang['LiveWebCam']             = 'Live Web Cam';
$lang['Online']                 = 'Online';
$lang['Offline']                = 'Offline';
$lang['Weatherstation']         = 'Wetterstation';
$lang['Cloudbase']              = 'Wolkenuntergrenze';
$lang['uvalert']                = 'UVINDEX Alarm';
$lang['Max']                    = 'Max';
$lang['Min']                    = 'Min';
//earthquake TOP MODULE 10 July 2017
$lang['MicroE']                  = 'Kleines Erdbeeben';
$lang['MinorE']                  = 'Geringes Erdbeeben';
$lang['LightE']                  = 'Leichtes Erdbeeben';
$lang['ModerateE']               = 'Moderate Erdbeeben';
$lang['StrongE']                 = 'Starkes Erdbeeben';
$lang['MajorE']                  = 'Schwerses Erdbeeben';
$lang['GreatE']                  = 'Grosses Erdbeeben';
$lang['RegionalE']              = 'Regional';
$lang['Conditions']             = 'Bedingungen';
$lang['Cloudbase Height']       = 'Wolkenbasishöhe';
$lang['Station']                = 'Station';
$lang['Detailed Forecast']      = 'Detaillierte Prognose';
$lang['Summary Outlook']        = 'Zusammenfassung Ausblick';
//Air Quality
$lang['Hazordous']              = 'Gefährlich Luftqualität';
$lang['VeryUnhealthy']          = 'Sehr ungesund Luftqualität';
$lang['Unhealthy']              = 'Ungesund Luftqualität';
$lang['UnhealthyFS']            = 'Für einige ungesund';
$lang['Moderate']               = 'Mäßige Luftqualität';
$lang['Good']                   = 'Gute Luftqualität';
#notification additions
$lang['notifyTitle']             = 'Wetterstationshinweis';
$lang['notifyAlert']             = "Alarm";
$lang['notifyLowBatteryAlert']   = "Niedrige Batterie";
$lang['notifyConsoleLowBattery'] = "Die Batterie der Konsole ist schwach";
$lang['notifyStationLowBattery'] = "Die Batterie der Station ist schwach";
$lang['notifyUVIndex']           = "UV-Index";
$lang['notifyUVExposure']        = "Reduzieren Sie die Sonneneinstrahlung";
$lang['notifyHeatExaustion']     = "Hitzeerschöpfung";
$lang['notifyExtremeWind']       = "Warnung vor extremem Wind";
$lang['notifyGustUpTo']          = "Böen bis zu";
$lang['notifySeekShelter']       = "<notifyred><b>sofort</b></notifyred> Schutz suchen";
$lang['notifyHighWindWarning']   = "Warnung vor starkem Wind";
$lang['notifySustainedAvg']      = "Anhaltender Durchschn.";
$lang['notifyWindAdvisory']      = "Windmeldung";
$lang['notifyFreezing']          = "Unter Gefrierpunkt";
$lang['StationTime']             = 'Uhrzeit der Station';
$lang['MoonPhase']               = 'Mondphase';
$lang['LocalNowcast']            = 'Lokale Vorhersage';
$lang['Lightning']               = 'Blitz';
$lang['AirQuality']              = 'Luftqualität';
$lang['UVSolar']                 = 'UV & Sonne';
$lang['Webcam']                  = 'Webcam';
$lang['SpaceWeather']            = 'Weltraumwetter';
$lang['NwsRadar']                = 'NWS Radar';
$lang['TemperatureYear']         = 'Temperatur Jahr';
$lang['WindGustYear']            = 'Windböen Jahr';
$lang['RainfallTotals']          = 'Niederschlagsmengen';
$lang['WeatherAdvisory']         = 'Wetterwarnung';

# Synced from lang.en.php (keys added for newer modules)
$lang['Southdir']                 = 'Genau <span> Süd</span>';
$lang['South']                    = 'Süd';
$lang['Active']                   = 'Aktiv';
$lang['AIndex']                   = 'A-Index';
$lang['AuroraAGuide']             = 'ist ein Indikator für möglicherweise verbesserte VHF-Funkverbindungen über Entfernungen von 1000 Meilen oder mehr. Bei starker Sonnenaktivität ermöglichen Frequenzen von 28 bis 433 MHz oder höher Funkverbindungen in mittleren bis hohen Breiten.';
$lang['AuroraHamGuide']           = 'Aurora-Verbindungen werden von VHF-Funkamateuren genutzt. Aurora-Scatter-Ausbreitung ermöglicht Funkamateurverbindungen.';
$lang['AuroraHamGuide2']          = 'Aurora-Scatter-Verbindungen mit speziellen Betriebstechniken erlauben Entfernungen von 2000 km oder mehr auf 28 MHz/50 MHz/144 MHz/433 MHz.';
$lang['AuroraKpGuide']            = 'ist ein guter Indikator für die Sichtbarkeit von Polarlichtern. Je höher der Kp-Index, desto höher die Sichtwahrscheinlichkeit. Der geschätzte planetare 3-Stunden-Kp-Index stammt von bodengestützten Magnetometern.';
$lang['AuroraNowcast']            = 'Polarlicht-Kurzfrist';
$lang['DataProvidedBy']           = 'Daten bereitgestellt von';
$lang['EstimatedZHR']             = 'Geschätzte ZHR';
$lang['Geo']                      = 'Geo';
$lang['GeomagneticMajorStorm']    = 'Starker geomagnetischer Sturm';
$lang['GeomagneticSevereStorm']   = 'Schwerer geomagnetischer Sturm';
$lang['GeomagneticStorm']         = 'Geomagnetischer Sturm';
$lang['Guide']                    = 'Leitfaden';
$lang['Information']              = 'Information';
$lang['Kp']                       = 'Kp';
$lang['KpGeomagneticForecast']    = 'Kp geomagnetische Vorhersage';
$lang['KpIndex']                  = 'Kp-Index';
$lang['KpPlanetaryIndex']         = 'KP-PLANETARER INDEX';
$lang['LastUpdated']              = 'Zuletzt aktualisiert';
$lang['LatestObserved']           = 'Zuletzt beobachtet';
$lang['MajorStorm']               = 'Starker Sturm';
$lang['MeteorShower']             = 'Meteorstrom';
$lang['MeteorShowers']            = 'Meteorströme';
$lang['MinorGeomagneticStorm']    = 'Schwacher geomagnetischer Sturm';
$lang['MinorStorm']               = 'Schwacher Sturm';
$lang['NoMeteorShower']           = 'Keine Meteorströme';
$lang['NorthernLights']           = 'Nordlichter';
$lang['NotActive']                = 'Nicht aktiv';
$lang['Peaks']                    = 'Maximum';
$lang['PeakViewingNow']           = 'Jetzt beste Sicht';
$lang['Quiet']                    = 'Ruhig';
$lang['QuietNoGeomagneticStorm']  = 'Ruhig, kein geomagnetischer Sturm';
$lang['QuietNoStorm']             = 'Ruhig, keine Stürme';
$lang['Radio']                    = 'Funk';
$lang['RadioAurora']              = 'Funk-Aurora';
$lang['RadioHamGuide']            = 'Funkamateur-Leitfaden';
$lang['SevereStorm']              = 'Schwerer Sturm';
$lang['Solar']                    = 'Solar';
$lang['WeakGeomagneticStorm']     = 'Schwacher geomagnetischer Sturm';
$lang['WeakStorm']                = 'Schwacher Sturm';
$lang['ViewLine']                 = 'Sichtlinie';
$lang['Forecast3Day']             = '3-Tage-Vorhersage';
$lang['HemPower']                 = 'Hem.-Leistung';
$lang['Ovation']                  = 'Ovation';
$lang['ForecastUnavailable']      = 'Vorhersage nicht verfügbar — stündliche Aktualisierung.';
$lang['ApIndex']                  = 'Ap-Index';
$lang['Ap']                       = 'Ap';
$lang['Type']                     = 'Typ';
$lang['Date']                     = 'Datum';
$lang['GeomagneticActivityProbabilities'] = 'Wahrscheinlichkeiten geomagnetischer Aktivität';
$lang['Level']                    = 'Stufe';
$lang['KpIndexForecast']          = 'Kp-Index-Vorhersage (UT)';
$lang['Period']                   = 'Zeitraum';
$lang['Issued']                   = 'Herausgegeben';
$lang['HemispherePowerUnavailable'] = 'Hemisphärenleistung nicht verfügbar — Aktualisierung alle 5 Min.';
$lang['NorthernHemisphereOvation'] = 'Nordhalbkugel — Ovation';
$lang['SouthernHemisphereOvation'] = 'Südhalbkugel — Ovation';
$lang['ObservationTimeUTC']       = 'Beobachtungszeit UTC';
$lang['NorthGW']                  = 'Nord (GW)';
$lang['SouthGW']                  = 'Süd (GW)';
$lang['G1PlusStorm']              = 'G1+ Sturm';
$lang['Nowcast6h']                = '6-Std.-Kurzfrist';
$lang['Snow6h']                   = 'Schnee 6 Std.';
$lang['Rain6h']                   = 'Regen 6 Std.';
$lang['Dry']                      = 'Trocken';
$lang['Slight']                   = 'Gering';
$lang['Chance']                   = 'Möglich';
$lang['Likely']                   = 'Wahrscheinlich';
$lang['NoHyperlocalNowcast']      = 'Keine lokale Kurzfristvorhersage verfügbar.';
$lang['NwsWeatherAlerts']         = 'NWS-Wetterwarnungen';
$lang['ActiveNwsWeatherAlerts']   = 'Aktive NWS-Wetterwarnungen';
$lang['Fetched']                  = 'Abgerufen';
$lang['NoActiveWeatherAlerts']    = 'Derzeit keine aktiven Wetterwarnungen.';
$lang['Headline']                 = 'Schlagzeile';
$lang['Effective']                = 'Gültig ab';
$lang['Expires']                  = 'Läuft ab';
$lang['Unknown']                  = 'Unbekannt';
$lang['AreaForecastDiscussion']   = 'Regionale Vorhersagediskussion';
$lang['KeyMessages']              = 'Kernaussagen';
$lang['ShortTerm']                = 'Kurzfristig';
$lang['LongTerm']                 = 'Langfristig';
$lang['OutlookTrend']             = 'Trend';
$lang['ShortTermDiscussion']      = 'Kurzfristige Diskussion';
$lang['LongTermDiscussion']       = 'Langfristige Diskussion';
$lang['NoSourceText']             = 'Kein Quelltext verfügbar.';
$lang['NoSummary']                = 'Noch keine Zusammenfassung';
$lang['GeneratedYet']             = 'erstellt.';
$lang['CheckBackgroundScript']    = 'Ausführung des Hintergrundskripts prüfen.';
$lang['ConfirmOllamaStatus']      = '<span class="hl-orange">Ollama-Status</span> auf dem Pi prüfen.';
$lang['Strikes']                  = 'Einschläge';
$lang['Last3Hrs']                 = 'Letzte 3 Std.';
$lang['Distance']                 = 'Entfernung';
$lang['LastStrike']               = 'Letzter Einschlag';
$lang['LastDistanceAt']           = 'Letzte Entfernung um';
$lang['AllTimeStrikeTotal']       = 'Einschläge gesamt';
$lang['Miles']                    = 'Meilen';
$lang['Km']                       = 'km';
?>
