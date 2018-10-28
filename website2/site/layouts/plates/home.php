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
            <div class="pure-u-1 content lead"><?= $this->content(0) ?></div>
        </div>

        <div class="pure-g content"><?= $this->content('boxes') ?></div>

        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-1-2">
                <div class="boxx boxx-1-2 content"><?= $this->content('footer') ?></div>
            </div>
            <div class="pure-u-1 pure-u-md-1-2">
                <div class="boxx boxx-2-2">
                    <h2>Die letzten Blogposts</h2>
                    {% for item in site.posts.recent(4) %}
                    <p class="post">{{ link(item.route, item.title) }}<br>
                        {{ item.date|strftime("%e. %B %Y") }} &ndash; {{ item.excerpt }}</p>
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
</div>
