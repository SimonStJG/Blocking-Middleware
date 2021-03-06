<?php

/* Tests for the url library */

include "../api/1.2/libs/url.php";

 //one of these days I'll use phpunit

$FAIL = 0;

function compare($input, $expect) {
    global $FAIL;

    $output = normalize_url($input);
    if ($output != $expect) {
        $FAIL ++ ;
        print "FAIL: $input => $output ($expect)\n";
    } else {
        print "  OK: $input => $output ($expect)\n";
    }
}

compare("Http://reddit.com", "http://reddit.com");
compare("http://reddit.com/", "http://reddit.com");
compare("https://reddit.com", "https://reddit.com");
compare("reddit.com", "http://reddit.com");
compare("REDDIT.COM", "http://reddit.com");
compare("reddit.com/r/nsfw", "http://reddit.com/r/nsfw");
compare("Http://reddit.com/r/GoneWild", "http://reddit.com/r/GoneWild");
compare("HTTP://WWW.REDDIT.COM", "http://www.reddit.com");
compare("HTTP://WWW.REDDIT.COM/R/NSFW", "http://www.reddit.com/R/NSFW");
compare("HTTPS://WWW.REDDIT.COM/R/NSFW", "https://www.reddit.com/R/NSFW");

if ($FAIL) {
    exit(1);
}
