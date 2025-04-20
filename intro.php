<div class="modal-content">

    <div class="modal-header">
        <h4 class="modal-title">r/place has now ended!</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
        <p>
            On April 1<sup>st</sup> 2022, Reddit (re)introduced <a href="https://reddit.com/r/place" target="_blank">r/place</a>, a gigantic pixel canvas where every Reddit user can place a pixel anywhere every 5 minutes.
        </p>
        <p>
            The goal of this website was to create snapshots of said canvas as often as possible, giving you the possibility to see the evolution of the canvas over time.
        </p>
        <p>
            r/place has ended on April 4<sup>th</sup> 2022, at midnight (UTC); this archive will stay there as an archive. You may now pick one of the options below:
        </p>

        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action disabled">See all the pixel arts done by the My Little Pony fandom <span class="badge badge-warning">Coming soon</span></a>
            <a href="/timelapse" class="list-group-item list-group-item-action">Watch a time-lapse of r/place based on our captures</a>
            <a href="/2022-04-04-22-47-06" class="list-group-item list-group-item-action">View the last good artwork before it was voided</a>
            <a href="/browser" class="list-group-item list-group-item-action">Pick a specific time in r/place's lifespan</a>
        </div>
    </div>

    <div class="modal-footer">
        <div style="margin-right: auto;">
            <input onchange="checkbox()" type="checkbox" name="dontShowAgain" id="dontShowAgain"><label id="dontShowAgain-label" for="dontShowAgain" style="margin-bottom: 0;padding-left:.3em;">Don't show this again</label>
        </div>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>

</div>

<?php if (str_contains($_SERVER['REQUEST_URI'], "latest")): ?>
<script>

    if (localStorage.getItem("dontShowAgain") !== "true") {
        $("#intro-box").modal();
    } else {
        document.getElementById("dontShowAgain").checked = true;
    }

</script>
<?php else: ?>
<style>
    #dontShowAgain, #dontShowAgain-label { display: none; }
</style>
<?php endif; ?>
