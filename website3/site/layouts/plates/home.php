<?php $this->layout('main') ?>

<div class="pure-g slider">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1">
                <h2 class="brand-tagline">Herbie ist ein <strong>einfaches Flat-File CMS- und Blogsystem</strong>, das auf simplen Textdateien basiert. Keine komplizierte Installation, keine Datenbank, nur Textdateien.</h2>
            </div>
        </div>
    </div>
</div>

<div class="content-container">
    <div class="container">
        <div class="pure-g content">
            <div class="pure-u-1 content lead">

                <?= $page->content ?>

            </div>
        </div>

        <div class="pure-g content"><?= $page->content_boxes ?></div>

        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-1-2">
                <div class="boxx boxx-1-2 content"><?= $page->content_footer ?></div>
            </div>
            <div class="pure-u-1 pure-u-md-1-2">
                <div class="boxx boxx-2-2">
                    <h2>Die letzten Blogposts</h2>
                    <?php foreach ($menu->find(["route^=blog","hidden<1","sort=-date","limit=5"]) as $item): ?>
                    <p class="post"><?= $this->link($item["route"], $item["title"]) ?><?php /*<br>
                        <?= strftime("%e. %B %Y", $item["date"]) ?> &ndash; <?= $item["excerpt"] ?>*/ ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
