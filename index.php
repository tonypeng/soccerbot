<?php
require "lib/lib.php";
require "bot.php";

set_verbose(true);
set_web(true);

$bot = new Bot(Config::FB_ACCESS_TOKEN, Config::FB_GROUP_ID);
$bot->runOneCycle();