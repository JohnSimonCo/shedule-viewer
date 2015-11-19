<?php

include "cors.php";

$week = $_GET["week"];
$className = $_GET["className"];

if(file_exists("schedules/$week")) {
    $file = "schedules/$week/$className.json";
    if(file_exists($file)) {
        echo file_get_contents($file);
        exit();
    } else {
        not_found();
    }
}

include('./httpful.phar');

$response = \Httpful\Request::get("http://jrp.se:8080/s/schedule/$week")
    ->send();

if($response->code != 200) {
    not_found();
}

//$response = file_get_contents("data.json");

$schedules = json_decode($response, true);
$schedule_found = false;

mkdir("schedules/$week", 0777, true);
foreach($schedules as $schedule) {
    $name = $schedule["className"];
    $json = json_encode($schedule);
    $file = "schedules/$week/$name.json";
    file_put_contents($file, $json);

    if($name == $className) {
        $schedule_found = true;
        echo $json;
    }
}
if(!$schedule_found) {
    not_found();
}

function not_found() {
    echo "Schedule not found";
    die(400);
}