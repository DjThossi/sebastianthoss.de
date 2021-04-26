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

function replaceRegex($content, $regexString, $replaceString) {
    return preg_replace($regexString, $replaceString, $content);
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

    $imgCount = substr_count($content, '<img ');
    if ($imgCount > 1) {
        continue;
    }

    var_dump($fileName);
    continue;

    $content = replaceContent($content, $searchString, $replaceString);

    file_put_contents($blogPath . $fileName, $content);
}
