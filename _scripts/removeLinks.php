<?php

foreach ( scandir('_posts/de/reiseblog') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $fileName = '_posts/de/reiseblog/' . $file;

    $content = file_get_contents($fileName);

    $content = preg_replace('!<a.*href="http://sebastian301082.*[jpegnif]{1,4}">(.*)</a>!', '$1', $content);

    file_put_contents($fileName, $content);
}
