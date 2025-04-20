<nav class="navbar navbar-expand-sm bg-light" style="display:block;position:fixed;width:100%;top:0;z-index:999;">
    <a class="navbar-brand" href="#" title="r/place Archive" data-toggle="modal" data-target="#about-box">
        <img src="/logo.png" alt="Logo" style="height:40px;">
    </a>
    <div class="navbar-nav" style="display:inline-block;">
            <span class="nav-item" style="display:inline-block;">
                <?php if ($index < count(array_keys($tiles)) && isset(array_keys($tiles)[$index + 1])): ?>
                    <a class="nav-link" href="/<?= array_keys($tiles)[$index + 1] ?>">«</a>
                <?php else: ?>
                    <a class="nav-link disabled" href="#">«</a>
                <?php endif; ?>
            </span>
        <span class="nav-item" style="display:inline-block;">
                <?php if ($_GET['t'] !== "latest"): ?>
                    <a class="nav-link" href="/latest">Now</a>
                <?php else: ?>
                    <a class="nav-link disabled" href="#">Now</a>
                <?php endif; ?>
            </span>
        <span class="nav-item" style="display:inline-block;">
                <?php if ($index >= 1 && isset(array_keys($tiles)[$index - 1])): ?>
                    <a class="nav-link" href="/<?= array_keys($tiles)[$index - 1] ?>">»</a>
                <?php else: ?>
                    <a class="nav-link disabled" href="#">»</a>
                <?php endif; ?>
            </span>
        <span class="nav-item text-danger mobilehide">
                <b>r/place has now ended! <a href="#" data-toggle="modal" data-target="#intro-box">More</a></b>
            </span>
        <span class="nav-item text-danger mobileonly">
                <a href="#" data-toggle="modal" data-target="#intro-box">Read more</a>
            </span>
    </div>
    <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/navigation.pages.php" ?>
</nav>