<?php $this->layout('main') ?>

<div class="content-container">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-2-3">
                <nav class="breadcrumb">
                    <?= $this->menu_breadcrumb($page, ["homelabel" => '<i class="fa fa-home"></i>', "delim" => "/"]) ?>
                </nav>
                <div class="content">
                    <?= $this->githublink($page) ?>
                    <?= $page->content ?>
                    <?php if (empty($page["link_to_overview"])): ?>
                        <?= $this->pager(
                            $limit='dokumentation',
                            $template='<div class="pagination">{prev} {next}</div>',
                            $linkClass='pure-button',
                            $prevPageLabel='Vorherige Seite',
                            $prevPageIcon='<i class="fa fa-chevron-left"></i>',
                            $nextPageLabel='Nächste Seite',
                            $nextPageIcon='<i class="fa fa-chevron-right"></i>'
                        ); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="sidebar">
                    <?php $this->insert('partials/simplesearch') ?>
                    <h4>Inhalt</h4>
                    <ul>
                        <?php foreach ($menu->find(["parent=dokumentation","hidden<1"]) as $item): ?>
                            <li><a href="<?= $baseUrl ?>/<?= $item["route"] ?>"><?= $item["title"] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
