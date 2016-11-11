<?php

foreach ( scandir('_posts/de/reiseblog') as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    $fileName = '_posts/de/reiseblog/' . $file;

    $content = file_get_contents($fileName);
    $content = preg_replace('/tags(.*\n)*---/', '---', $content);

    $content = preg_replace('/layout: post\n/', "layout: de_travelblog\n", $content);
    $content = preg_replace('/categories:\n/', "categories:\n  - de\n  - Reiseblog\nlocations:\n", $content);
    $content = preg_replace('/\n- /', "\n  - ", $content);
    $content = preg_replace('/{{ site.baseurl }}/', "{{ site.github.url }}", $content);

    $intro = null;
    $contentStart = strpos($content, '---', 3) +3 ;
    $morePos = strpos($content, '<!--more-->');
    if ($morePos !== false) {
        $intro = substr($content, $contentStart, $morePos-$contentStart);

        $intro = preg_replace('/\[caption .*\[\/caption\]/', ' ', $intro);
        $intro = preg_replace('/\n/', ' ', $intro);
        $intro = strip_tags($intro);
        $intro = str_replace('"', '', $intro);
        $intro = trim($intro);
        $intro = '"' . $intro . '"';
    }

    $image = null;
    $alt = null;
    $caption = null;
    preg_match('/\[caption.*\](.*)\[\/caption]/', $content, $caption);

    if (count($caption) === 2) {
        preg_match('/<img.*src="([\{ A-z\.\}\/0-9\-]*\.[jpgpnif]{1,4})"/', $caption[1], $image);
        $alt = trim(strip_tags($caption[1]));
        $alt = str_replace('"', '', $alt);
    } else {
        preg_match('/<img.*src="([\{ A-z\.\}\/0-9\-]*\.[jpgpnif]{1,4})"/', $content, $image);
    }

    if (count($image) === 2) {
        $image = $image[1];
        $image = str_replace('{{ site.github.url }}', '', $image);
    } else {
        $image = null;
    }


    $content = preg_replace('/\n---/', "\noverview:\n  intro: $intro\n  image:\n    url: $image\n    alt: $alt\n---", $content);

//continue;




    file_put_contents($fileName, $content);
    //var_dump($content);
    //die;


}