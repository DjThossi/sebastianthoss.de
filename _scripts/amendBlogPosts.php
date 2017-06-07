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

function insertContent ($content, $searchString, $addContent) {
    $position = strpos($content, $searchString);

    if ($position === false) {
        return $content;
    }

    $position += strlen($searchString);

    return substr($content, 0, $position) . $addContent . substr($content, $position);
}

function replaceContent ($content, $searchString, $replaceString) {
    $position = strpos($content, $searchString);

    if ($position === false) {
        return $content;
    }

    return str_replace($searchString, $replaceString, $content);
}

$blogPath = __DIR__ . "/../source/_reiseblog/";

$searchString = 'sitemap: false';

$addContent = '
headline_type: top
pagination:
  previous: true
  next: true';

foreach(readFiles($blogPath) as $fileName) {
	$content = file_get_contents($blogPath . $fileName);

	$content = insertContent($content, $searchString, $addContent);

	file_put_contents($blogPath . $fileName, $content);
}
