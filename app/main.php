<?php

declare(strict_types=1);

$serverPath = __DIR__."/server.php";
exec('php'.' '.$serverPath.' > /dev/null &');