<?php

foreach ( scandir('_posts/de/reiseblog') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $fileName = '_posts/de/reiseblog/' . $file;

    $content = file_get_contents($fileName);

    $content = preg_replace('/layout: de_travelblog\n/', "layout: de_travelblog\nactive_nav: travelblog\n", $content);

    file_put_contents($fileName, $content);
}
