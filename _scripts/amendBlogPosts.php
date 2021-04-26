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

    $imgCount = substr_count($content, '<img ');
    if ($imgCount > 1) {
        continue;
    }

    preg_match('/\[caption .*\[\/caption\]/', $content, $matches);
    preg_match('/src=".*" alt="[A-z0-9.&; \-]{0,}"/', $matches[0], $matches);
    $imgData = $matches[0];

    $content = replaceRegex($content, '/\[caption .*\[\/caption\]/', '');
    $content = replaceRegex($content, '/<p>[ \t\n]{0,}<\/p>\n/', '');

    $contentStart = strpos($content, '---', 5) + 4;

    $markup = '<div class="row margin-bottom-10">
  <div class="col-md-5 margin-bottom-10">
    <img class="img-bordered img-responsive img-center" %s
    />
  </div>
  <div class="col-md-7">
    %s
  </div>
</div>';

    $markup = sprintf(
        $markup,
        $imgData,
        substr($content, $contentStart)
    );

    $content = substr($content, 0, $contentStart) . $markup;

    file_put_contents($blogPath . $fileName, $content);
}
