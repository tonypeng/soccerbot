<?php
require "lib/lib.php";
require "bot.php";

set_verbose(true);
set_web(false);

$bot = new Bot(Config::FB_ACCESS_TOKEN, Config::FB_GROUP_ID);
while(true) {
    try {
        $bot->runOneCycle();
    } catch (Exception $e) {
        echo '[EXCEPTION] '.$e->getMessage()."\n".$e->getTraceAsString();
    }
    sleep(10);
}