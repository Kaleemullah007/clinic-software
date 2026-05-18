<?php
$sChars = 'abcdefghijklmnopqrstuvwxyz0123456789';
$sCode = substr(str_shuffle($sChars), 0, 5);
session(['SpamCode' => $sCode]);
session(['Md5SpamCode' => md5(strtolower($sCode))]);

$sLetter1 = $sCode[0];
$sLetter2 = $sCode[1];
$sLetter3 = $sCode[2];
$sLetter4 = $sCode[3];
$sLetter5 = $sCode[4];

$objImage = imagecreatefromjpeg(public_path('code-bg.jpg'));
$sFont = public_path('verdana/verdana.ttf');
$iAngle = 0;
$iFontSize = 20;
$incr = 90;
$fromTop = 28;
$sColors[0] = [122, 229, 112];
$sColors[1] = [85, 178, 85];
$sColors[2] = [226, 108, 97];
$sColors[3] = [141, 214, 210];
$sColors[4] = [214, 141, 205];
$sColors[5] = [100, 138, 204];

$iColor1 = rand(0, 5);
$iColor2 = rand(0, 5);
$iColor3 = rand(0, 5);
$iColor4 = rand(0, 5);
$iColor5 = rand(0, 5);

$sTextColor1 = imagecolorallocate($objImage, $sColors[$iColor1][0], $sColors[$iColor1][1], $sColors[$iColor1][2]);
$sTextColor2 = imagecolorallocate($objImage, $sColors[$iColor2][0], $sColors[$iColor2][1], $sColors[$iColor2][2]);
$sTextColor3 = imagecolorallocate($objImage, $sColors[$iColor3][0], $sColors[$iColor3][1], $sColors[$iColor3][2]);
$sTextColor4 = imagecolorallocate($objImage, $sColors[$iColor4][0], $sColors[$iColor4][1], $sColors[$iColor4][2]);
$sTextColor5 = imagecolorallocate($objImage, $sColors[$iColor5][0], $sColors[$iColor5][1], $sColors[$iColor5][2]);

imagettftext($objImage, $iFontSize, $iAngle, 10+$incr,$fromTop, $sTextColor1, $sFont, $sLetter1);
imagettftext($objImage, $iFontSize, $iAngle, 32+$incr+20,$fromTop, $sTextColor2, $sFont, $sLetter2);
imagettftext($objImage, $iFontSize, $iAngle, 54+$incr+40,$fromTop, $sTextColor3, $sFont, $sLetter3);
imagettftext($objImage, $iFontSize, $iAngle, 76+$incr+60,$fromTop, $sTextColor4, $sFont, $sLetter4);
imagettftext($objImage, $iFontSize, $iAngle, 98+$incr+80,$fromTop, $sTextColor5, $sFont, $sLetter5);

header('Content-type: image/jpeg');

imagejpeg($objImage, null, 100);
imagedestroy($objImage);

?>
