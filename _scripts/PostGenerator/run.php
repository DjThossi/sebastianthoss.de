<?php
/** @noinspection PhpUnhandledExceptionInspection */
$blogType = "_familie";
$template = file_get_contents(__DIR__ . "/templates/{$blogType}.html.twig");

require_once (__DIR__ . '/File.php');

$file = new File(__DIR__ . '/text.txt');

$stop = false;

$title = $file->getNextLine();
$didMatch = preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4}) (.*)/', $title, $dateParts);

if ($didMatch === 1) {
    $day = strlen($dateParts[1]) === 1 ? '0' . $dateParts[1] : $dateParts[1];
    $month = strlen($dateParts[2]) === 1 ? '0' . $dateParts[2] : $dateParts[2];
    $year = strlen($dateParts[3]) === 2 ? '20' . $dateParts[3] : $dateParts[3];
    $titleForFile = $dateParts[4];
} elseif (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\. (.*)/', $title, $dateParts) === 1) {
    $day = strlen($dateParts[1]) === 1 ? '0' . $dateParts[1] : $dateParts[1];
    $month = strlen($dateParts[2]) === 1 ? '0' . $dateParts[2] : $dateParts[2];
    $year = '2013';
    $titleForFile = $dateParts[3];
} else {
    throw new Exception('Date not found');
}

$nameReplace = [
    '&' => 'und',
    ' - ' => '-nach-',
    ' ' => '-',
    'ä' => 'ae',
    'ö' => 'oe',
    'ü' => 'ue',
    'Ä' => 'ae',
    'Ö' => 'oe',
    'Ü' => 'ue',
    'ß' => 'ss',
];

$fileName = $year . '-' . $month . '-' . $day . '-' . urlencode(
    strtolower(
        str_replace(
            array_keys($nameReplace),
            array_values($nameReplace),
            $titleForFile
        )
    )) . '.html.twig';

$replace = [
    '{#title#}' => $title,
];

do {
    $line = $file->getNextLine();
}
while (strlen($line) === 0);

$cutPosition = strrpos(substr($line, 0, 150), ' ');
$intro = trim(substr($line, 0, $cutPosition));
$replace['{#intro#}'] = $intro;

$afterIntro = trim(substr($line, $cutPosition));
$replace['{#afterIntro#}'] = $afterIntro;

$additionalNextToImage = '';
try {
    while ($file->getCharCount() < 1300) {
        do {
            $line = $file->getNextLine();
        }
        while (strlen($line) === 0);

        $additionalNextToImage .= prepareLine($line, $file);
    }
} catch (UnexpectedValueException $e) {
    $stop = true;
}
$replace['{#additionalNextToImage#}'] = $additionalNextToImage;

//Add first col-12
$additionalContent = '';
if ($stop === false) {
    $snippet = file_get_contents(__DIR__ . '/templates/md-12.html.twig');
    $snippetContent = '';

    try {
        for ($i = 1; $i <= 2; $i++) {
            do {
                $line = $file->getNextLine();
            }
            while (strlen($line) === 0);

            $snippetContent .= prepareLine($line, $file);
        }

        if ($file->doesAnotherImageFit() === false) {
            while (true) {
                do {
                    $line = $file->getNextLine();
                }
                while (strlen($line) === 0);

                $snippetContent .= prepareLine($line, $file);
            }
        }
    } catch (UnexpectedValueException $e) {
        $stop = true;
    }

    if ($snippetContent !== '') {
        $additionalContent .= str_replace('{#content#}', rtrim($snippetContent), $snippet);
    }
}

//Add second image
if ($stop === false) {
    $snippet = file_get_contents(__DIR__ . '/templates/md-6-left.html.twig');
    $snippetContent = '';

    $maxCount = $file->getCharCount() + 1400;

    try {
        while ($file->getCharCount() < $maxCount) {
            do {
                $line = $file->getNextLine();
            }
            while (strlen($line) === 0);

            $snippetContent .= prepareLine($line, $file);
        }
    } catch (UnexpectedValueException $e) {
        $stop = true;
    }

    if ($snippetContent !== '') {
        $additionalContent .= str_replace('{#content#}', rtrim($snippetContent), $snippet);
    }
}

//Add another col-12
if ($stop === false) {
    $snippet = file_get_contents(__DIR__ . '/templates/md-12.html.twig');
    $snippetContent = '';

    try {
        while (true) {
            do {
                $line = $file->getNextLine();
            }
            while (strlen($line) === 0);

            $snippetContent .= prepareLine($line, $file);
        }
    } catch (UnexpectedValueException $e) {
        $stop = true;
    }

    if ($snippetContent !== '') {
        $additionalContent .= str_replace('{#content#}', rtrim($snippetContent), $snippet);
    }
}


$replace['{#additionalContent#}'] = $additionalContent;

$template = str_replace(array_keys($replace), array_values($replace), $template);

// Write file
file_put_contents(__DIR__ . "/../../source/{$blogType}/{$fileName}", $template);






function prepareLine(string $line, File $file): string
{
    if (strpos($line, '-') === 0) {
        $snippet = file_get_contents(__DIR__ . '/templates/ul.html.twig');
        $snippetContent = '';

        try {
            while (strpos($line, '-') === 0) {
                $snippetContent .= sprintf("            <li>%s</li>\n", trim(substr($line, 1)));

                $line = $file->getNextLine();
            }
        } catch (UnexpectedValueException $e) {
            return str_replace('{#content#}', $snippetContent, $snippet);
        }

        $snippet = str_replace('{#content#}', rtrim($snippetContent), $snippet);

        if (strlen($line) > 0) {
            $snippet .= sprintf("        <p>\n            %s\n        </p>\n", $line);
        }

        return $snippet;
    }

    return sprintf("        <p>\n            %s\n        </p>\n", $line);
}