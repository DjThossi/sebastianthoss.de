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

echo "COLLECT INFOS FOR SOCIAL\n";
$hasFound = preg_match("/\ntitle:([A-z 0-9\.\-,\(\) &;äöüÜÖÄß€\"\']{1,})\n/", $content, $matches);
if ($hasFound !== 1) {
    echo "[ERROR] TITLE NOT FOUND\n";
    exit(1);
}
$title = trim($matches[1], " \t\n\r\0\x0B\"");

$hasFound = preg_match("/\n[ ]{1,}intro:([A-z 0-9\.\-,\(\) &;äöüÜÖÄß€\"\']{1,})\n/", $content, $matches);
if ($hasFound !== 1) {
    echo "[ERROR] INTRO NOT FOUND\n";
    exit(1);
}
$intro = trim($matches[1], " \t\n\r\0\x0B\"");

echo "SAVE FILE\n";
file_put_contents($travelBlogPath . $fileName, $content);

echo "REMOVE ORIGINAL FILE\n";
unlink($familiePath . $fileName);

echo "TITLE AND INTRO: \n";
echo $title . "\n";
echo $intro . "\n";

exit(0);

echo "GENERATE DEV\n";

$basePath = __DIR__ . '/../';

$output = null;
$returnValue = null;
exec("cd $basePath && vendor/bin/sculpin generate --clean --no-interaction", $output, $returnValue);

foreach($output as $line) {
    echo $line . "\n";
}

if ($returnValue !== 0) {
    echo "SOMETIHNG WENT WRONG!\n";
    die ($returnValue);
}


echo "GENERATE PROD\n";

$output = null;
$returnValue = null;
exec("cd $basePath && vendor/bin/sculpin generate --env=prod --clean --no-interaction && git add .", $output, $returnValue);

foreach($output as $line) {
    echo $line . "\n";
}

if ($returnValue !== 0) {
    echo "SOMETIHNG WENT WRONG!\n";
    die ($returnValue);
}
