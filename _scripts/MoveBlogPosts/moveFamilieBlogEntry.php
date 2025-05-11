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

$familiePath = __DIR__ . "/../../source/_familie/";
$travelBlogPath = __DIR__ . "/../../source/_reiseblog/";

echo "READ FILE LIST\n";
$files = readFiles($familiePath);
sort($files);
$fileName = array_shift($files);

echo "READ OLDEST FILE\n";
$content = file_get_contents($familiePath . $fileName);

echo "AMEND FILE\n";
$content = replaceContent($content, 'layout: blog/de/familie', 'layout: blog/de/travel-blog');
$content = replaceContent($content, 'sitemap: false', 'sitemap: true
headline_type: no');

echo "ADD POST TO 2024 IST DXB VACATION\n";
$content = replaceContent($content, '  - Reiseblog', '  - Reiseblog
  - 2024-ist-dxb');

echo "COLLECT INFOS FOR SOCIAL\n";
$hasFound = preg_match("/\ntitle:(.*)\n/", $content, $matches);
if ($hasFound !== 1) {
    echo "[ERROR] TITLE NOT FOUND\n";
    exit(1);
}
$title = trim($matches[1], " \t\n\r\0\x0B\"");

$fileNameParts = explode('-', $fileName);
$link = '/' . array_shift($fileNameParts) . '/' . array_shift($fileNameParts) . '/' . array_shift($fileNameParts) . '/' ;
$link .= implode('-', $fileNameParts);
$link = trim($link, '.twig');

if (!file_exists(__DIR__ . '/../../docs/familie/' . $link)) {
    echo "[ERROR] LINK VALIDATION FAILED. FILE $link NOT FOUND IN /docs/familie \n";
    exit(1);
}

echo "SAVE FILE\n";
file_put_contents($travelBlogPath . $fileName, $content);

echo "REMOVE ORIGINAL FILE\n";
unlink($familiePath . $fileName);

echo "TITLE AND INTRO: \n";
echo $title . "\n";
echo 'http://www.sebastianthoss.de/de/reiseblog' . $link . "\n";

exit(0);

