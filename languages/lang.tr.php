<?php
/* 
-----------------
Language Translation File for HOMEWEATHERSTATION Template
Language: Turkish
Translated by: Brian Underdown / Erim Mehmet Eren
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
setlocale(LC_TIME, "tr_TR.UTF-8");
$lang                           = array();
// Menu
$lang['Settings']               = 'Ayarlar';
$lang['Layout']                 = 'Anahtar Düzenı';
$lang['Lighttheme']             = 'Işık Teması';
$lang['Darktheme']              = 'Kara Teması';
$lang['Nonmetric']              = 'US (F) ';
$lang['Metric']                 = 'Metrik (C)';
$lang['UKmetric']               = 'UK (MPH - Metrik) ';
$lang['Scandinavia']            = 'İskandinavya(M/S)';
$lang['Worldwideearthquakes']   = 'Dunya Depremleri';
$lang['Toggle']                 = 'Toggle Fullscreen ';
$lang['Contactinfo']            = 'İstasyon ve İletişim Bilgileri';
$lang['Templateinfo']           = 'Contributors';
$lang['language']               = 'Dil Seçin';
$lang['Weatherstationinfo']     = 'Hava Istasyon Bilgi';
$lang['Webdesigninfo']          = 'Web Sitesi Bilgi';
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
$lang['Temperature']            = 'Sıcaklık';
$lang['Feelslike']              = 'Hissedilen';
$lang['Humidity']               = 'Nem';
$lang['Dewpoint']               = 'Çiğ noktası';
$lang['Trend']                  = 'Gidişat';
$lang['Heatindex']              = 'Sıcaklık endeksi';
$lang['Apparent']               = 'Görünür';
$lang['Windchill']              = 'Rüzgar Sıcaklığı ';
$lang['IndoorTemp']             = 'İç Sıcaklık';
$lang['Tempfactors']            = 'Sıcaklık Faktörler';
$lang['Nocautions']             = 'Hayır Dikkat';
$lang['Wetbulb']                = 'Islak Ampul';
$lang['dry']                    = '& Kuru';
$lang['verydry']                = '& Çok kuru';
//new feature temperature feels
$lang['FreezingCold']           = 'Çok Çok Soğuk';
$lang['FeelingVeryCold']        = 'Duygu Çok Soğuk';
$lang['FeelingCold']            = 'Duygu Soğuk';
$lang['FeelingCool']            = 'Duygu Cool';
$lang['FeelingComfortable']     = 'Duygu Iyi ';
$lang['FeelingWarm']            = 'Duygu Hafif Sıcak';
$lang['FeelingHot']             = 'Duygu Sıcak';
$lang['FeelingUncomfortable']   = 'Duygu Rahatsız';
$lang['FeelingVeryHot']         = 'Duygu Çok Sıcak';
$lang['FeelingExtremelyHot']    = 'Duygu Çok Çok Sıcak';
//wind
$lang['Windspeed']              = 'Rüzgar hızı';
$lang['Gust']                   = 'Rüzgar';
$lang['Direction']              = 'Yön';
$lang['Gusting']                = 'Max Rüzgar ';
$lang['Blowing']                = 'üfleme ';
$lang['Blowing']                = 'Rüzgar';
$lang['Wind Run']               = 'Rüzgar Run';
// Wind phrases for Beaufort scale for windspeed area
$lang['Calm']                   = 'Sakin';
$lang['Lightair']               = 'Çok hafif Rüzgarlı';
$lang['Lightbreeze']            = 'Orta Rüzgârlı';
$lang['Gentelbreeze']           = 'Orta Rüzgârlı';
$lang['Moderatebreeze']         = 'Orta Rüzgârlı';
$lang['Freshbreeze']            = 'Sert Rüzgârlı';
$lang['Strongbreeze']           = 'Kuvvetli Rüzgarlı';
$lang['Neargale']               = 'Fırtınamsı Rüzgar';
$lang['Galeforce']              = 'Fırtına ';
$lang['Stronggale']             = 'Kuvvetli Fırtına';
$lang['Storm']                  = 'Tam Fırtına';
$lang['Violentstorm']           = 'şiddetli Fırtına';
$lang['Hurricane']              = 'Kasırga';
// Wind phrases from Beaufort scale for current conditions area
$lang['CalmConditions']         = 'Sakin';
$lang['LightBreezeattimes']     = 'Çok Hafif Rüzgarlı';
$lang['MildBreezeattimes']      = 'Orta Rüzgârlı';
$lang['ModerateBreezeattimes']  = 'Orta Rüzgârlı';
$lang['FreshBreezeattimes']     = 'Sert Rüzgârlı';
$lang['StrongBreezeattimes']    = 'Kuvvetli Rüzgarlı';
$lang['NearGaleattimes']        = 'Fırtınamsı Rüzgar';
$lang['GaleForceattimes']       = 'Fırtına';
$lang['StrongGaleattimes']      = 'Kuvvetli Fırtına ';
$lang['StormConditions']        = 'Tam Fırtına';
$lang['ViolentStormConditions'] = 'şiddetli Fırtına';
$lang['HurricaneConditions']    = 'Kasırga';
$lang['Avg']                    = '<span2> Avg: </span2>';
//wind direction compass
$lang['Northdir']               = '<span>Kuzey<br></span>';
$lang['NNEdir']                 = 'Kuzey Kuzey <br><span>Doğu</span>';
$lang['NEdir']                  = 'Kuzey <span> Doğu<br></span>';
$lang['ENEdir']                 = 'Doğu Kuzey<br><span>Doğu</span>';
$lang['Eastdir']                = "<span> Doğu<br></span>";
$lang['ESEdir']                 = 'Doğu Güney<br><span>Doğu</span>';
$lang['SEdir']                  = 'Güney <span> Doğu</span>';
$lang['SSEdir']                 = 'Güney Güney<br><span>Doğu</span>';
$lang['Southdir']               = '<span> Güney</span>';
$lang['SSWdir']                 = 'Güney Güney<br><span>Batı</span>';
$lang['SWdir']                  = 'Güney <span> Batı</span>';
$lang['WSWdir']                 = 'Batı Güney<br><span>Batı</span>';
$lang['Westdir']                = '<span> Batı</span>';
$lang['WNWdir']                 = 'Batı Kuzey<br><span>Batı</span>';
$lang['NWdir']                  = 'Kuzey <span> Batı</span>';
$lang['NWNdir']                 = 'Kuzey Kuzey<br><span>Batı</span>';
//wind direction avg
$lang['North']                  = 'Kuzey';
$lang['NNE']                    = 'KKD';
$lang['NE']                     = 'KD';
$lang['ENE']                    = 'DKD';
$lang['East']                   = 'Doğu ';
$lang['ESE']                    = 'DGD';
$lang['SE']                     = 'GD';
$lang['SSE']                    = 'GGD';
$lang['South']                  = 'Güney';
$lang['SSW']                    = 'GGB';
$lang['SW']                     = 'GB';
$lang['WSW']                    = 'BGB';
$lang['West']                   = 'Batı';
$lang['WNW']                    = 'BKB';
$lang['NW']                     = 'KB';
$lang['NWN']                    = 'KKB';
//rain
$lang['raintoday']              = 'Bugün Yağmurlu';
$lang['Rate']                   = 'Oran';
$lang['Rainfall']               = 'Yağmur';
$lang['Precip']                 = 'Yağmur'; // must be short name do not use full precipatation !!!! ///
$lang['Rain']                   = 'Yağmur';
$lang['Heavyrain']              = 'Ağır Yağmur';
$lang['Flooding']               = 'Dikkat';
$lang['Rainbow']                = 'Gökkuşağı';
$lang['Windy']                  = 'Çok Ruzgar';
//sun -moon-daylight-darkness
$lang['Sun']                    = 'Güneş';
$lang['Moon']                   = 'Ay';
$lang['Sunrise']                = 'Güneşin doğuşu';
$lang['Sunset']                 = 'Güneşin batışı';
$lang['Moonrise']               = 'Ay doğuşu ';
$lang['Moonset']                = 'Ay batışı';
$lang['Night']                  = 'Gece ';
$lang['Day']                    = 'Gun';
$lang['Nextnewmoon']            = 'Yeni Ay';
$lang['Nextfullmoon']           = 'Tam Ay';
$lang['Luminance']              = '&nbsp;&nbsp;Parlaklık  ';
$lang['Moonphase']              = 'Ay evreleri';
$lang['Estimated']              = ' Tahmini';
$lang['Daylight']               = ' Aydınlık';
$lang['Darkness']               = ' Karanlık';
$lang['Daysold']                = 'Gun';
$lang['Belowhorizon']           = 'altında<br>ufuk';
$lang['Mintill']                = '<br>sonra';
$lang['Till']                   = 'Için ';
$lang['Minago']                 = 'son';
$lang['Hrs']                    = ' hrs';
$lang['Min']                    = ' min';
$lang['TotalDarkness']          = 'Toplam Karanlık';
$lang['TotalDaylight']          = 'Toplam Aydınlık';
$lang['Below']                  = 'ufuktan aşağıda';
$lang['Newmoon']                = 'Yeni Ay ';
$lang['Waxingcrescent']         = 'Waxing Crescent';
$lang['Firstquarter']           = 'İlk çeyrek';
$lang['Waxinggibbous']          = 'Waxing Gibbous';
$lang['Fullmoon']               = 'Tam Ay';
$lang['Waninggibbous']          = 'Waning Gibbous';
$lang['Lastquarter']            = 'Son çeyrek';
$lang['Waningcrescent']         = 'Waning Crescent';
//trends
$lang['Falling']                = ' düşüyor';
$lang['Rising']                 = ' yükseliyor';
$lang['Steady']                 = ' sabit';
$lang['Rapidly']                = ' Hızla';
$lang['Temp']                   = 'Temp';
//Solar-UV
//uv
$lang['Nocaution']              = 'Dikkat <color>Hayir</color>';
$lang['Wearsunglasses']         = 'Güneş Gözlüğü<color>takın</color>';
$lang['Stayinshade']            = '<color>Güneş</color> altında kalmaktan kaçının';
$lang['Reducetime']             = 'Saat 10 & 16 arasında <color>güneş</color> çıkmayın ';
$lang['Minimize']               = '<color>Kuvvetli</color> güneş 10 & 16 arası  ';
$lang['Trytoavoid']             = 'Öğlen <color>güneş</color> e çıkmayın. Sıcak çarpma riski ';
//solar
$lang['Poor']                   = 'Çok Zayıf<color> <br>Işın</color>';
$lang['Low']                    = 'Düşük<br><color>Işın</color>';
$lang['Moderate']               = 'Orta <br><color>Işın</color>';
$lang['Good']                   = 'İyi <br><color>Işın</color>';
$lang['Solarradiation']         = 'Güneş Işınları';
//current sky
$lang['Currentsky']             = 'Güncel Hava';
$lang['Currently']              = 'Güncel';
$lang['Cloudcover']             = 'Bulutu';
//Notifications
$lang['Nocurrentalert']         = 'Uyarı yok';
$lang['Windalert']              = 'Kuvvetli rüzgar uyarısı';
$lang['Tempalert']              = 'Yüksek sıcaklık';
$lang['Heatindexalert']         = 'Heat Index Dikkat ';
$lang['Windchillalert']         = 'Windchill Dikkat';
$lang['Dewpointalert']          = 'Bunaltıcı nem';
$lang['Dewpointcolderalert']    = 'Çiğ noktası Dikkat';
$lang['Feelslikecolderalert']   = 'Daha soğuk Dikkat';
$lang['Feelslikewarmeralert']   = 'Daha sıcak Dikkat';
$lang['Rainratealert']          = 'Saat<span> Yağmur ';
//Earthquake Notifications
$lang['Regional']               = 'Bölgesel Deprem';
$lang['Significant']            = 'Önemli Deprem';
$lang['Nosignificant']          = 'Deprem yok';
//Main page
$lang['Barometer']              = 'Barometre';
$lang['UVSOLAR']                = 'UV | Solar Data';
$lang['Earthquake']             = 'Deprem Data';
$lang['Daynight']               = 'Gun & Gece Bilgi';
$lang['SunPosition']            = 'Güneş';
$lang['Location']               = 'Yer ';
$lang['Hardware']               = 'Donanım';
$lang['Rainfalltoday']          = 'Toplam Yağmur';
$lang['Windspeed']              = 'Rüzgar hızı';
$lang['Winddirection']          = 'Yönü';
$lang['Measured']               = 'Bugün Toplam';
$lang['Forecast']               = 'HAVA DURUMU';
$lang['Forecastahead']          = 'HAVA DURUMU 10 GUN ';
$lang['Forecastsummary']        = 'HAVA DURUMU';
$lang['WindGust']               = 'Rüzgar Hızı | Bora';
$lang['Hourlyforecast']         = 'Saat Hava ';
$lang['Significantearthquake']  = 'Önemli Depremler';
$lang['Regionalearthquake']     = 'Bölgesel Depremler';
$lang['Average']                = 'Ortalama';
$lang['Notifications']          = 'Bildirimler <span>Dikkat</span>';
$lang['Indoor']                 = 'Ev';
$lang['Today']                  = 'Bugün';
$lang['Tonight']                = 'Bu Gece';
$lang['Tomorrow']               = 'Yarin';
$lang['Tomorrownight']          = 'Yarin Gece';
$lang['Updated']                = 'Yenile';
$lang['Meteor']                 = 'Meteor Yağmuru Info';
$lang['Firerisk']               = 'Yangın Tehlikesi';
$lang['Localtime']              = 'Mahalli <span2>Saat</span2>';
$lang['Nometeor']               = 'Meteor Yağmuru Hayir';
$lang['LiveWebCam']             = 'Canlı Yayın Cam';
$lang['Online']                 = 'Online';
$lang['Offline']                = 'Offline';
$lang['Weatherstation']         = 'Meteoroloji İstasyon';
$lang['Cloudbase']              = 'Bulutu';
$lang['uvalert']                = 'Dikkat UVINDEX';
$lang['Max']                    = 'Max';
$lang['Min']                    = 'Min';
//earthquake TOP MODULE 10 July 2017
$lang['MicroE']                  = 'Micro Earthquake';
$lang['MinorE']                  = 'Az  Depremler';
$lang['LightE']                  = 'Light Earthquake';
$lang['ModerateE']               = 'Orta  Depremler';
$lang['StrongE']                 = 'Önemli  Depremler';
$lang['MajorE']                  = 'Major Earthquake';
$lang['GreatE']                  = 'Great Earthquake';
$lang['RegionalE']              = 'Bölgesel';
$lang['Conditions']             = 'Kosullari';
$lang['Cloudbase Height']       = 'Bulutu';
$lang['Station']                = 'İstasyon';
$lang['Detailed Forecast']      = 'Hava Durumu Gun ';
$lang['Summary Outlook']        = 'Hava Durumu';
//Air Quality
$lang['Hazordous']              = 'Kötü Hava Kosullari';
$lang['VeryUnhealthy']          = 'Sağlıksız Hava Kalitesi';
$lang['Unhealthy']              = 'Sağlıksız Hava Kalitesi Uyarisi';
$lang['UnhealthyFS']            = 'Sağlıksız Hava Kalitesi Uyarisi';
$lang['Moderate']               = 'Orta Hava Kalitesi Uyarisi';
$lang['Good']                   = 'Saglikli Hava Kalitesi ';
#notification additions
$lang['notifyTitle']            = 'Istasyonu <span>Dikkat</span>';
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
$lang['StationTime']             = 'İstasyon Saati';
$lang['MoonPhase']               = 'Ay Fazı';
$lang['LocalNowcast']            = 'Yerel Tahmin';
$lang['Lightning']               = 'Yıldırım';
$lang['AirQuality']              = 'Hava Kalitesi';
$lang['UVSolar']                 = 'UV & Güneş';
$lang['Webcam']                  = 'Web Kamerası';
$lang['SpaceWeather']            = 'Uzay Havası';
$lang['NwsRadar']                = 'NWS Radar';
$lang['TemperatureYear']         = 'Sıcaklık Yılı';
$lang['WindGustYear']            = 'Rüzgar Hamlesi Yılı';
$lang['RainfallTotals']          = 'Yağış Toplamları';
$lang['WeatherAdvisory']         = 'Hava Uyarısı';

# Synced from lang.en.php (keys added for newer modules)
$lang['Wind']                     = 'Rüzgar';
$lang['Active']                   = 'Aktif';
$lang['AIndex']                   = 'A-Endeksi';
$lang['AuroraAGuide']             = '1000 mil veya daha uzak mesafelerde olası iyileşmiş VHF telsiz iletişiminin bir göstergesidir. Güçlü güneş etkinliği sırasında 28 ila 433 MHz veya daha yüksek frekanslar orta ve yüksek enlemlerde telsiz iletişimini mümkün kılar.';
$lang['AuroraHamGuide']           = 'Aurora iletişimi VHF amatör telsizcileri tarafından kullanılabilir. Aurora saçılımı yayılımı amatör telsiz iletişimini mümkün kılar.';
$lang['AuroraHamGuide2']          = 'Özel tekniklerle aurora saçılımı iletişimi 28 MHz/50 MHz/144 MHz/433 MHz frekanslarında 2000 km veya daha uzak mesafelere olanak tanır.';
$lang['AuroraKpGuide']            = 'kutup ışıklarını görmek için iyi bir göstergedir. Kp endeksi ne kadar yüksekse görme olasılığı o kadar yüksektir. Tahmini 3 saatlik gezegensel Kp endeksi yer tabanlı manyetometrelerden alınır.';
$lang['AuroraNowcast']            = 'Aurora Anlık Tahmini';
$lang['DataProvidedBy']           = 'Veri sağlayıcı:';
$lang['EstimatedZHR']             = 'Tahmini ZHR';
$lang['Geo']                      = 'Geo';
$lang['GeomagneticMajorStorm']    = 'Büyük Jeomanyetik Fırtına';
$lang['GeomagneticSevereStorm']   = 'Şiddetli Jeomanyetik Fırtına';
$lang['GeomagneticStorm']         = 'Jeomanyetik Fırtına';
$lang['Guide']                    = 'Rehber';
$lang['Information']              = 'Bilgi';
$lang['Kp']                       = 'Kp';
$lang['KpGeomagneticForecast']    = 'Kp Jeomanyetik Tahmini';
$lang['KpIndex']                  = 'Kp Endeksi';
$lang['KpPlanetaryIndex']         = 'KP GEZEGENSEL ENDEKSİ';
$lang['LastUpdated']              = 'Son Güncelleme';
$lang['LatestObserved']           = 'Son Gözlem';
$lang['MajorStorm']               = 'Büyük Fırtına';
$lang['MeteorShower']             = 'Meteor Yağmuru';
$lang['MeteorShowers']            = 'Meteor Yağmurları';
$lang['MinorGeomagneticStorm']    = 'Küçük Jeomanyetik Fırtına';
$lang['MinorStorm']               = 'Küçük Fırtına';
$lang['NoMeteorShower']           = 'Meteor Yağmuru Yok';
$lang['NorthernLights']           = 'Kuzey Işıkları';
$lang['NotActive']                = 'Aktif Değil';
$lang['Peaks']                    = 'Zirve';
$lang['PeakViewingNow']           = 'Şu An En İyi Görüş';
$lang['Quiet']                    = 'Sakin';
$lang['QuietNoGeomagneticStorm']  = 'Sakin, Jeomanyetik Fırtına Yok';
$lang['QuietNoStorm']             = 'Sakin, Fırtına Yok';
$lang['Radio']                    = 'Radyo';
$lang['RadioAurora']              = 'Radyo Aurora';
$lang['RadioHamGuide']            = 'Amatör Telsiz Rehberi';
$lang['SevereStorm']              = 'Şiddetli Fırtına';
$lang['Solar']                    = 'Güneş';
$lang['WeakGeomagneticStorm']     = 'Zayıf Jeomanyetik Fırtına';
$lang['WeakStorm']                = 'Zayıf Fırtına';
$lang['ViewLine']                 = 'Görüş Hattı';
$lang['Forecast3Day']             = '3 Günlük Tahmin';
$lang['HemPower']                 = 'Yarıküre Gücü';
$lang['Ovation']                  = 'Ovation';
$lang['ForecastUnavailable']      = 'Tahmin verisi yok — saatte bir yenilenir.';
$lang['ApIndex']                  = 'Ap Endeksi';
$lang['Ap']                       = 'Ap';
$lang['Type']                     = 'Tür';
$lang['Date']                     = 'Tarih';
$lang['GeomagneticActivityProbabilities'] = 'Jeomanyetik Etkinlik Olasılıkları';
$lang['Level']                    = 'Seviye';
$lang['KpIndexForecast']          = 'Kp Endeksi Tahmini (UT)';
$lang['Period']                   = 'Dönem';
$lang['Issued']                   = 'Yayımlandı';
$lang['HemispherePowerUnavailable'] = 'Yarıküre gücü verisi yok — 5 dakikada bir yenilenir.';
$lang['NorthernHemisphereOvation'] = 'Kuzey Yarıküre — Ovation';
$lang['SouthernHemisphereOvation'] = 'Güney Yarıküre — Ovation';
$lang['ObservationTimeUTC']       = 'Gözlem Zamanı UTC';
$lang['NorthGW']                  = 'Kuzey (GW)';
$lang['SouthGW']                  = 'Güney (GW)';
$lang['G1PlusStorm']              = 'G1+ Fırtına';
$lang['Nowcast6h']                = '6 Saatlik Anlık Tahmin';
$lang['Snow6h']                   = 'Kar 6 sa';
$lang['Rain6h']                   = 'Yağmur 6 sa';
$lang['Dry']                      = 'Kuru';
$lang['Slight']                   = 'Hafif';
$lang['Chance']                   = 'Olası';
$lang['Likely']                   = 'Muhtemel';
$lang['NoHyperlocalNowcast']      = 'Yerel anlık tahmin mevcut değil.';
$lang['NwsWeatherAlerts']         = 'NWS Hava Uyarıları';
$lang['ActiveNwsWeatherAlerts']   = 'Aktif NWS Hava Uyarıları';
$lang['Fetched']                  = 'Alındı';
$lang['NoActiveWeatherAlerts']    = 'Şu anda aktif hava uyarısı yok.';
$lang['Headline']                 = 'Başlık';
$lang['Effective']                = 'Geçerlilik';
$lang['Expires']                  = 'Bitiş';
$lang['Unknown']                  = 'Bilinmiyor';
$lang['AreaForecastDiscussion']   = 'Bölgesel Tahmin Değerlendirmesi';
$lang['KeyMessages']              = 'Temel Mesajlar';
$lang['ShortTerm']                = 'Kısa Vade';
$lang['LongTerm']                 = 'Uzun Vade';
$lang['OutlookTrend']             = 'Eğilim';
$lang['ShortTermDiscussion']      = 'Kısa Vadeli Değerlendirme';
$lang['LongTermDiscussion']       = 'Uzun Vadeli Değerlendirme';
$lang['NoSourceText']             = 'Kaynak metin yok.';
$lang['NoSummary']                = 'Henüz özet';
$lang['GeneratedYet']             = 'oluşturulmadı.';
$lang['CheckBackgroundScript']    = 'Arka plan betiğinin çalıştığını kontrol edin.';
$lang['ConfirmOllamaStatus']      = 'Pi üzerinde <span class="hl-orange">Ollama durumunu</span> kontrol edin.';
$lang['Strikes']                  = 'Çakmalar';
$lang['Last3Hrs']                 = 'Son 3 Saat';
$lang['Distance']                 = 'Mesafe';
$lang['LastStrike']               = 'Son Çakma';
$lang['LastDistanceAt']           = 'Son Mesafe Saati';
$lang['AllTimeStrikeTotal']       = 'Toplam Çakma';
$lang['Miles']                    = 'mil';
$lang['Km']                       = 'km';
?>