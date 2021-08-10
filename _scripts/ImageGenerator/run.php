<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Orbitale\Component\ImageMagick\Command;
use Orbitale\Component\ImageMagick\ReferenceClasses\Geometry;

//TODO Amend here
$folderFileName = '2021-alps';
$generateBothImages = true;
$imageGenerationOnly = false;
$generateProd = false;

//LOAD LIST OF NEW IMAGES
echo "LOAD LIST OF NEW IMAGES\n";

function readFiles($directory)
{
    $excludedFiles = ['.', '..', '.gitkeep', '.DS_Store'];

    $files = [];
    foreach ( scandir($directory, null) as $file) {
        if (in_array($file, $excludedFiles)) {
            continue;
        }

        $files[] = $file;
    }

    return $files;
}

function generateImage(string $inputPath, $image, int $imageLongSidePixels, string $path): void
{
    $command = new Command();
    $response = $command
        ->convert($inputPath . $image)
        ->resize(new Geometry($imageLongSidePixels))
        ->file($path . $image, false)
        ->run();

    if ($response->hasFailed()) {
        echo "SOMETIHNG WENT WRONG!\n";
        die($response->getError());
    }
}

$inputPath = __DIR__ . '/input/';
$inputImages = readFiles($inputPath);
sort($inputImages);

if (count($inputImages) === 0) {
	echo("Nothing found");die;
}

$outputPath = __DIR__ . '/output/';
$outputSmallPath = __DIR__ . '/output/small/';




// CONVERT ALL IMAGES
echo "CONVERT ALL IMAGES\n";

foreach ($inputImages as $image) {
    //Large output image
    if ($generateBothImages) {
        generateImage($inputPath, $image, 1024, $outputPath);
        generateImage($inputPath, $image, 455, $outputSmallPath);
    } else {
        generateImage($inputPath, $image, 455, $outputPath);
    }
}





//LOAD LIST OF OUTPUT IMAGES
echo "LOAD LIST OF OUTPUT IMAGES\n";

$outputImages = readFiles($outputPath);

$images = array_intersect($inputImages, $outputImages);

if (count($images) === 0 ) {
    echo "NOTHING TO MOVE\n";
    die (1);
}



//MOVE IMAGES
echo "MOVE IMAGES\n";

$largePath = __DIR__ . '/../../source/assets/img/blog/' . $folderFileName . '/';
if (!file_exists($largePath)) {
    if (!mkdir($largePath) && !is_dir($largePath)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $largePath));
    }
}

if ($generateBothImages) {
    $smallPath = __DIR__ . '/../../source/assets/img/blog/' . $folderFileName . '/small/';
    if (!file_exists($smallPath)) {
        if (!mkdir($smallPath) && !is_dir($smallPath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $smallPath));
        }
    }
}

foreach ($images as $image) {
    unlink($inputPath . $image);
    rename($outputPath . $image, $largePath . $image);
    if ($generateBothImages) {
        rename($outputSmallPath . $image, $smallPath . $image);
    }
}


if ($imageGenerationOnly) {
    echo "DONE\n";
    exit(0);
}



//READ EXISTING IMAGES DATA
echo "READ EXISTING IMAGES DATA\n";

$existingImagesFile = __DIR__ . '/data/existing-images-' . $folderFileName . '.php';
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
file_put_contents(__DIR__ . '/../../source/_includes/imageData/' . $folderFileName . '.csv', $content);




//WRITE EXISTING IMAGES DATA
echo "WRITE EXISTING IMAGES DATA\n";

$content = '<?php $existingImages = ' . var_export($existingImages, true) . ';';
file_put_contents($existingImagesFile, $content);

if ($generateProd === false) {
    echo "DONE\n";
    exit(0);
}

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