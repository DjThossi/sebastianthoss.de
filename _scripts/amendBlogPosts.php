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

$count = 0;
foreach(readFiles($blogPath) as $fileName) {
    $date = strtotime(substr($fileName, 0, 10));
    if ($date < $fromDate || $date > $toDate) {
        continue;
    }

	$content = file_get_contents($blogPath . $fileName);
    if (hasContent($content, 'sitemap: false') === false) {
        continue;
    }

    $count++;
//    continue;

    $imgCount = substr_count($content, '[caption ');
    if ($imgCount < 1) {
        continue;
    }

    $content = replaceContent($content, 'sitemap: false', 'sitemap: true');
    $content = replaceRegex($content, '/  external_link: .*\n/', '');

    preg_match('/\[caption .*\[\/caption\]/', $content, $matches);
    preg_match('/src="[\/{}A-z0-9.&; \-]{0,}"/', $matches[0], $matchesSrc);
    if(empty($matchesSrc)) {
        echo "ERROR in ";
        echo $fileName;
        die();
    }

    $imgSrcData = $matchesSrc[0];
    preg_match('/alt="[A-z!?0-9.,&; \-]{0,}"/', $matches[0], $matchesAlt);
    if(empty($matchesAlt)) {
        echo "ERROR2 in ";
        echo $fileName;
        die();
    }

    $imgAltData = $matchesAlt[0];
    $imgData = $imgSrcData . ' ' . $imgAltData;

    $content = replaceRegex($content, '/\[caption .*\[\/caption\]/', '');
    $content = replaceRegex($content, '/<p>[ \t\n]{0,}<\/p>\n/', '');

    $contentStart = strpos($content, '---', 5) + 4;

    $markupTemplate = '<div class="row margin-bottom-10">
  <div class="col-md-5 margin-bottom-10">
    <img class="img-bordered img-responsive img-center" %s
    />
  </div>
  <div class="col-md-7">
    %s
  </div>
</div>

<div class="row margin-bottom-10">
  <div class="col-md-5 visible-sm visible-xs margin-bottom-10">
    <img class="img-bordered img-responsive img-center"
         
    />
  </div>
  <div class="col-md-7">
    
  </div>
  <div class="col-md-5 hidden-sm hidden-xs">
    <img class="img-bordered img-responsive img-center"
         
    />
  </div>
</div>

<div class="row margin-bottom-10">
  <div class="col-md-5 margin-bottom-10">
    <img class="img-bordered img-responsive img-center"
    
    />
  </div>
  <div class="col-md-7">
    
  </div>
</div>

';

    $markup = sprintf(
        $markupTemplate,
        $imgData,
        substr($content, $contentStart)
    );

    $content = substr($content, 0, $contentStart) . $markup;

//    var_dump($content);
//    die();

    file_put_contents($blogPath . $fileName, $content);
    break;
}

var_dump($count);
die();