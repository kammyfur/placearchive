<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time-lapse | r/place Archive</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/dark.css">
    <script src="/js/jquery-3.6.0.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style> @media (max-width: 700px) { .mobilehide { display:none; } #canvas-img { padding-top: 80px !important; } #statusbar { text-align: center; } } @media (min-width: 700px) { .mobileonly { display:none; } } </style>
    <link rel="shortcut icon" href="/logo.png" type="image/png">
</head>
<body style="overflow: hidden;">
    <?php $title = "Time-lapse"; require_once $_SERVER["DOCUMENT_ROOT"] . "/navigation.php"; ?>

    <div class="modal fade" id="about-box">
        <div class="modal-dialog">
            <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/about.php"; ?>
        </div>
    </div>

    <video muted autoplay loop style="width: 100vw;height: calc(100vh - 66px);margin-top: 66px;background: black;" playsinline>
        <source src="/timelapse.mp4">
        <source src="/timelapse.webm">
    </video>

    <script>
        setInterval(() => {
            console.log(document.getElementsByTagName("video")[0].currentTime);
            if (document.getElementsByTagName("video")[0].currentTime > 3.3 && document.getElementsByTagName("video")[0].currentTime < 50.5) {
                document.getElementsByTagName("video")[0].style.transform = "scale(2)";
            } else {
                document.getElementsByTagName("video")[0].style.transform = "";
            }
        })
    </script>
</body>
</html>