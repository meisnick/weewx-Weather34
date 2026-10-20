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
include('settings.php');
include_once('w34CombinedData.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }

//weather34 next meteor event original idea betejuice of cumulus forum..
$meteor_nextevent="No Meteor Shower<br>s";
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 12, 23,19),"event_title"=>"Meteor Shower<br> <orange1>Quadrantids</orange1><div class=date><br>Active Dec 28th-Jan 12th
<br><green>Estimated ZHR: </green><orange>120 <br> Peaks <orange>Jan 3rd-4th</orange></div></div>","event_end"=>mktime(23, 59, 59, 1, 2,20),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 12, 24),"event_title"=>"Meteor Shower<br> <orange1>Quadrantids</orange1><div class=date><br>Active Dec 28th-Jan 12th
<br><green>Estimated ZHR: </green><orange>120 <br>Peaks <orange>Jan 3rd-4th</orange></div></div>","event_end"=>mktime(23, 59, 59, 1, 2),);
$meteor_eventsnext[]=array("event_start"=>mktime(0, 0, 0, 1, 3),"event_title"=>"Meteor Shower<br> <orange1>Quadrantids</orange1><div class=date><br>Peak Viewing Now<br><div class=date>
<br><green>Estimated ZHR: </green><orange>120</div></div>","event_end"=>mktime(23, 59, 59, 1, 4),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 1, 2),"event_title"=>"Meteor Shower<br> <orange1>Lyrids</orange1><div class=date><br>Active Apr 18th-Apr 25th<br>
<green>Estimated ZHR: </green><orange>18</orange><br>Peaks <orange>Apr 23rd</orange></div></div>","event_end"=>mktime(23, 59, 59, 4, 20),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 4, 20),"event_title"=>"Meteor Shower<br> <orange1>Lyrids</orange1> <div class=date><br>Peak Viewing Now<br>
<green>Estimated ZHR: </green><orange>18</orange><br>Peaks <orange>Apr 23rd</orange></div></div>","event_end"=>mktime(23, 59, 59, 4, 22),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 4, 22),"event_title"=>"Meteor Shower<br> <orange1>ETA Aquarids </orange1><br><div class=date><br>Active Apr 24th-May 19th<br>
<green>Estimated ZHR: </green><orange>55 </orange><br> Peaks <orange>May 6th</orange></div></div>","event_end"=>mktime(23, 59, 59, 5, 6),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 5, 6),"event_title"=>"Meteor Shower<br> <orange1>Delta Aquarids</orange1><div class=date><br>Active Jul 21st-Aug 23rd<br>
<green>Estimated ZHR: </green><orange>16</orange><br> Peaks <orange>Jul 28th</orange></div></div>","event_end"=>mktime(23, 59, 59, 7, 27),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 7, 27),"event_title"=>"Meteor Shower<br> <orange1> Perseids</orange1><div class=date><br>Active Jul 13th-Aug 26th<br>
<green>Estimated ZHR: </green><orange>100</orange><br> Peaks <orange>Aug 11th-13th</orange></div>","event_end"=>mktime(23, 59, 59, 8, 10),);
$meteor_eventsnext[]=array("event_start"=>mktime(0, 0, 0, 8, 12),"event_title"=>"Meteor Shower<br><orange1> Perseids</orange1><div class=date><br>Peak Viewing Now<br>
<green>Estimated ZHR: </green><orange>100</orange><br> Peaks <orange>Aug 11th-13th</orange></div></div>","event_end"=>mktime(23, 59, 59, 8, 13),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 8, 13),"event_title"=>"Meteor Shower<br> <orange1> Draconids</orange1><div class=date><br>Active October 6th-10th<br>
<green>Estimated ZHR: </green><orange>5</orange><br> Peaks <orange>Oct 9th</orange></div></div>","event_end"=>mktime(23, 59, 59, 10, 7),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 10, 7),"event_title"=>"Meteor Shower<br> <orange1> Orionids</orange1><div class=date><br>Active Oct 21st-Oct 22nd<br>
<green>Estimated ZHR: </green><orange>25</orange><br> Peaks <orange>Oct 22nd</orange></div></div>","event_end"=>mktime(23, 59, 59, 10, 21),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 10, 21),"event_title"=>"Meteor Shower<br> <orange1> South Taurids</orange1><div class=date><br>Active Nov 4th- Nov 5th<br>
<green>Estimated ZHR: </green><orange>5</orange><br> Peaks <orange>Oct 10th</orange></div></div>","event_end"=>mktime(23, 59, 59, 11, 5),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 11, 5),"event_title"=>"Meteor Shower<br> <orange1> North Taurids</orange1><div class=date><br>Active Nov 11th<br>
<green>Estimated ZHR: </green><orange>5</orange><br> Peaks <orange>Nov 13th</orange></div></div>","event_end"=>mktime(23, 59, 59, 11, 11),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 11, 11),"event_title"=>"Meteor Shower<br> <orange1> Leonids</orange1><div class=date><br>Active Nov 5th-Dec 3rd<br>
<green>Estimated ZHR: </green><orange>15</orange><br> Peaks <orange>Nov 18th</orange></div></div>","event_end"=>mktime(23, 59, 59, 11, 18),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 11, 18),"event_title"=>"Meteor Shower<br> <orange1> Geminids</orange1><div class=date><br>Active Nov 30th-Dec 17th<br>
<green>Estimated ZHR: </green><orange>120</orange><br> Peaks <orange>Dec 14th</orange></div></div>","event_end"=>mktime(23, 59, 59, 12, 16),);
$meteor_eventsnext[]=array("event_start"=>mktime(23, 59, 59, 12, 15),"event_title"=>"Meteor Shower<br> <orange1> Ursids</orange1><div class=date><br>Active Dec 17th-Dec 24th<br>
<green>Estimated ZHR: </green><orange>5-10</orange><br> Peaks <orange>Dec 23rd</orange></div></div>","event_end"=>mktime(23, 59, 59, 12, 18),);

$meteorNext=time();$meteorOP=false;
foreach ($meteor_eventsnext as $meteor_check) {if ($meteor_check["event_start"]<=$meteorNext&&$meteorNext<=$meteor_check["event_end"]) {$meteorOP=true;$meteor_nextevent=$meteor_check["event_title"];}};
//end meteor nevt event
$meteorinfo3="<svg width='22px' height='22px' viewBox='0 0 16 16'><path fill='darkgrey' d='M0 0l14.527 13.615s.274.382-.081.764c-.355.382-.82.055-.82.055L0 0zm4.315 1.364l11.277 10.368s.274.382-.081.764c-.355.382-.82.055-.82.055L4.315 1.364zm-3.032 2.92l11.278 10.368s.273.382-.082.764c-.355.382-.819.054-.819.054L1.283 4.284zm6.679-1.747l7.88 7.244s.19.267-.058.534-.572.038-.572.038l-7.25-7.816zm-5.68 5.13l7.88 7.244s.19.266-.058.533-.572.038-.572.038l-7.25-7.815zm9.406-3.438l3.597 3.285s.094.125-.029.25c-.122.125-.283.018-.283.018L11.688 4.23zm-7.592 7.04l3.597 3.285s.095.125-.028.25-.283.018-.283.018l-3.286-3.553z'/></svg>";
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('MeteorShowers', 'Meteor Showers'); ?></title>
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
a:link,a:visited,a:hover{color:silver}
a:active{color:var(--pop-value)}
[data-theme="light"] a:link,[data-theme="light"] a:visited,[data-theme="light"] a:hover,[data-theme="light"] a:active{color:var(--pop-cyan)}
.weather34darkbrowser{position:relative;background:0;width:97%;height:30px;margin:auto;margin-top:-5px;margin-left:0;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px;margin-right:44px}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-value);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 10px 0 auto;font-family:arial;height:20px}
blue{color:var(--pop-blue)}
orange{color:var(--pop-cyan)}
orange1{position:relative;color:var(--pop-cyan);margin:0 auto;text-align:center;margin-left:5%;font-size:1.1rem}
green{color:#aaa}
[data-theme="light"] green{color:#2d7a2d}
red{color:var(--pop-red)}
red6{color:var(--pop-red-dark)}
value{color:var(--pop-value)}
yellow{color:var(--pop-yellow-dim)}
purple{color:var(--pop-purple)}
meteotextshowertext{font-size:1.2rem;color:var(--pop-cyan)}
meteorsvgicon{color:#f5f7fc}
[data-theme="light"] meteorsvgicon{color:#000}
.moonphasetext{font-size:1.1rem;color:var(--pop-value);position:absolute;display:inline;left:140px;top:80px}
moonphaseriseset{font-size:.9rem}
credit{position:relative;font-size:.8em;top:10%}
.actualt{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74,99,111,.1);padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:#aaa;align-items:center;justify-content:center;margin-bottom:10px;top:0}
[data-theme="light"] .actualt{background:teal;color:#fff;}
.actualw{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74,99,111,.1);padding:5px;font-family:Arial,Helvetica,sans-serif;width:100px;height:.8em;font-size:.8rem;padding-top:2px;color:var(--pop-green-dim);border-bottom:2px solid var(--pop-badge-border);align-items:center;justify-content:center;margin-bottom:10px;top:0}
  </style>
</head>
<body>
<div class="weather34darkbrowser" url="<?php echo htmlspecialchars(w34t('MeteorShowers', 'Meteor Showers') . ' ' . $stationName); ?>"></div>
<main class="grid">
  <article>       
<meteotextshowertext>
<?php if ($meteor_default)  {	echo "<meteorsvgicon>".$meteorinfo3."</meteorsvgicon> ";
  echo ($meteor_default === "No Meteor") ? w34t('NoMeteorShowerVisible', 'No Meteor Shower<br>Currently Visible')
    : w34_meteor($meteor_default) . "<br>" . w34t('ShowerCurrentlyVisible', 'Shower Currently Visible');} 
?></meteotextshowertext><br>
 <?php	echo w34_date('D jS M Y');?>       
</article>  
  
  <article>
  <?php if ($meteor_nextevent)  {echo $meteorinfo3 ," ", w34t('NextLabel', 'Next'), " ", w34_meteor($meteor_nextevent) ;} ?>    
  </article> 
  
  <article>  
   <?php echo $info ;?> <green><?php echo w34f('MeteorGuideTitle', 'Guide to %s Major Meteor Showers', date('Y'));?><br></green> <br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Quadrantids', 'Quadrantids');?></green> <?php echo w34_datetext('Dec 28-Jan 12');?><br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Lyrids', 'Lyrids');?></green> <?php echo w34_datetext('Apr 18-Apr 25');?><br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Perseids', 'Perseids');?></green> <?php echo w34_datetext('Jul 13-Aug 26');?><br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Leonids', 'Leonids');?></green> <?php echo w34_datetext('Nov 05-Dec 03');?><br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Geminids', 'Geminids');?></green> <?php echo w34_datetext('Nov 30-Dec 17');?><br>
       <?php echo $meteorinfo;?> <green><?php echo w34t('Ursids', 'Ursids');?></green> <?php echo w34_datetext('Dec 17-Dec 24');?><br>
        
   </article> 
   
    <article>
   <?php echo $info ;?> <orange><?php echo w34t('ViewingGuide', 'Viewing Guide'); ?></orange><br><?php echo w34t('MeteorViewingGuide', 'Meteors are best viewed during the night hours, though they enter the atmosphere at any time of day; they are just harder to see in daylight apart from dawn and dusk. Nearby ambient light and moonlight make viewing difficult. Meteor showers are best viewed away from city lights.'); ?><br>
 
 
              
  </article>  
  
  <article>
   <?php echo $info ;?> <orange><?php echo w34t('RadioHamGuide', 'Radio Ham Guide'); ?></orange><br><?php echo w34t('MeteorHamGuide', 'Meteor scatter propagation lets VHF ham radio enthusiasts, and also commercial radio services, make contacts. With specialised operating techniques, meteor scatter allows distances of up to 2000 km or more on VHF frequencies.'); ?><br>
 
 
              
  </article> 
  <article>
  <div class=actualt> &copy; <?php echo w34t('Information', 'Information'); ?></div>  
  <?php echo $info?> <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin from the original CSS/SVG/PHP scripts by weather34.com'); ?> &copy; 2015-<?php echo date('Y');?>
  <br><br><?php echo $info ;?> <?php echo w34t('DataProvidedBy', 'Data Provided by'); ?> <a href="https://en.wikipedia.org/wiki/Meteor_shower" title="https://en.wikipedia.org/wiki/Meteor_shower" target="_blank"><?php echo w34t('InternationalMeteorOrg', 'International Meteor Organization'); ?></a> 
  
  </article> 
</main>
</body>
</html>
