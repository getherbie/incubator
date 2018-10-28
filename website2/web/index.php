<?php

ini_set('display_errors', 1);

require_once(__DIR__ . '/../vendor/autoload.php');

define('HERBIE_DEBUG', true);

use Herbie2\Alias;
use Herbie2\Benchmark;
use Herbie2\Menu\Builder;
use Herbie2\Model\Page;

Alias::init([
    '@page' => '/web/www/getherbie/website2/site/pages/'
]);



$page = Page::findOneByPath('@page/2-dokumentation/2-inhalte/4-blogpost-erstellen.md');

echo"<pre>";print_r($page);echo"</pre>";

exit;



Benchmark::mark();

$builder = new Builder(["@page" => "/web/www/getherbie/website2/site/pages"], ["txt", "md", "markdown", "textile", "htm", "html", "rss", "xml"]);
$collection = $builder->buildCollection();
#$node = Herbie2\Menu\Node::buildTree($collection);

/*
echo"<pre>";
print_r($node);
echo"</pre>";

render($node);

function render($node) {
    foreach ($node->getChildren() as $child) {
        if ($child->hasChildren()) {
            render($child);
        }
        echo $child . "<br>";
    }
}
*/

/*echo"<pre>";
print_r($collection);
echo"</pre>";*/

$i = 1;
foreach ($collection as $route => $item) {

    print_r($item->toArray());

    echo $i++ . " / " . $route . " / " . $item . "<br>";
}

$time = Benchmark::mark();
echo sprintf("<br>Generated in %s seconds", $time);

exit;

echo"<pre>";
print_r($collection);
echo"</pre>";
