<?php

#echo"<pre>";print_r($menuItems->find("parent="));echo"</pre>";

/*
$menuItem = (new MenuItems)->get("route=dokumentation");

while ($next = $menuItem->next("hidden=0")) {
    echo $menuItem["title"] . "<br>";
    $menuItem = $next;
}
echo $menuItem["title"] . "<br>";

echo "<br>UND ZURUECK<br>";

while ($prev = $menuItem->prev("hidden=0")) {
    echo $menuItem["title"] . "<br>";
    $menuItem = $prev;
}
echo $menuItem["title"] . "<br>";

echo $menuItem["title"];
foreach ($menuItem->find() as $item) {
    echo $item["title"] . "<br>";
}

foreach ($menuItems as $menuItem) {
    echo $menuItem["title"] . "<br>";
}
#echo"<pre>";print_r($menuItems);echo"</pre>";

$menuItem = $menuItems->last();

$parents = $menuItem->parents();
foreach ($parents as $menuItem) {
    echo $menuItem["title"] . "<br>";
}

echo "<hr>";

$menuItem = (new MenuItems)->get("route=");
echo $menuItem["title"];
foreach ($menuItem->children("hidden<1") as $child1) {
    echo $child1["title"] . "<br>";
    foreach ($child1->children("hidden<1") as $child2) {
        echo "---" . $child2["title"] . "<br>";
    }
}

$menuItem = (new MenuItems)->get("route=");
while (($child = $menuItem->child())) {
    echo " // " . strtoupper($child["title"]);
    $menuItem = $child;
}

while ($menuItem->parent()) {
    $menuItem = $menuItem->parent();
    var_dump($menuItem["title"]);
}
*/

#echo "<br>" . get_class($menuItem);

#var_dump($menuItems->first());

#echo"<pre>";print_r($menuItems->get("parent="));echo"</pre>";


#$pages = PagesTest::create();

/*
$test1 = new Test1();
print_r($test1->getStaticArray());
$test2 = new Test2();
print_r($test2->getStaticArray());


$this->traverseMenuArray();
*/

/*
$parent = "__root__";
foreach (PagesTest::$tree[$parent] as $page) {
    echo $page["title"] . "<br>";
}
echo"<pre>";print_r(PagesTest::$tree);echo"</pre>";
*/


/*
$pages = PagesTest::create();
foreach ($pages->find("parent=") as $child1) {
    echo "1 " . $child1["title"] . "<br>";
    if ($child1->hasChildren()) {
        foreach ($child1->children() as $child2) {
            echo "2 --" . $child2["title"] . "<br>";
            if ($child2->hasChildren()) {
                foreach ($child2->children() as $child3) {
                    echo "3 ----" . $child3["title"] . "<br>";
                    if ($child3->hasChildren()) {
                        foreach ($child3->children() as $child4) {
                            echo "4 ------" . $child4["title"] . "<br>";
                            if ($child4->hasChildren()) {
                                foreach ($child4->children() as $child5) {
                                    echo "5 --------" . $child5["title"] . "<br>";
                                    if ($child5->hasChildren()) {
                                        foreach ($child5->children() as $child6) {
                                            echo "6 ----------" . $child6["title"] . "<br>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
echo "<hr>";
*/

#echo"<pre>";print_r($pages);echo"</pre>";exit;

#$this->menu = $this->loadMenu();


$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("hidden<1");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("hidden=0");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("route*=dokumentation");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("path=test");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("route=sitemap");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("parent=");
$Menu = Menu::create()->find("route=datenschutz");
$Menu = Menu::create()->find("hidden<1");

foreach ($Menu as $item) {
    echo $item["title"] . "<br>";
}



$pages = Pages::create()->find("parent=");

$pages = Pages::create()->find("parent=");
foreach ($pages as $page) {
    echo $page->get("content");
    echo "<hr>";
}


$page = (new Pages)->get("blog/02-04-herbie-ist-da");
$page = (new Pages)->get("@page/blog/2014-02-04-herbie-ist-da.txt");

echo $page->get("content");
echo $page->get("boxes");
echo $page->get("footer");

echo"<pre>";print_r($page);echo"</pre>";exit;


echo (new Pages)->count("parent=");

$pages = (new Pages)->find("parent=");
foreach ($pages as $page) {
    #echo $page->get("content");
    #echo "<hr>";
}

#$test = $this->menu->find("layout!=default.html");
#$test = $this->menu->find("hidden<1");
$menu = $this->menu
    ->find("route^=dokumentation")
    ->find("hidden<1")
    ;

$menu = $this->menu->find("parent=");
$menu = $this->menu->find("route=");

$menu = $this->menu
    ->find('route^=blog')
    ;//->sort("-path");

#$menu = $menu->limit(3);
//->find("text/html", "content_type")
#echo"<pre>";print_r($menu);echo"</pre>";exit;

#echo"<pre>";print_r($this->menu);echo"</pre>";exit;
