<?php

$url = "https://api.open-meteo.com/v1/dwd-icon?latitude=52.52&longitude=13.41&hourly=temperature_2m&current_weather=true&timezone=Europe%2FBerlin&start_date=2023-03-01&end_date=2023-03-09";

$response = file_get_contents($url);

$data = json_decode($response);

$hourly_forecast = $data->hourly->temperature_2m;
$when = $data->hourly->time;

$today = date("Y-m-d");
$time_now = date("h:00", strtotime(date("h:i")));

$thing = $today."T".$time_now;


?>
<html lang="en">
<head>
    <title>Sunny innit</title>
</head>
<body>
<div style="text-align: center;">
    <?php
        echo "<h1>Weather now:  ".$today.", ".$time_now."</h1>";
        echo "<h1>".round($hourly_forecast[array_search($thing, $when)])."°C</h1>"
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
