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

function getImgSrcAndAlt($captionImg, $fileName): string
{
    preg_match('/src="[\/{}A-z0-9.&; \-]{0,}"/', $captionImg, $matchesSrc);
    if (empty($matchesSrc)) {
        echo "ERROR in ";
        die();
    }

    $imgSrcData = $matchesSrc[0];
    preg_match('/alt="[A-z!?0-9#.,&; \-]{0,}"/', $captionImg, $matchesAlt);
    if (empty($matchesAlt)) {
        echo "ERROR2 in ";
        echo $fileName;
        var_dump($captionImg);
        die();
    }

    $imgAltData = $matchesAlt[0];

    return $imgSrcData . ' ' . $imgAltData;
}

function fixSpecialChars($content)
{
    $content = replaceContent($content, 'ä', '&auml;');
    $content = replaceContent($content, 'Ä', '&Auml;');
    $content = replaceContent($content, 'ö', '&ouml;');
    $content = replaceContent($content, 'Ö', '&Ouml;');
    $content = replaceContent($content, 'ü', '&uuml;');
    $content = replaceContent($content, 'Ü', '&Uuml;');
    $content = replaceContent($content, 'ß', '&szlig;');
    $content = replaceContent($content, '€', '&euro;');

    return $content;
}


$blogPath = __DIR__ . "/../source/_reiseblog/";

foreach(readFiles($blogPath) as $fileName) {
	$content = file_get_contents($blogPath . $fileName);
    $content = fixSpecialChars($content);

    file_put_contents($blogPath . $fileName, $content);
}

//var_dump($count);
//die();