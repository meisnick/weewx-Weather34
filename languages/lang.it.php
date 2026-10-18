<?php
/* 
-----------------
Language Translation File for HOMEWEATHERSTATION Template
Language: Italian
Translated by: Antonio Angelotti - meteopomarico.it - 23.04.2017
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
setlocale(LC_TIME, "it_IT.UTF-8");
$lang = array();
// Menu
$lang['Settings'] = 'Impostazioni';
$lang['Layout'] = 'Cambia Tema';
$lang['Lighttheme'] = 'Tema Light';
$lang['Darktheme'] = 'Tema Dark';
$lang['Nonmetric'] = 'US (F) ';
$lang['Metric'] = 'Metrico (C)';
$lang['UKmetric'] = 'UK (MPH - Metrico) ';
$lang['Scandinavia'] = 'Scandinavia(M/S)';
$lang['Worldwideearthquakes'] = 'Terremoti Mondo';
$lang['Toggle'] = 'Schermo intero';
$lang['Contactinfo'] = 'Stazione & Informazioni';
$lang['Templateinfo'] = 'Contributori';
$lang['language'] = 'Seleziona la lingua';
$lang['Weatherstationinfo'] = 'Info Stazione Meteo';
$lang['Webdesigninfo'] = 'Info Template';
$lang['Contact'] = 'Contatti';
//days
$lang['Monday'] = 'Monday';
$lang['Tuesday'] = 'Tuesday';
$lang['Wednesday'] = 'Wednesday';
$lang['Thursday'] = 'Thursday';
$lang['Friday'] = 'Friday ';
$lang['Saturday'] = 'Saturday';
$lang['Sunday'] = 'Sunday';
//months
$lang['January'] = 'January';
$lang['Febuary'] = 'Febuary';
$lang['March'] = 'March';
$lang['April'] = 'April';
$lang['May'] = 'May';
$lang['June'] = 'June';
$lang['July'] = 'July';
$lang['August'] = 'August';
$lang['September'] = 'September';
$lang['October'] = 'October';
$lang['November'] = 'November';
$lang['December'] = 'December';
//temperature
$lang['Temperature'] = 'Temperatura';
$lang['Feelslike'] = 'Percepita';
$lang['Humidity'] = 'Umidità';
$lang['Dewpoint'] = 'Punto di rugiada';
$lang['Trend'] = 'trend ';
$lang['Heatindex'] = 'Indice di calore';
$lang['Apparent'] = 'Apparente';
$lang['Windchill'] = 'Percepita ';
$lang['IndoorTemp'] = 'Temperatura Interna';
$lang['Tempfactors'] = 'Fattore Temp';
$lang['Nocautions'] = 'Nessuna Cautela';
$lang['Wetbulb'] = 'Wet Bulb';
$lang['dry'] = '& Dry';
$lang['verydry'] = '& Very Dry';
//new feature temperature feels
$lang['FreezingCold'] = 'Freddo Glaciale';
$lang['FeelingVeryCold'] = 'Si Sente molto freddo';
$lang['FeelingCold'] = 'Si Sente Freddo';
$lang['FeelingCool'] = 'Si Sente Fresco';
$lang['FeelingComfortable'] = 'Si Sente a proprio agio ';
$lang['FeelingWarm'] = 'Si Sente Caldo';
$lang['FeelingHot'] = 'Si Sente molto Caldo';
$lang['FeelingUncomfortable'] = 'Si Sente a Disagio';
$lang['FeelingVeryHot'] = 'Si Sente molto Caldo';
$lang['FeelingExtremelyHot'] = 'Si Sente Estremamente Caldo';
//wind
$lang['Windspeed'] = 'Velocità vento';
$lang['Gust'] = 'Raffica';
$lang['Direction'] = 'Direzione';
$lang['Gusting'] = 'Raffica';
$lang['Blowing'] = 'Soffia';
$lang['Wind'] = 'Velocità';
$lang['Wind Run'] = 'Velocità Run';
// Wind phrases for Beaufort scale for windspeed area
$lang['Calm'] = 'Calma';
$lang['Lightair'] = 'Bava di Vento';
$lang['Lightbreeze'] = 'Brezza Leggera';
$lang['Gentelbreeze'] = 'Brezza tesa';
$lang['Moderatebreeze'] = 'Vento moderato';
$lang['Freshbreeze'] = 'Vento teso';
$lang['Strongbreeze'] = 'Vento fresco';
$lang['Neargale'] = 'Vicino a burrasca';
$lang['Galeforce'] = 'Gale Burrasca';
$lang['Stronggale'] = 'Burrasca forte';
$lang['Storm'] = 'Tempesta';
$lang['Violentstorm'] = 'Tempesta violenta';
$lang['Hurricane'] = 'Uragano';
// Wind phrases from Beaufort scale for current conditions area
$lang['CalmConditions'] = 'Condizioni di Calma';
$lang['LightBreezeattimes'] = 'Bava di Vento ';
$lang['MildBreezeattimes'] = 'Brezza Leggera ';
$lang['ModerateBreezeattimes'] = 'Brezza Tesa';
$lang['FreshBreezeattimes'] = 'Vento Moderato';
$lang['StrongBreezeattimes'] = 'Vento Teso';
$lang['NearGaleattimes'] = 'Vento Fresco';
$lang['GaleForceattimes'] = 'Vento Forte';
$lang['StrongGaleattimes'] = 'Burrasca';
$lang['StormConditions'] = 'Tempesta';
$lang['ViolentStormConditions'] = 'Tempesta Violenta';
$lang['HurricaneConditions'] = 'Uragano';
$lang['Avg'] = ' Avg: ';
//wind direction compass
$lang['Northdir'] = 'Da Nord
';
$lang['NNEdir'] = 'Nord Nord
Est';
$lang['NEdir'] = 'Nord Est
';
$lang['ENEdir'] = 'Est Nord
Est';
$lang['Eastdir'] = "Da Est
";
$lang['ESEdir'] = 'Est Sud
Est';
$lang['SEdir'] = 'Sud Est';
$lang['SSEdir'] = 'Sud Sud
Est';
$lang['Southdir'] = 'Da Sud';
$lang['SSWdir'] = 'Sud Sud
Ovest';
$lang['SWdir'] = 'Sud Ovest';
$lang['WSWdir'] = 'Ovest Sud
Ovest';
$lang['Westdir'] = 'Da Ovest';
$lang['WNWdir'] = 'Ovest Nord
Ovest';
$lang['NWdir'] = 'Nord Ovest';
$lang['NWNdir'] = 'Nord Nord
Ovest';
//wind direction avg
$lang['North'] = 'Nord';
$lang['NNE'] = 'NNE';
$lang['NE'] = 'NE';
$lang['ENE'] = 'ENE';
$lang['East'] = 'Est ';
$lang['ESE'] = 'ESE';
$lang['SE'] = 'SE';
$lang['SSE'] = 'SSE';
$lang['South'] = 'Sud';
$lang['SSW'] = 'SSO';
$lang['SW'] = 'SO';
$lang['WSW'] = 'OSO';
$lang['West'] = 'Ovest';
$lang['WNW'] = 'ONO';
$lang['NW'] = 'NO';
$lang['NWN'] = 'NNO';
//rain
$lang['raintoday'] = 'Precipitazioni Oggi';
$lang['Rate'] = 'Tasso';
$lang['Rainfall'] = 'Precipitazioni';
$lang['Precip'] = 'precip'; // must be short name do not use full precipitation !!!! ///
$lang['Rain'] = 'Pioggia';
$lang['Heavyrain'] = 'Pioggia forte';
$lang['Flooding'] = 'Possibili Inondazioni';
$lang['Rainbow'] = 'Rainbow';
$lang['Windy'] = 'Windy';
//sun -moon-daylight-darkness
$lang['Sun'] = 'Sole';
$lang['Moon'] = 'Luna';
$lang['Sunrise'] = 'Alba';
$lang['Sunset'] = 'Tramonto';
$lang['Moonrise'] = 'Sorgere della luna ';
$lang['Moonset'] = 'Tramonto Luna';
$lang['Night'] = 'Notte ';
$lang['Day'] = 'Giorno';
$lang['Nextnewmoon'] = 'Luna nuova';
$lang['Nextfullmoon'] = 'Luna Piena';
$lang['Luminance'] = 'Luminanza';
$lang['Moonphase'] = 'Fase Lunare';
$lang['Estimated'] = 'Stimato';
$lang['Daylight'] = 'Luce';
$lang['Darkness'] = 'Oscurità';
$lang['Daysold'] = 'Vecchia';
$lang['Belowhorizon'] = 'sotto
orizzonte';
$lang['Mintill'] = '
min fino';
$lang['Till'] = 'a ';
$lang['Minago'] = ' min fa';
$lang['Hrs'] = ' ore';
$lang['Min'] = ' min';
$lang['TotalDarkness'] = 'Ore notte';
$lang['TotalDaylight'] = 'Ore giorno';
$lang['Below'] = 'è sotto orizzonte';
$lang['Newmoon'] = 'Luna Nuova';
$lang['Waxingcrescent'] = 'Luna Crescente';
$lang['Firstquarter'] = 'Primo Quarto';
$lang['Waxinggibbous'] = 'Gibbosa Crescente';
$lang['Fullmoon'] = 'Luna Piena';
$lang['Waninggibbous'] = 'Gibbosa Calante';
$lang['Lastquarter'] = 'Ultimo Quarto';
$lang['Waningcrescent'] = 'Luna Calante';
//trends
$lang['Falling'] = 'Diminuz';
$lang['Rising'] = 'Aumento';
$lang['Steady'] = 'Costante';
$lang['Rapidly'] = 'Rapidamente';
$lang['Temp'] = 'Temp';
//Solar-UV
//uv
$lang['Nocaution'] = 'Nessuna cautela necessaria';
$lang['Wearsunglasses'] = 'Indossare occhiali da sole in giornate luminose';
$lang['Stayinshade'] = 'Rimani all ombra vicino mezzogiorno quando il sole è più forte';
$lang['Reducetime'] = 'Ridurre il tempo al sole tra le 10am-4pm ';
$lang['Minimize'] = 'Ridurre al minimo esposizione al sole tra le 10am-4pm ';
$lang['Trytoavoid'] = 'Cercate di evitare esposizione al sole tra le 10am-4pm ';
//solar
$lang['Poor'] = 'Scarsa
Radiazione';
$lang['Low'] = 'Bassa
Radiazione';
$lang['Moderate'] = 'Moderata
Radiazione';
$lang['Good'] = 'Forte
Radiazione';
$lang['Solarradiation'] = 'Radiazione solare';
//current sky
$lang['Currentsky'] = 'Condizioni Attuali';
$lang['Currently'] = 'Attualmente';
$lang['Cloudcover'] = 'Coperto dalle Nuvole';
//Notifications
$lang['Nocurrentalert'] = 'Nessuna allerta';
$lang['Windalert'] = 'Raffiche di vento';
$lang['Tempalert'] = 'Alta temperatura';
$lang['Heatindexalert'] = 'Allerta Indice di calore';
$lang['Windchillalert'] = 'Allerta Temperatura';
$lang['Dewpointalert'] = 'disagio Umidità';
$lang['Dewpointcolderalert'] = 'Allerta Punto di rugiada';
$lang['Feelslikecolderalert'] = 'Si sente più freddo';
$lang['Feelslikewarmeralert'] = 'Si sente più caldo';
$lang['Rainratealert'] = 'per/hr Precipitazione ';
//Earthquake Notifications
$lang['Regional'] = 'Terremoti Regionali';
$lang['Significant'] = 'Significativi';
$lang['Nosignificant'] = 'Nessun Terremoto Significativo';
//Main page
$lang['Barometer'] = 'Barometro';
$lang['UVSOLAR'] = 'UV | Dati Solari';
$lang['Earthquake'] = 'Dati Terremoto';
$lang['Daynight'] = 'Info Giorno & Notte';
$lang['SunPosition'] = 'Sun Position';
$lang['Location'] = 'Posizione ';
$lang['Hardware'] = 'Hardware';
$lang['Rainfalltoday'] = 'Pioggia Giornaliera';
$lang['Windspeed'] = 'Vento';
$lang['Winddirection'] = 'La direzione del vento';
$lang['Measured'] = 'Giorno Misurato';
$lang['Forecast'] = 'Previsione';
$lang['Forecastahead'] = 'Previsione 10 Giorni';
$lang['Forecastsummary'] = 'Previsione Sommaria';
$lang['WindGust'] = 'Raffica';
$lang['Hourlyforecast'] = 'Previsione Oraria';
$lang['Significantearthquake'] = 'Terremoto Significativi';
$lang['Regionalearthquake'] = 'Terremoto Regionali';
$lang['Average'] = 'Media';
$lang['Notifications'] = 'Notifiche di Allerta';
$lang['Indoor'] = 'Interno';
$lang['Today'] = 'Oggi';
$lang['Tonight'] = 'Notte';
$lang['Tomorrow'] = 'Domani';
$lang['Tomorrownight'] = 'Domani notte';
$lang['Updated'] = 'Aggiornato';
$lang['Meteor'] = 'Info Sciame meteorico';
$lang['Firerisk'] = 'Rischio Incendio';
$lang['Localtime'] = 'Ora Locale';
$lang['Nometeor'] = 'Nessun Sciame meteorico';
$lang['LiveWebCam'] = 'WebCam';
$lang['Online'] = 'Online';
$lang['Offline'] = 'Offline';
$lang['Weatherstation'] = 'Weather Station';
$lang['Cloudbase'] = 'Cloudbase';
$lang['uvalert'] = 'Caution High UVINDEX';
$lang['Max'] = 'Max';
$lang['Min'] = 'Min';
//earthquake TOP MODULE 10 July 2017
$lang['MicroE'] = 'Micro Earthquake';
$lang['MinorE'] = 'Minor Earthquake';
$lang['LightE'] = 'Light Earthquake';
$lang['ModerateE'] = 'Moderate Earthquake';
$lang['StrongE'] = 'Strong Earthquake';
$lang['MajorE'] = 'Major Earthquake';
$lang['GreatE'] = 'Great Earthquake';
$lang['RegionalE'] = 'Regional';
$lang['Conditions'] = 'Condizioni';
$lang['Cloudbase Height'] = 'Altezza Nuvole';
$lang['Station'] = 'Stazione';
$lang['Detailed Forecast'] = 'Dettagli Previsione';
$lang['Summary Outlook'] = 'Riepilogo Previsione';
//Air Quality
$lang['Hazordous'] = 'Hazardous Conditions';
$lang['VeryUnhealthy'] = 'Very Unhealthy';
$lang['Unhealthy'] = 'Unhealthy Air Quality';
$lang['UnhealthyFS'] = 'Unhealthy For Some';
$lang['Moderate'] = 'Moderate Air Quality ';
$lang['Good'] = 'Good Air Quality ';
#notification additions
$lang['notifyTitle'] = 'Notifiche';
$lang['notifyAlert'] = "Alert";
$lang['notifyLowBatteryAlert'] = "Low Battery Alert";
$lang['notifyConsoleLowBattery'] = "Console's battery is low";
$lang['notifyStationLowBattery'] = "Station's battery is low";
$lang['notifyUVIndex'] = "UV-Index Caution";
$lang['notifyUVExposure'] = "Reduce Sun Exposure";
$lang['notifyHeatExaustion'] = "Heat Exhaustion";
$lang['notifyExtremeWind'] = "Extreme Wind Warning";
$lang['notifyGustUpTo'] = "Gusts up to";
$lang['notifySeekShelter'] = "Seek shelter immediately";
$lang['notifyHighWindWarning'] = "High Wind Warning";
$lang['notifySustainedAvg'] = "Sustained avg";
$lang['notifyWindAdvisory'] = "Wind Advisory";
$lang['notifyFreezing'] = "Below Freezing";
$lang['StationTime'] = 'Ora della stazione';
$lang['MoonPhase'] = 'Fase lunare';
$lang['LocalNowcast'] = 'Previsione locale';
$lang['Lightning'] = 'Fulmine';
$lang['AirQuality'] = 'Qualità dell\'aria';
$lang['UVSolar'] = 'UV e solare';
$lang['Webcam'] = 'Webcam';
$lang['SpaceWeather'] = 'Meteo spaziale';
$lang['NwsRadar'] = 'NWS Radar';
$lang['TemperatureYear'] = 'Temperatura annuale';
$lang['WindGustYear'] = 'Raffiche di vento annuali';
$lang['RainfallTotals'] = 'Totali precipitazioni';
$lang['WeatherAdvisory'] = 'Avviso meteorologico';

# Synced from lang.en.php (keys added for newer modules)
$lang['Northdir']                 = 'Da <span>Nord<br></span>';
$lang['NNEdir']                   = 'Nord Nord<br><span>Est</span>';
$lang['NEdir']                    = 'Nord <span> Est<br></span>';
$lang['ENEdir']                   = 'Est Nord<br><span>Est</span>';
$lang['Eastdir']                  = 'Da <span> Est<br></span>';
$lang['ESEdir']                   = 'Est Sud<br><span>Est</span>';
$lang['SSEdir']                   = 'Sud Sud<br><span>Est</span>';
$lang['SSWdir']                   = 'Sud Sud<br><span>Ovest</span>';
$lang['WSWdir']                   = 'Ovest Sud<br><span>Ovest</span>';
$lang['WNWdir']                   = 'Ovest Nord<br><span>Ovest</span>';
$lang['NWNdir']                   = 'Nord Nord<br><span>Ovest</span>';
$lang['Belowhorizon']             = 'sotto<br>l’orizzonte';
$lang['Mintill']                  = ' min a';
$lang['Poor']                     = 'Radiazione<color> <br>scarsa</color>';
$lang['Low']                      = 'Radiazione <br><color>bassa</color>';
$lang['Active']                   = 'Attivo';
$lang['AIndex']                   = 'Indice A';
$lang['AuroraAGuide']             = 'è un indicatore di possibili comunicazioni radio VHF migliorate a distanze di 1000 miglia o più. Durante una forte attività solare, le frequenze da 28 a 433 MHz o superiori consentono comunicazioni radio a medie e alte latitudini.';
$lang['AuroraHamGuide']           = 'Le comunicazioni via aurora possono essere usate dai radioamatori VHF. La propagazione per diffusione aurorale consente collegamenti tra radioamatori.';
$lang['AuroraHamGuide2']          = 'La diffusione aurorale, con tecniche operative specializzate, consente collegamenti fino a 2000 km o più su 28 MHz/50 MHz/144 MHz/433 MHz.';
$lang['AuroraKpGuide']            = 'è un buon indicatore per osservare l’aurora boreale. Più alto è l’indice Kp, maggiore è la probabilità di osservarla. L’indice Kp planetario stimato a 3 ore proviene da magnetometri a terra.';
$lang['AuroraNowcast']            = 'Previsione immediata aurora';
$lang['DataProvidedBy']           = 'Dati forniti da';
$lang['EstimatedZHR']             = 'THZ stimato';
$lang['Geo']                      = 'Geo';
$lang['GeomagneticMajorStorm']    = 'Tempesta geomagnetica forte';
$lang['GeomagneticSevereStorm']   = 'Tempesta geomagnetica grave';
$lang['GeomagneticStorm']         = 'Tempesta geomagnetica';
$lang['Guide']                    = 'Guida';
$lang['Information']              = 'Informazioni';
$lang['Kp']                       = 'Kp';
$lang['KpGeomagneticForecast']    = 'Previsione geomagnetica Kp';
$lang['KpIndex']                  = 'Indice Kp';
$lang['KpPlanetaryIndex']         = 'INDICE PLANETARIO KP';
$lang['LastUpdated']              = 'Ultimo aggiornamento';
$lang['LatestObserved']           = 'Ultima osservazione';
$lang['MajorStorm']               = 'Tempesta forte';
$lang['MeteorShower']             = 'Sciame meteorico';
$lang['MeteorShowers']            = 'Sciami meteorici';
$lang['MinorGeomagneticStorm']    = 'Tempesta geomagnetica minore';
$lang['MinorStorm']               = 'Tempesta minore';
$lang['NoMeteorShower']           = 'Nessuno sciame meteorico';
$lang['NorthernLights']           = 'Aurora boreale';
$lang['NotActive']                = 'Non attivo';
$lang['Peaks']                    = 'Picco';
$lang['PeakViewingNow']           = 'Visibilità massima ora';
$lang['Quiet']                    = 'Calmo';
$lang['QuietNoGeomagneticStorm']  = 'Calmo, nessuna tempesta geomagnetica';
$lang['QuietNoStorm']             = 'Calmo, nessuna tempesta';
$lang['Radio']                    = 'Radio';
$lang['RadioAurora']              = 'Radio aurora';
$lang['RadioHamGuide']            = 'Guida radioamatori';
$lang['SevereStorm']              = 'Tempesta grave';
$lang['Solar']                    = 'Solare';
$lang['WeakGeomagneticStorm']     = 'Tempesta geomagnetica debole';
$lang['WeakStorm']                = 'Tempesta debole';
$lang['ViewLine']                 = 'Linea di visibilità';
$lang['Forecast3Day']             = 'Previsione 3 giorni';
$lang['HemPower']                 = 'Pot. emisf.';
$lang['Ovation']                  = 'Ovation';
$lang['ForecastUnavailable']      = 'Previsione non disponibile — aggiornamento ogni ora.';
$lang['ApIndex']                  = 'Indice Ap';
$lang['Ap']                       = 'Ap';
$lang['Type']                     = 'Tipo';
$lang['Date']                     = 'Data';
$lang['GeomagneticActivityProbabilities'] = 'Probabilità di attività geomagnetica';
$lang['Level']                    = 'Livello';
$lang['KpIndexForecast']          = 'Previsione indice Kp (UT)';
$lang['Period']                   = 'Periodo';
$lang['Issued']                   = 'Emesso';
$lang['HemispherePowerUnavailable'] = 'Potenza emisferica non disponibile — aggiornamento ogni 5 min.';
$lang['NorthernHemisphereOvation'] = 'Emisfero nord — Ovation';
$lang['SouthernHemisphereOvation'] = 'Emisfero sud — Ovation';
$lang['ObservationTimeUTC']       = 'Ora osservazione UTC';
$lang['NorthGW']                  = 'Nord (GW)';
$lang['SouthGW']                  = 'Sud (GW)';
$lang['G1PlusStorm']              = 'Tempesta G1+';
$lang['Nowcast6h']                = 'Previsione immediata 6 h';
$lang['Snow6h']                   = 'Neve 6 h';
$lang['Rain6h']                   = 'Pioggia 6 h';
$lang['Dry']                      = 'Asciutto';
$lang['Slight']                   = 'Lieve';
$lang['Chance']                   = 'Possibile';
$lang['Likely']                   = 'Probabile';
$lang['NoHyperlocalNowcast']      = 'Nessuna previsione immediata locale disponibile.';
$lang['NwsWeatherAlerts']         = 'Allerte meteo NWS';
$lang['ActiveNwsWeatherAlerts']   = 'Allerte meteo NWS attive';
$lang['Fetched']                  = 'Recuperato';
$lang['NoActiveWeatherAlerts']    = 'Nessuna allerta meteo attiva al momento.';
$lang['Headline']                 = 'Titolo';
$lang['Effective']                = 'In vigore';
$lang['Expires']                  = 'Scade';
$lang['Unknown']                  = 'Sconosciuto';
$lang['AreaForecastDiscussion']   = 'Discussione previsionale regionale';
$lang['KeyMessages']              = 'Messaggi chiave';
$lang['ShortTerm']                = 'Breve termine';
$lang['LongTerm']                 = 'Lungo termine';
$lang['OutlookTrend']             = 'Tendenza';
$lang['ShortTermDiscussion']      = 'Discussione a breve termine';
$lang['LongTermDiscussion']       = 'Discussione a lungo termine';
$lang['NoSourceText']             = 'Nessun testo sorgente disponibile.';
$lang['NoSummary']                = 'Nessun riepilogo';
$lang['GeneratedYet']             = 'ancora generato.';
$lang['CheckBackgroundScript']    = 'Verificare l’esecuzione dello script in background.';
$lang['ConfirmOllamaStatus']      = 'Verificare lo <span class="hl-orange">stato di Ollama</span> sul Pi.';
$lang['Strikes']                  = 'Fulmini';
$lang['Last3Hrs']                 = 'Ultime 3 ore';
$lang['Distance']                 = 'Distanza';
$lang['LastStrike']               = 'Ultimo fulmine';
$lang['LastDistanceAt']           = 'Ultima distanza alle';
$lang['AllTimeStrikeTotal']       = 'Totale fulmini';
$lang['Miles']                    = 'miglia';
$lang['Km']                       = 'km';
?>
