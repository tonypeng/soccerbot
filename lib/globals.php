<?php
$GLOBALS['verbose'] = false;
function set_verbose($verbose) {
    $GLOBALS['verbose'] = $verbose;
}

$GLOBALS['web'] = false;
function set_web($web) {
    $GLOBALS['web'] = $web;
}

function vprint($var) {
    if ($GLOBALS['verbose']) {
        echo $var;
    }
}

function vprintln($var) {
    if ($GLOBALS['verbose']) {
        $linebreak = $GLOBALS['web'] ? '<br />' : "\n";
        echo $var.$linebreak;
    }
}

function extract_hash_tags($text): array {
    $hashtags= false;
    preg_match_all("/(#\w+)/u", $text, $matches);
    if ($matches) {
        $hashtags_arr = array_count_values($matches[0]);
        $hashtags = array_keys($hashtags_arr);
        $hashtags = array_map(function($val) { return mb_substr($val, 1); }, $hashtags);
    }
    return $hashtags;
}

function startsWith(string $str, string $contains) {
    return mb_substr($str, 0, mb_strlen($contains)) == $contains;
}

function natural_language_join(array $list, $conjunction = 'and') {
    $last = array_pop($list);
    if ($list) {
        return implode(', ', $list) . ' ' . $conjunction . ' ' . $last;
    }
    return $last;
}