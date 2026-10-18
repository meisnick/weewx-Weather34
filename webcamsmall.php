<?php include_once('w34CombinedData.php');error_reporting(0);?>
<style>
.webcam{
-webkit-border-radius:4px;	-moz-border-radius:4px;	-o-border-radius:4px;	-ms-border-radius:4px;border-radius:4px;border:solid var(--card-surface) 2px;width:275px;height:145px;margin:10px auto 0;display:block;}
[data-theme="light"] .webcam{border-color:var(--line-soft);}
</style>

<?php $file_headers = trim($webcamurl ?? '') !== '' ? @get_headers(trim($webcamurl)) : false; // get_headers('') throws on PHP 8 ?>
<div class="updatedtime1"><span>
<?php if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
  echo $online.' '.date($timeFormat);
} else if (file_exists($webcamurl)&&time()- filemtime($webcamurl)<300) {
  echo $online. ' <online>'.date($timeFormat,filemtime($webcamurl)).'</online>';
} else {
  echo $offline. '<offline> Offline </offline>';
}
?>
</span></div>
<!-- HOMEWEATHER STATION TEMPLATE SIMPLE WEBCAM -add your url as shown below do NOT delete the class='webcam' !!! -->
<a href="<?php echo htmlspecialchars($webcamurl ?? ''); ?>" data-lity><img src="<?php echo htmlspecialchars($webcamurl ?? ''); ?>?v=<?php echo date('YmdGis'); ?>" alt="weathercam" class="webcam"></a>


