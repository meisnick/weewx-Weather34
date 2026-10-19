<?php
include_once('w34_i18n.php');
$theme = (isset($_GET['theme']) && $_GET['theme'] === 'light') ? 'light' : 'dark';
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="utf-8">
<title><?php echo w34t('LiveRadar', 'Live Radar'); ?></title>
<link rel="stylesheet" href="css/tokens.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html,body{width:100%;height:100%;overflow:hidden;background:#000}
iframe{width:100%;height:100%;border:none;display:block}
</style>
</head>
<body>
<iframe src="https://radar.weather.gov/?settings=v1_eyJhZ2VuZGEiOnsiaWQiOiJ3ZWF0aGVyIiwiY2VudGVyIjpbLTg4LjA5Myw0My4zNzVdLCJsb2NhdGlvbiI6Wy04Ny45NzIsNDMuNDEzXSwiem9vbSI6Ny45ODE5MzI3MDY0NzkxMzUsImxheWVyIjoiYnJlZl9xY2QifSwiYW5pbWF0aW5nIjpmYWxzZSwiYmFzZSI6ImRhcmtjYW52YXMiLCJhcnRjYyI6ZmFsc2UsImNvdW50eSI6dHJ1ZSwiY3dhIjpmYWxzZSwicmZjIjpmYWxzZSwic3RhdGUiOmZhbHNlLCJtZW51Ijp0cnVlLCJzaG9ydEZ1c2VkT25seSI6ZmFsc2UsIm9wYWNpdHkiOnsiYWxlcnRzIjowLCJsb2NhbCI6MC42LCJsb2NhbFN0YXRpb25zIjowLjgsIm5hdGlvbmFsIjowLjQ2fX0%3D"
        allowfullscreen></iframe>
</body>
</html>
