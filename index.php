<?php
$cachefile = basename($_SERVER['PHP_SELF'], '.php') . '.cache';
clearstatcache();

if (file_exists($cachefile) && filemtime($cachefile) > time() - 10) { // good to serve!
    include($cachefile);
    exit;
}

ob_start();

// DELIMITER

$url = "https://api.open-meteo.com/v1/dwd-icon?latitude=52.52&longitude=13.41&hourly=temperature_2m&current_weather=true&timezone=Europe%2FBerlin&start_date=2023-03-01&end_date=2023-03-09";

$response = file_get_contents($url);

$data = json_decode($response);

$hourly_forecast = $data->hourly->temperature_2m;
$when = $data->hourly->time;

$today = date("Y-m-d");
$time_now = date("h:00", strtotime(date("h:i")));

$thing = $today."T".$time_now;
$days = [];

foreach($when as $dt){
    array_push($days, substr($dt, 0, 10));
}
$days = array_unique($days);

?>
<html lang="en">
<head>
    <title>Sunny innit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <style>
        img {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
<div class="text-center">
    <?php
    echo "<h1>Weather now:  ".$today.", ".$time_now."</h1>";
    echo "<h1>".round($hourly_forecast[array_search($thing, $when)])."°C</h1>"
    ?>
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-2">
            <img src="img/weather-showers-scattered-min.png" loading="lazy" alt="">
        </div>
        <div class="col-md-2">
            <img src="img/weather-clear-min.png" loading="lazy" alt="">
        </div>
        <div class="col-md-2">
            <img src="img/weather-overcast-min.png" loading="lazy" alt="">
        </div>
    </div>
    <?php
    foreach ($days as $day) {
        echo "<h1>".$day."</h1><br>";
        echo "<table style='width:100%'><th>";
        foreach ($hourly_forecast as $hour => $temperature){

            if (substr($when[$hour], 0, 10) == $day){
                echo "<td>".substr($when[$hour], 11)."<br>".round($temperature)."°C</td>";
            }

        }
        echo "</th></table>";
    }
    ?>
</div>
<!--
    <table>
        <tr>
            <th>Hour</th>
            <th>Temperature °C</th>
        </tr>
        <?php
foreach ($hourly_forecast as $hour => $temperature) {
    echo "<tr><th>$when[$hour]</th><th>".round($temperature)."</th></tr>";
}
?>
    </table>
    -->

</body>
</html>
<?php
// END DELIMITER
$contents = ob_get_contents();
ob_end_clean();

$handle = fopen("var/www/public_html/$cachefile", "w");
fwrite($handle, $contents);
fclose($handle);

include("var/www/public_html/$cachefile");
?>

