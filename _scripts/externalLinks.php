<?php

$xml = new DOMDocument();
$success = $xml->load("wordpress.xml");

$links = [];
/** @var DOMElement $item */
foreach ($xml->getElementsByTagName('item') as $item) {

    $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
    $link = $item->getElementsByTagName('link')->item(0)->nodeValue;

    $links[trim($title)] = trim($link);
}

foreach ( scandir('_posts/de/reiseblog') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $fileName = '_posts/de/reiseblog/' . $file;

    $content = file_get_contents($fileName);

    $title = null;
    preg_match("/title: (.*)\n/", $content, $title);
    $title = trim($title[1], "'\"");
    $title = str_replace("''", "'", $title);

    $link = $links[$title];

    $content = preg_replace('/overview:\n/', "overview:\n  external_link: $link\n", $content);

    file_put_contents($fileName, $content);
}
