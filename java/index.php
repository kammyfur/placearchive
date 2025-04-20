<?php

setcookie("IAmJava", "1", time() + strtotime("1 year"), "/");
header("Location: /");
die();