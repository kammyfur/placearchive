<nav class="navbar navbar-expand-sm bg-light" style="display:block;position:fixed;width:100%;top:0;z-index:999;">
    <a class="navbar-brand" href="#" title="r/place Archive" data-toggle="modal" data-target="#about-box">
        <img src="/logo.png" alt="Logo" style="height:40px;">
    </a>
    <div class="navbar-nav" style="display:inline-block;">
            <span class="nav-item" style="display:inline-block;">
            <span class="nav-item" style="display:inline-block;">
                <a class="nav-link" style="padding-right:0;margin-right:0;" href="/latest">Now</a>
            </span>
            <span class="nav-item" style="display:inline-block;">
                | <?= $title ?? "r/place archive" ?>
            </span>
            <span class="nav-item text-danger mobilehide">
                <b>r/place has now ended!</b>
            </span>
    </div>
    <?php require_once $_SERVER["DOCUMENT_ROOT"] . "/navigation.pages.php" ?>
</nav>