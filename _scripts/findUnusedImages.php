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

$imagePath = __DIR__ . "/../source/assets/img/blog/";

$images = readFiles($imagePath);

$blogPath = __DIR__ . "/../source/_reiseblog/";

foreach(readFiles($blogPath) as $entry) {
	$content = file_get_contents($blogPath . $entry);
	foreach($images as $key => $image) {
		if (strpos($content, $image) !== false) {
			unset($images[$key]);
		}
	}
}

$postsPath = __DIR__ . "/../source/_posts/";

foreach(readFiles($postsPath) as $entry) {
	$content = file_get_contents($postsPath . $entry);
	foreach($images as $key => $image) {
		if (strpos($content, $image) !== false) {
			unset($images[$key]);
		}
	}
}

var_dump($images);