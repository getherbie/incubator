<?php

ini_set('display_errors', 1);

// load dependencies
require_once(__DIR__ . '/../vendor/autoload.php');

$app = Herbie\App::instance();

$app->timer();

$app->init('../site');

$shortcode = $app->getPlugin("shortcode");

// homepage: box1
$shortcode->add('box1', function ($atts, $content) {
    return '<div class="pure-u-1-2 box box-1"><div markdown="1">'
    . $content
    . '</div></div>';
});

// homepage: box2
$shortcode->add('box2', function ($atts, $content) {
    return '<div class="pure-u-1-2 box box-2"><div markdown="1">'
    . $content
    . '</div></div>';
});

// info
$shortcode->add('info', function ($atts, $content) use ($shortcode) {
    return '<div class="info"><div markdown="1">'
    . $shortcode->parse($content)
    . '</div></div>';
});

// github: readme abrufen, parsen und darstellen
$shortcode->add('githubreadme', function ($attribs) {

    $url = $attribs['0'];
    $content = @file_get_contents($url);
    if ($content === false) {
        $content = "Die Readme-Seite konnte nicht von GitHub geladen werden:<br>{$url}";
    }

    // parse string
    $content = MarkdownPlugin::parseMarkdown($content);

    $replaced = str_replace(
        ['<h1>Herbie ', '<table>'],
        ['<h1>', '<table class="pure-table pure-table-horizontal">'],
        $content
    );

    return '<div class="github-readme">' . $replaced . '</div>';
});

$plates = $app->getPlugin("plates");

$plates->registerFunction("githublink", function($page) {
    $href = 'https://github.com/getherbie/website/blob/master/site/' . str_replace(['@page', '@post'], ['pages', 'posts'], $page->path);
    return '<a class="github-edit-link" href="'. $href . '" title="Fehler entdeckt? Bearbeite den Text auf Github!" target="_blank"><i class="fa fa-2x fa-github"></i></a>';
});

$plates->registerFunction("menu_breadcrumb", function($page, array $options = []) {
    $delim = "";
    $html = "";
    $menuItem = (new \Herbie\MenuItems)->get("route=" . $page->get("route"));
    foreach ($menuItem->parents() as $menuItem) {
        $html .= "<li>" . $delim . $menuItem["title"] . "</li>";
        $delim = " / ";
    }
    $html .= "<li>" . $delim . $page["title"] . "</li>";
    return '<ul class="breadcrumb">' . $html . "</ul>";
});

// run application
echo $app->run();

$time = $app->timer();

echo sprintf("<br>Generated in: %s seconds", $time);
echo sprintf("<br>Used memory: %s bytes", number_format(memory_get_peak_usage(), 0, ".", "'"));

exit;
