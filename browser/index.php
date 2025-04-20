<?php

if (str_contains($_SERVER["REQUEST_URI"], "/?t=")) {
    header("Location: /" . substr($_SERVER["REQUEST_URI"], 4));
    die();
}

$files = scandir($_SERVER["DOCUMENT_ROOT"] . "/images");
$tiles = [];
$sorted = [];

function time_ago_en($time) {
    if(!is_numeric($time))
        $time = strtotime($time);

    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "age");
    $lengths = array("60","60","24","7","4.35","12","100");

    $now = time();

    $difference = $now - $time;
    if ($difference <= 10 && $difference >= 0)
        return $tense = 'just now';
    elseif($difference > 0)
        $tense = 'ago';
    elseif($difference < 0)
        $tense = 'later';

    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    $period =  $periods[$j] . ($difference >1 ? 's' :'');
    return "{$difference} {$period} {$tense} ";
}

foreach ($files as $file) {
    if ($file !== "." && $file !== ".." && str_ends_with($file, ".png")) {
        $s = substr($file, 0, -4);
        $s = str_replace("_", ":", $s);
        $s1 = explode(":", $s);
        $s2 = explode(" ", $s1[0]);
        array_shift($s1);
        $id = $s2[0] . " " . $s2[1] . " 2022 " . $s2[2] . ":" . implode(":", $s1);
        $tiles["2022-" . sprintf("%02.0f", date_parse($id)["month"]) . "-" . sprintf("%02.0f", date_parse($id)["day"]) . "-" . sprintf("%02.0f", date_parse($id)["hour"]) . "-" . sprintf("%02.0f", date_parse($id)["minute"]) . "-" . sprintf("%02.0f", date_parse($id)["second"])] = [
            'file' => $_SERVER["DOCUMENT_ROOT"] . "/images/" . $file,
            'path' => "/images/" . $file,
            'date_parsed' => date_parse($id),
            'date_id' => "2022-" . sprintf("%02.0f", date_parse($id)["month"]) . "-" . sprintf("%02.0f", date_parse($id)["day"]) . "-" . sprintf("%02.0f", date_parse($id)["hour"]) . "-" . sprintf("%02.0f", date_parse($id)["minute"]) . "-" . sprintf("%02.0f", date_parse($id)["second"]),
            'date_relative' => trim(time_ago_en(strtotime($id)))
        ];
        if (!isset($sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]])) $sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]] = [];
        if (!isset($sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]][date_parse($id)["hour"]])) $sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]][date_parse($id)["hour"]] = [];
        if (!isset($sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]][date_parse($id)["hour"]][date_parse($id)["minute"]])) $sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]][date_parse($id)["hour"]][date_parse($id)["minute"]] = [];
        $sorted[date_parse($id)["year"] . "/" . date_parse($id)["month"] . "/" . date_parse($id)["day"]][date_parse($id)["hour"]][date_parse($id)["minute"]][date_parse($id)["second"]] = $tiles["2022-" . sprintf("%02.0f", date_parse($id)["month"]) . "-" . sprintf("%02.0f", date_parse($id)["day"]) . "-" . sprintf("%02.0f", date_parse($id)["hour"]) . "-" . sprintf("%02.0f", date_parse($id)["minute"]) . "-" . sprintf("%02.0f", date_parse($id)["second"])];
    }
}

