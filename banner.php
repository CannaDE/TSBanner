<?php
//-> Copyright © 2016 by Canna
//-> 4.3.2017 Changes by Lars "elyday" <me@elyday.net>
//-> Version: 0.9.7
include('lib/ts3admin.class.php');
include('config.inc.php');
header('Content-Type:image/png');
date_default_timezone_set('Europe/Berlin');;
$date = date('d.F Y');
$time = date('H:i') . " Uhr";
$slots = 0;
$maxSlots = 0;
function tsconnect()
{
    global $ts3config;
    $ts3 = new ts3admin($ts3config['ts3host'], $ts3config['ts3qport']);
    $connecting = $ts3->connect();
    if ($connecting['success']) {
        $select = $ts3->selectServer($ts3config['ts3port'], true);
        if ($select['success']) {
            if ($ts3config['ts3user'] != "" && $ts3config['ts3pass']) {
                $logging = $ts3->login($ts3config['ts3user'], $ts3config['ts3pass']);
                if ($logging)
                    return $ts3;
                else
                    return '<p>Die Login Daten funktionieren nicht!</p>';
            } else
                return '<p> Fehler in der config.inc.php. Bitte überprüfe diese und stelle sicher, dass alle Daten vorhanden sind!</p>';
        } else
            return '<p>Der TS3 Port ist fehlerhaft!</p>';
    } else
        return '<p>Es ist ein unerwarteter Fehler aufgetreten. </p>';
}
$ts3 = tsconnect();
$sinfo = $ts3->serverInfo();
if ($sinfo['success']) {
    $sinfo = $sinfo['data'];
    $slots = $sinfo['virtualserver_clientsonline'] - $sinfo['virtualserver_queryclientsonline'];
    $maxSlots = $sinfo['virtualserver_maxclients'];
    $query = $sinfo['virtualserver_queryclientsonline'] - 1;
    $text = $time . " | " . $date . " | User: " . $slots . "(+" . $query . ") / " . $maxSlots;
    $textTime = $time;
    $textDate = $date;
    $textClients = $slots . "/" . $maxSlots;
    $image = imagecreatefrompng('img/cannadesign_tsbanner.png');
    $textColor = imagecolorallocate($image, 79, 95, 5);
    //imagestring($image, 5, 10, 5, $text, $textColor);
    //imagettftext($image, 18, 0, 5, 225, $textColor, "fonts/Exo-Medium.ttf", $text);
    imagettftext($image, 20, 0, 60, 200, $textColor, "fonts/Exo-Medium.ttf", $textTime);
    imagettftext($image, 20, 0, 60, 240, $textColor, "fonts/Exo-Medium.ttf", $textDate);
    imagettftext($image, 20, 0, 60, 280, $textColor, "fonts/Exo-Medium.ttf", $textClients);
    imagejpeg($image);
} else {
    echo $ts3;
}
?>
