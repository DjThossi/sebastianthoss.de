<?php

foreach ( scandir('_posts/de/reiseblog') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $fileName = '_posts/de/reiseblog/' . $file;

    $content = file_get_contents($fileName);

    $content = preg_replace('/layout: de_travel-blog\n/', "layout: de_travel-blog\nactive_nav: travel-blog\n", $content);

    file_put_contents($fileName, $content);
}
