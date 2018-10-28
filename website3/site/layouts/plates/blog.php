<?php $this->layout('main') ?>

<div class="content-container">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-2-3">
                <nav class="breadcrumb">
                    <?= $this->menu_breadcrumb($page, ["homelabel" => '<i class="fa fa-home"></i>', "delim" => "/"]) ?>
                </nav>
                <div class="content">

                    <?php if ($page->parent == "blog"): ?>

                        <?= $this->githublink($page) ?>

                        <h1> {{ page.title }}</h1>
                        <div class="blog-meta">
                            {{ page.date|strftime("%e. %B %Y") }}
                            {{ include('@widget/blog/links.html', {page: page}) }}
                        </div>
                        <?= $page->content ?>

                        {{ disqus('getherbie') }}

                    <?php else: ?>

                    <div class="posts">

                        <?php $blogs = $pages->find(["route^=blog", "hidden<1"]); ?>

                        <?php if (empty($blogs)): ?>

                            <section class="post">
                                <header class="post-header">
                                    <h2 class="post-title">Blog</h2>
                                </header>
                                <div class="post-description">
                                    <p>Es sind noch keine Blogposts erfasst.</p>
                                </div>
                            </section>

                        <?php else: ?>

                            <?php foreach ($blogs as $blog): ?>

                                <section class="post">
                                    <header class="post-header">
                                        <h2 class="post-title"><?= $this->link($blog->route, $blog->title) ?></h2>
                                    </header>
                                    <div class="blog-meta">
                                        <?= strftime("%e. %B %Y", (int)$blog->date) ?>
                                    </div>
                                    <?php if ($blog->image && is_file($file = "media/" . $blog->image)): ?>
                                        <div class="post-image">
                                            <?= $this->link($blog->route, '<img class="pure-img" src="' . $file . '" alt="" />') ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($blog->excerpt): ?>
                                        <div class="post-description">
                                            <p><?= $blog->excerpt ?></p>
                                        </div>
                                    <?php endif; ?>
                                </section>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

                    <?php endif; ?>

                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="sidebar">
                    <?php $this->insert('partials/simplesearch') ?>
                    {{ include('@widget/blog/recent_posts.html', {title: 'Letzte Artikel', showDate:false}) }}
                    {{ include('@widget/blog/categories.html', {title: 'Kategorien', showCount:true}) }}
                    {{ include('@widget/blog/tags.html', {title: 'Tags', showCount:true}) }}
                    {{ include('@widget/blog/archives.html', {title: 'Archiv', showCount:true}) }}
                </div>
            </div>
        </div>
    </div>
</div>
