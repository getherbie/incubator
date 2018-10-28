<?php

ini_set('display_errors', 1);

// load dependencies
require_once(__DIR__ . '/../vendor/autoload.php');

$app = Herbie\App::instance();
$app->init('../site');
$app->timer();

/*
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {}
foreach (herbie_menuitems($app) as $item) {
    echo $item["title"] . "<br>";
}

foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {}
foreach (Herbie\MenuItems::getPages() as $item) {
    echo $item["title"] . "<br>";
}
*/

foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}
foreach (herbie_pages($app) as $page) {}

foreach (herbie_pages($app) as $page) {
    echo $page["title"] . "<br>";
}



echo sprintf("<br>Generated in: %s seconds", $app->timer());
echo sprintf("<br>Used memory: %s bytes", number_format(memory_get_peak_usage(), 0, ".", "'"));

function herbie_menuitems($app) {
    foreach ($app->readFiles("@page", "txt") as $alias) {
        $data = $app->parsePageData($alias, ["title", "route", "path", "parent", "hidden"]);
        yield $data["route"] => new Herbie\MenuItem($data);
    }
    return;
}

function herbie_pages($app)
{
    $files = $app->getFiles("@site", "txt");
    foreach ($files as $alias) {
        $data = $app->parsePageData($alias);
        yield new Herbie\Page($data);
    }
    return;
}

function herbie_pages_normal($app)
{
    $files = $app->getFiles("@page", "txt");
    $items = [];
    foreach ($files as $alias) {
        $data = $app->parsePageData($alias);
        $items[] = new Herbie\Page($data);
    }
    return $items;
}
