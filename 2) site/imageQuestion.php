<?php declare(strict_types=1);
require_once 'autoload.php';

$imgID = 1;
if (isset($_GET['id']))
    if (ctype_digit($_GET['id']))
        $imgID = intval($_GET['id']);

//
$image = Image::createFromId($imgID);
$string = base64_decode($image->getContenu());
$img = imagecreatefromstring($string);
header('Content-Type: image/jpeg');
imagejpeg($img);
imagedestroy($img);