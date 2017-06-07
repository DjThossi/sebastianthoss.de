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

$familiePath = __DIR__ . "/../source/_familie/";
$travelBlogPath = __DIR__ . "/../source/_reiseblog/";

echo "READE FILE\n";
$files = readFiles($familiePath);
sort($files);
$fileName = array_shift($files);

echo "MOVE FIRST FILE\n";
rename($familiePath . $fileName, $travelBlogPath . $fileName);

echo "READ FILE\n";
$content = file_get_contents($travelBlogPath . $fileName);

echo "AMEND FILE\n";
$content = replaceContent($content, 'layout: blog/de/familie', 'layout: blog/de/travel-blog');
$content = replaceContent($content, 'sitemap: false', 'sitemap: true
headline_type: no');

echo "SAVE FILE\n";
file_put_contents($travelBlogPath . $fileName, $content);

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
