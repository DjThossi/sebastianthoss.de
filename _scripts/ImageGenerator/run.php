<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Orbitale\Component\ImageMagick\Command;
use Orbitale\Component\ImageMagick\ReferenceClasses\Geometry;



//LOAD LIST OF NEW IMAGES
echo "LOAD LIST OF NEW IMAGES\n";

function readFiles($directory) {
    $excludedFiles = ['.', '..', '.gitkeep', '.DS_Store'];

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
sort($inputImages);

if (count($inputImages) == 0) {
	echo("Nothing found");die;
}

$outputPath = __DIR__ . '/output/';
$outputSmallPath = __DIR__ . '/output/small/';




// CONVERT ALL IMAGES
echo "CONVERT ALL IMAGES\n";

foreach ($inputImages as $image) {
    //Large output image
    $command = new Command();
    $response = $command
        ->convert($inputPath . $image)
        ->resize(new Geometry(1024))
        ->file($outputPath . $image, false)
        ->run();

    if ($response->hasFailed()) {
        echo "SOMETIHNG WENT WRONG!\n";
        die($response->getError());
    }

    //Small output image
    $command = new Command();
    $response = $command
        ->convert($inputPath . $image)
        ->resize(new Geometry(455))
        ->file($outputSmallPath . $image, false)
        ->run();

    if ($response->hasFailed()) {
        echo "SOMETIHNG WENT WRONG!\n";
        die($response->getError());
    }
}





//LOAD LIST OF OUTPUT IMAGES
echo "LOAD LIST OF OUTPUT IMAGES\n";

$outputImages = readFiles($outputPath);

$images = array_intersect($inputImages, $outputImages);

if (count($images) === 0 ) {
    echo "NOTHING TO MOVE\n";
    die ($returnValue);
}



//MOVE IMAGES
echo "MOVE IMAGES\n";

$largePath = __DIR__ . '/../../source/assets/img/blog/2018-philippines/';
$smallPath = __DIR__ . '/../../source/assets/img/blog/2018-philippines/small/';

foreach ($images as $image) {
    unlink($inputPath . $image);
    rename($outputPath . $image, $largePath . $image);
    rename($outputSmallPath . $image, $smallPath . $image);
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
file_put_contents(__DIR__ . '/../../source/_includes/imageData/2018-philippines.csv', $content);




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