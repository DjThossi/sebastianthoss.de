<?php
// CONVERT ALL IMAGES
echo "CONVERT ALL IMAGES\n";

$converterPath = __DIR__ . '/converter';

$output = null;
$returnValue = null;
exec("cd $converterPath && npm install && node convert", $output, $returnValue);

foreach($output as $line) {
    echo $line . "\n";
}

if ($returnValue !== 0) {
    echo "SOMETIHNG WENT WRONG!\n";
    die ($returnValue);
}



//LOAD LIST OF NEW IMAGES
echo "LOAD LIST OF NEW IMAGES\n";

function readFiles($directory) {
    $excludedFiles = ['.', '..', '.gitkeep'];

    $files = [];
    foreach ( scandir($directory) as $file) {
        if (in_array($file, $excludedFiles)) {
            continue;
        }

        $files[] = $file;
    }

    return $files;
}

$inputPath = __DIR__ . '/input/';
$inputImages = readFiles($inputPath);

$outputPath = __DIR__ . '/output/';
$outputImages = readFiles($outputPath);

$images = array_intersect($inputImages, $outputImages);

if (count($images) === 0 ) {
    echo "NOTHING TO MOVE\n";
    die ($returnValue);
}



//MOVE IMAGES
echo "MOVE IMAGES\n";

$largePath = __DIR__ . '/../../source/assets/img/blog/2017-asia-oz/';
$smallPath = __DIR__ . '/../../source/assets/img/blog/2017-asia-oz/small/';

foreach ($images as $image) {
    rename($inputPath . $image, $largePath . $image);
    rename($outputPath . $image, $smallPath . $image);
}



//READ EXISTING IMAGES DATA
echo "READ EXISTING IMAGES DATA\n";

$existingImagesFile = __DIR__ . '/data/existingImages.php';
@include $existingImagesFile;
if (!isset($existingImages)) {
    $existingImages = [];
}

foreach ($images as $image) {
    $existingImages[$image] = $image;
}




//WRITE CSV DATA
echo "WRITE CSV DATA\n";

$content = null;
foreach (array_reverse($existingImages) as $image) {
    if (!empty($content)) {
        $content .= ', ';
    }
    $content .= $image;
}
file_put_contents(__DIR__ . '/../../source/_includes/image_data.csv', $content);




//WRITE EXISTING IMAGES DATA
echo "WRITE EXISTING IMAGES DATA\n";

$content = '<?php $existingImages = ' . var_export($existingImages, true) . ';';
file_put_contents($existingImagesFile, $content);



//GENERATE PROD
echo "GENERATE PROD\n";

$basePath = __DIR__ . '/../../';

$output = null;
$returnValue = null;
exec("cd $basePath && vendor/bin/sculpin generate --env=prod && git add .", $output, $returnValue);

foreach($output as $line) {
    echo $line . "\n";
}

if ($returnValue !== 0) {
    echo "SOMETIHNG WENT WRONG!\n";
    die ($returnValue);
}