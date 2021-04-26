<?php

function readFiles($directory) {
    $excludedFiles = ['.', '..', '.gitkeep'];

    $files = [];
    foreach ( scandir($directory) as $file) {
        if (in_array($file, $excludedFiles) || is_dir($directory . $file)) {
            continue;
        }

        $files[] = $file;
    }

    return $files;
}

function insertAfterContent ($content, $searchString, $addContent) {
    $position = strpos($content, $searchString);

    if ($position === false) {
        return $content;
    }

    $position += strlen($searchString);

    return substr($content, 0, $position) . $addContent . substr($content, $position);
}

function hasContent ($content, $searchString) {
    $position = strpos($content, $searchString);

    if ($position === false) {
        return false;
    }

    return true;
}

function replaceContent ($content, $searchString, $replaceString) {
    $position = strpos($content, $searchString);

    if ($position === false) {
        return $content;
    }

    return str_replace($searchString, $replaceString, $content);
}

function replaceRegex($content, $regexString, $replaceString, $limit = 1) {
    return preg_replace($regexString, $replaceString, $content, $limit);
}

$blogPath = __DIR__ . "/../source/_reiseblog/";

$searchString = 'active_nav:';

$addContent = '
active_nav:';

$fromDate = strtotime('2012-09-16');
$toDate = strtotime('2013-06-09');

foreach(readFiles($blogPath) as $fileName) {
    $date = strtotime(substr($fileName, 0, 10));
    if ($date < $fromDate || $date > $toDate) {
        continue;
    }

	$content = file_get_contents($blogPath . $fileName);
    if (hasContent($content, 'sitemap: false') === false) {
        continue;
    }

    preg_match_all('/\/([0-9A-z\-_]{1,}\.jpg)/', $content, $matches);
    foreach($matches[1] as $imgName) {
        checkImage($imgName, $fileName);
    }

    preg_match_all('/\/([0-9A-z\-_]{1,}\.jpeg)/', $content, $matches);
    foreach($matches[1] as $imgName) {
        checkImage($imgName, $fileName);
    }

    preg_match_all('/\/([0-9A-z\-_]{1,}\.png)/', $content, $matches);
    foreach($matches[1] as $imgName) {
        checkImage($imgName, $fileName);
    }

    preg_match_all('/\/([0-9A-z\-_]{1,}\.gif)/', $content, $matches);
    foreach($matches[1] as $imgName) {
        checkImage($imgName, $fileName);
    }
}

function checkImage($imgName, $fileName) {
    $fileExists = file_exists(__DIR__ . '/../source/assets/img/blog/auszeit/small/' . $imgName);
    if ($fileExists) {
        return;
    }
    if (preg_match('/[0-9]{8}-[0-9]{6}/', $imgName)) {
        $imgUrl = "https://sebastian301082.files.wordpress.com/" . substr($imgName, 0,4) . '/' . substr($imgName, 4, 2) . '/' . $imgName;

        file_put_contents(__DIR__ . '/ImageGenerator/input/' . $imgName, file_get_contents($imgUrl));

    } else {

        for($i = 0; $i < 10; $i++) {
            $imgUrl = "https://sebastian301082.files.wordpress.com/2016/0" . $i . '/' . $imgName;
            $fileGetContents = @file_get_contents($imgUrl);
            if ($fileGetContents === false) {
                if ($i < 9) {
                    continue;
                }

                echo $imgName . " $fileName\n";
                return;
            }
            file_put_contents(__DIR__ . '/ImageGenerator/input/' . $imgName, $fileGetContents);
            break;
        }

    }
}