uasort($tiles, function ($a, $b) {
    return strcmp($a['date_id'], $b['date_id']);
});
$tiles = array_reverse($tiles, true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Selector | r/place Archive</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/dark.css">
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style> @media (max-width: 700px) { .mobilehide { display:none; } #canvas-img { padding-top: 80px !important; } #statusbar { text-align: center; } } @media (min-width: 700px) { .mobileonly { display:none; } } </style>
    <link rel="shortcut icon" href="/logo.png" type="image/png">
</head>
<body>
    <?php $title = "Archive browser"; require_once $_SERVER["DOCUMENT_ROOT"] . "/navigation.php"; ?>

    <div class="modal fade" id="about-box">
        <div class="modal-dialog">
            <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/about.php"; ?>
        </div>
    </div>

    <div class="container" style="margin-top: 80px;">
        <p>Select a day:</p>
        <div class="btn-group-vertical">
            <?php foreach (array_keys($sorted) as $day): ?>
                <button type="button" onclick="event.target.blur(); showDay('<?= $day ?>');" class="btn btn-primary"><?php

                    $parts = explode("/", $day);
                    $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                    echo $date;

                    ?></button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach (array_keys($sorted) as $day): ?>
        <div class="container sel-day" style="display:none;" id="sel-day-<?= $day ?>">
            <br>
            <p>Select an hour on <b><?php

                $parts = explode("/", $day);
                $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                echo $date;

                ?></b> (UTC, <span class="localTimeComparison">...</span>):</p>
            <div class="btn-group-vertical">
                <div class="btn-group">
                    <?php foreach (["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"] as $hour): ?>
                        <?php if (in_array($hour, array_keys($sorted[$day]))): ?>
                            <button type="button" onclick="event.target.blur(); showHour('<?= $day ?>', '<?= $hour ?>');" class="btn btn-primary"><?php

                                echo $hour;

                                ?></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary" disabled><?php

                                echo $hour;

                                ?></button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="btn-group">
                    <?php foreach (["10", "11", "12", "13", "14", "15", "16", "17", "18", "19"] as $hour): ?>
                        <?php if (in_array($hour, array_keys($sorted[$day]))): ?>
                            <button type="button" onclick="event.target.blur(); showHour('<?= $day ?>', '<?= $hour ?>');" class="btn btn-primary"><?php

                                echo $hour;

                                ?></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary" disabled><?php

                                echo $hour;

                                ?></button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="btn-group">
                    <?php foreach (["20", "21", "22", "23"] as $hour): ?>
                        <?php if (in_array($hour, array_keys($sorted[$day]))): ?>
                            <button type="button" onclick="event.target.blur(); showHour('<?= $day ?>', '<?= $hour ?>');" class="btn btn-primary"><?php

                                echo $hour;

                                ?></button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary" disabled><?php

                                echo $hour;

                                ?></button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach (array_keys($sorted) as $day): ?>
        <?php foreach (array_keys($sorted[$day]) as $hour): ?>
            <div class="container sel-minute" style="display:none;" id="sel-day-<?= $day ?>-hour-<?= $hour ?>">
                <br>
                <p>Select a minute on <b><?php

                        $parts = explode("/", $day);
                        $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                        echo $date;

                        ?></b>, at <b><?= $hour ?></b> (UTC, <span class="localTimeComparison">...</span>):</p>
                <div class="btn-group-vertical">
                    <div class="btn-group">
                        <?php foreach (["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="btn-group">
                        <?php foreach (["10", "11", "12", "13", "14", "15", "16", "17", "18", "19"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="btn-group">
                        <?php foreach (["20", "21", "22", "23", "24", "25", "26", "27", "28", "29"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="btn-group">
                        <?php foreach (["30", "31", "32", "33", "34", "35", "36", "37", "38", "39"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="btn-group">
                        <?php foreach (["40", "41", "42", "43", "44", "45", "46", "47", "48", "49"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="btn-group">
                        <?php foreach (["50", "51", "52", "53", "54", "55", "56", "57", "58", "59"] as $minute): ?>
                            <?php if (in_array($minute, array_keys($sorted[$day][$hour]))): ?>
                                <button type="button" onclick="event.target.blur(); showMinute('<?= $day ?>', '<?= $hour ?>', '<?= $minute ?>');" class="btn btn-primary"><?php

                                    echo $minute;

                                    ?></button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled><?php

                                    echo $minute;

                                    ?></button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php foreach (array_keys($sorted) as $day): ?>
        <?php foreach (array_keys($sorted[$day]) as $hour): ?>
            <?php foreach (array_keys($sorted[$day][$hour]) as $minute): ?>
                <div class="container sel-second" style="display:none;" id="sel-day-<?= $day ?>-hour-<?= $hour ?>-minute-<?= $minute ?>">
                    <br>
                    <?php $snapshots = array_keys($sorted[$day][$hour][$minute]); ?>
                    <?php if (count($snapshots) > 1): ?>
                        <p>There are multiple snapshot of r/place that were taken on <b><?php

                                $parts = explode("/", $day);
                                $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                                echo $date;

                                ?></b>, at <b><?= $hour ?>:<?= sprintf("%02.0f", $minute) ?></b> (UTC, <span class="localTimeComparison">...</span>):</p>
                    <ul>
                        <?php foreach ($snapshots as $snapshot): ?>
                        <li>
                            <a href="/<?= $sorted[$day][$hour][$minute][$snapshot]["date_id"] ?>"><?php

                                $parts = explode("/", $day);
                                $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                                echo $date;

                                ?>, at <?= $hour ?>:<?= sprintf("%02.0f", $minute) ?>:<?= sprintf("%02.0f", $snapshot) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                        <p>There is a snapshot of r/place that was taken on <b><?php

                                $parts = explode("/", $day);
                                $date = date("F, jS", mktime(0, 0, 0, (int)$parts[1], (int)$parts[2]));
                                echo $date;

                                ?></b>, at <b><?= $hour ?>:<?= sprintf("%02.0f", $minute) ?>:<?= sprintf("%02.0f", $snapshots[0]) ?></b> (UTC, <span class="localTimeComparison">...</span>).</p>
                        <a class="btn btn-outline-primary" href="/<?= $sorted[$day][$hour][$minute][$snapshots[0]]["date_id"] ?>">View snapshot</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <script>
        function showDay(day) {
            console.log(day);

            Array.from(document.getElementsByClassName("sel-day")).forEach((e) => { e.style.display = "none"; })
            Array.from(document.getElementsByClassName("sel-hour")).forEach((e) => { e.style.display = "none"; })
            Array.from(document.getElementsByClassName("sel-minute")).forEach((e) => { e.style.display = "none"; })
            Array.from(document.getElementsByClassName("sel-second")).forEach((e) => { e.style.display = "none"; })

            document.getElementById("sel-day-" + day).style.display = "";
            document.body.focus();
        }

        function showHour(day, hour) {
            console.log(day, hour);

            Array.from(document.getElementsByClassName("sel-hour")).forEach((e) => { e.style.display = "none"; })
            Array.from(document.getElementsByClassName("sel-minute")).forEach((e) => { e.style.display = "none"; })
            Array.from(document.getElementsByClassName("sel-second")).forEach((e) => { e.style.display = "none"; })

            document.getElementById("sel-day-" + day + "-hour-" + hour).style.display = "";
            document.body.focus();
        }

        function showMinute(day, hour, minute) {
            console.log(day, hour, minute);

            Array.from(document.getElementsByClassName("sel-second")).forEach((e) => { e.style.display = "none"; })

            document.getElementById("sel-day-" + day + "-hour-" + hour + "-minute-" + minute).style.display = "";
            document.body.focus();
        }

        Array.from(document.getElementsByClassName("localTimeComparison")).forEach((e) => {
            tz = new Date().toLocaleTimeString('en-us',{timeZoneName:'short'}).split("GMT")[1];
            if (tz === undefined || tz === "null") {
                e.innerText = "your local time";
            } else if (tz.startsWith("+")) {
                e.innerText = tz.substring(1) + " hour" + (tz.substring(1) - 1 + 1 > 1 ? "s" : "") + " behind you"
            } else if (tz.startsWith("-")) {
                e.innerText = tz.substring(1) + " hour" + (tz.substring(1) - 1 + 1 > 1 ? "s" : "") + " ahead of you"
            } else {
                e.innerText = "not your local time";
            }
        })
    </script>

    <br>
    <br>
</body>
</html>