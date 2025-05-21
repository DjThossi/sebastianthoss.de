<?php
// _scripts/translateResoblog.php

require __DIR__ . '/../vendor/autoload.php';

use Stichoza\GoogleTranslate\GoogleTranslate;

const SRC_DIR = __DIR__ . '/../source/_reiseblog/';
const DST_DIR = __DIR__ . '/../source/_travelblog/';

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    return strtolower($text) ?: 'n-a';
}

function readFiles(string $dir): array
{
    $out = [];
    foreach (scandir($dir) as $f) {
        if (in_array($f, ['.', '..']) || is_dir($dir . $f)) continue;
        if (substr($f, -10) === '.html.twig') $out[] = $f;
    }
    sort($out);
    return $out;
}

function splitFrontmatter(string $raw): array
{
    if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $raw, $m)) {
        return [ $m[1], $m[2] ];
    }
    throw new RuntimeException('Kein Frontmatter gefunden');
}

function parseYamlLines(string $yaml): array
{
    return preg_split("/\r?\n/", trim($yaml));
}

function buildFrontmatter(array $lines): string
{
    return "---\n" . implode("\n", $lines) . "\n---\n\n";
}

if (!@mkdir($concurrentDirectory = DST_DIR, 0755, true) && !is_dir($concurrentDirectory)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
}
$tr = new GoogleTranslate('en', 'de'); // von Deutsch nach Englisch



function translateHtml(string $html, GoogleTranslate $tr): string
{
    // 1) Alle Twig‐Stellen maskieren
    preg_match_all('/(\{\{.*?\}\}|\{%.*?%\})/s', $html, $m);
    $replacements = [];
    foreach ($m[0] as $i => $twigTag) {
        $token = "___TWIG_TOKEN_{$i}___";
        $replacements[$token] = $twigTag;
        $html = str_replace($twigTag, $token, $html);
    }

    // 2) HTML laden
    libxml_use_internal_errors(true);
    $doc = new \DOMDocument('1.0', 'UTF-8');
    $doc->loadHTML('<?xml encoding="utf-8"?>' . $html,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    // 3) Nur Textknoten übersetzen
    $xpath = new \DOMXPath($doc);
    foreach ($xpath->query('//text()') as $textNode) {
        $orig = $textNode->nodeValue;
        if (trim($orig) === '') {
            continue;
        }
        $translated = $tr->translate($orig);
        // sicherstellen, dass vor und nach dem Text ein Leerzeichen steht
        $textNode->nodeValue = ' ' . trim($translated) . ' ';
    }

    // 4) raus mit XML‐Prolog
    $out = $doc->saveHTML();
    $out = preg_replace('/^<\?xml.*?\?>\s*/', '', $out);

    // 5) Twig‐Tokens zurücktauschen
    $out = str_replace(['Twig_token', 'twig_token' ], 'TWIG_TOKEN', $out);
    return strtr($out, $replacements);
}

$notBefore = new DateTime('2018-04-04');

foreach (readFiles(SRC_DIR) as $file) {
    $postDate = new DateTime(substr($file, 0, 10));
    if ($postDate < $notBefore) {
        continue;
    }

    $raw = file_get_contents(SRC_DIR . $file);
    [$yamlBlock, $body] = splitFrontmatter($raw);
    $lines = parseYamlLines($yamlBlock);

    $inLocations = false;
    $translatedTitle = '';

    foreach ($lines as $i => $ln) {
        // 0) Layout-Pfad anpassen: "blog/de/travel-blog" → "blog/en/travelblog"
        if (preg_match('/^layout:\s*blog\/de\/travel-blog\b/', $ln)) {
            $lines[$i] = preg_replace(
                '/blog\/de\/travel-blog\b/',
                'blog/en/travelblog',
                $ln
            );
            continue;
        }

        // 1) Kategorien: "- Reiseblog" → "- travelblog"
        if (preg_match('/^\s*-\s*Reiseblog\s*$/', $ln)) {
            // exakt gleiche Einrückung beibehalten, nur Text ersetzen
            $lines[$i] = preg_replace('/- Reiseblog/', '- travelblog', $ln);
            continue;
        }

        // 2) Kategorien: "de" → "en"
        if (preg_match('/^\s*-\s*de\s*$/', $ln)) {
            $lines[$i] = str_replace('de', 'en', $ln);
            continue;
        }

        // 3) Titel übersetzen
        if (preg_match('/^title:\s*["\']?(.*?)["\']?$/', $ln, $m)) {
            $translatedTitle = $tr->translate($m[1]);
            $lines[$i] = 'title: "' . addslashes($translatedTitle) . '"';
            continue;
        }

        // 3) Locations übersetzen
        if (preg_match('/^\s*locations:\s*$/', $ln)) {
            $inLocations = true;
            continue;
        }
        if ($inLocations) {
            if (preg_match('/^(\s*-\s*)(.+)$/', $ln, $m)) {
                $locTrans = $tr->translate($m[2]);
                $lines[$i] = $m[1] . $locTrans;
                continue;
            }

            // wenn nicht mehr "- " kommt, beenden
            $inLocations = false;
        }

        // 4) overview.intro übersetzen
        if (preg_match('/^\s*intro:\s*["\']?(.*?)["\']?$/', $ln, $m)) {
            $introTrans = $tr->translate($m[1]);
            $lines[$i] = '  intro: "' . addslashes($introTrans) . '"';
            continue;
        }

        // 5) Front-Matter: alt-Attribute übersetzen
        if (preg_match('/^\s*(\S*?\s*)alt:\s*["\']?(.*?)["\']?$/', $ln, $m)) {
            $altTrans       = $tr->translate($m[2]);
            $lines[$i]      = $m[1] . '    alt: "' . addslashes($altTrans) . '"';
            continue;
        }
    }

    // Body komplett übersetzen
    $translatedBody = translateHtml($body, $tr);

    // 6) HTML-Body: alt="…" übersetzen
    $translatedBody = preg_replace_callback(
        '/(alt\s*=\s*)"([^"]+)"/i',
        function ($m) use ($tr) {
            $trans = $tr->translate($m[2]);
            return $m[1] . '"' . addslashes($trans) . '"';
        },
        $translatedBody
    );

    // Neuen Dateinamen bilden
    if (!preg_match('/^(\d{4}-\d{2}-\d{2})-/', $file, $m)) {
        throw new RuntimeException("Dateiname-Pattern passt nicht: $file");
    }
    $newFile = slugify($tr->translate(str_replace('-', ' ', substr($file, 0, -10)))) . '.html.twig';

    // Schreiben
    // … restliches Script bleibt unverändert …
    file_put_contents(DST_DIR . $newFile, buildFrontmatter($lines) . $translatedBody);
    echo "Created: $newFile\n";
}

echo "Fertig.\n";