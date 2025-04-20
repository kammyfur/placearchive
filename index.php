<?php

if (str_contains($_SERVER["REQUEST_URI"], "/?t=")) {
    header("Location: /" . substr($_SERVER["REQUEST_URI"], 4));
    die();
}

$files = scandir($_SERVER["DOCUMENT_ROOT"] . "/images");
$tiles = [];

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
    }
}

uasort($tiles, function ($a, $b) {
    return strcmp($a['date_id'], $b['date_id']);
});
$tiles = array_reverse($tiles, true);

if (!isset($_GET['t'])) {
    header("Location: /latest");
    die();
} else {
    $t = $_GET['t'];

    if ($t === "latest") {
        $t = array_keys($tiles)[0];
    }

    if (isset($tiles[$t])) {
        $tile = $tiles[$t];
        $index = array_search($t, array_keys($tiles));
    } else {
        header("Location: /latest");
        die();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>r/place Archive</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/dark.css">
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style> @media (max-width: 700px) { .mobilehide { display:none; } #canvas-img { padding-top: 80px !important; } #statusbar { text-align: center; } } @media (min-width: 700px) { .mobileonly { display:none; } } </style>
    <link rel="shortcut icon" href="/logo.png" type="image/png">
</head>
<body>
    <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/navigation.home.php"; ?>

    <div class="modal fade" id="about-box">
        <div class="modal-dialog">
            <div class="modal-dialog">
                <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/about.php"; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="intro-box">
        <div class="modal-dialog">
            <div class="modal-dialog">
                <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/intro.php"; ?>
            </div>
        </div>
    </div>

    <div id="canvas">
        <img id="canvas-img" src="<?= $tile["path"] ?>" style="display:block;margin-left:auto;margin-right:auto;max-height:calc(100vh - 24px);cursor:crosshair;padding-top:66px;padding-left:0;image-rendering: pixelated;transform:scale(1);transition: transform 200ms, padding-top 200ms, padding-left 200ms;max-width:100vw;">

        <div id="statusbar" style="height:24px;border-top:1px solid rgba(0, 0, 0, .25);font-size: 12px;padding: 3px;background:white;position:fixed;bottom:0;width:100%;">
            This is what <a href="https://reddit.com/r/place" target="_blank">r/place</a> looked like <?php

                $date = date("F, jS", mktime(0, 0, 0, $tile["date_parsed"]["month"], $tile["date_parsed"]["day"]));
                if ("on <b>" . $date . "</b> at <b>" . $tile["date_parsed"]["hour"] . ":" . sprintf("%02.0f", $tile["date_parsed"]["minute"]) . "</b>" === "on <b>April, 5th</b> at <b>0:02</b>") {
                    echo "<b>when r/place ended</b>";
                } else {
                    echo "on <b>" . $date . "</b> at <b>" . $tile["date_parsed"]["hour"] . ":" . sprintf("%02.0f", $tile["date_parsed"]["minute"]) . "</b>";
                }

                ?>
            <span id="coordinates" style="float: right;font-family: monospace;"></span>
        </div>
    </div>

    <script>
        function checkbox() {
            if (document.getElementById("dontShowAgain").checked) {
                localStorage.setItem("dontShowAgain", "true");
            } else {
                localStorage.removeItem("dontShowAgain");
            }
        }

        function fix2(n) {
            s = n.toString();
            if (s.length >= 2) {
                return s;
            } else if (s.length >= 1) {
                return "0" + s;
            } else if (s.length >= 0) {
                return "00";
            }
        }

        function fix4(n) {
            s = n.toString();
            if (s.length >= 4) {
                return s;
            } else if (s.length >= 3) {
                return "0" + s;
            } else if (s.length >= 2) {
                return "00" + s;
            } else if (s.length >= 1) {
                return "000" + s;
            } else if (s.length >= 0) {
                return "0000";
            }
        }

        let positionX = -1;
        let positionY = -1;
        let scaleLvl = 1;

        $(document).ready(function() {
            let img = $("#canvas-img");

            img.on("mousemove", function(event) {
                let x = event.pageX - this.offsetLeft;
                let y = event.pageY - this.offsetTop;
                document.getElementById('coordinates').innerText = "(" + fix4(x) + "; " + fix4(y) + ") " + fix2(scaleLvl) + "x";
                positionX = event.pageX;
                positionY = event.pageY;
            });

            img.on("mouseleave", function(event) {
                document.getElementById('coordinates').innerText = "";
            });

            img.on("dragstart", function(event) {
                event.preventDefault();
                return false;
            });

            img.on('click', (event) => {
                if (event.target !== document.getElementById("canvas-img")) return;

                if (document.getElementById("canvas-img").style.transform === "scale(5)") {
                    document.getElementById("canvas-img").style.transform = "scale(10)";
                    document.getElementById("canvas-img").style.paddingTop = "357px";
                    document.getElementById("canvas-img").style.paddingLeft = "688px";
                    scaleLvl = 10;
                } else if (document.getElementById("canvas-img").style.transform === "scale(10)") {
                    document.getElementById("canvas-img").style.transform = "scale(1)";
                    document.getElementById("canvas-img").style.paddingTop = "66px";
                    document.getElementById("canvas-img").style.paddingLeft = "0";
                    scaleLvl = 1;
                } else {
                    document.getElementById("canvas-img").style.transform = "scale(5)";
                    document.getElementById("canvas-img").style.paddingTop = "324px";
                    document.getElementById("canvas-img").style.paddingLeft = "604px";
                    scaleLvl = 5;
                }

                let x = event.pageX - document.getElementById('canvas-img').offsetLeft;
                let y = event.pageY - document.getElementById('canvas-img').offsetTop;
                document.getElementById('coordinates').innerText = "(" + fix4(x) + "; " + fix4(y) + ") " + fix2(scaleLvl) + "x";
                positionX = event.pageX;
                positionY = event.pageY;
            })
        });
    </script>
</body>
</html>