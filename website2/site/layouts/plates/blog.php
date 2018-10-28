<?php $this->layout('main') ?>

<div class="content-container">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-2-3">
                <nav class="breadcrumb">
                    {{ menu_breadcrumb({homeurl:'', homelabel: '<i class="fa fa-home"></i>', delim: ' / '}) }}
                </nav>
                <div class="content">

                    {% if page is post %}

                        {{ githublink() }}

                        <h1> {{ page.title }}</h1>
                        <div class="blog-meta">
                            {{ page.date|strftime("%e. %B %Y") }}
                            {{ include('@widget/blog/links.html', {page: page}) }}
                        </div>
                        <?= $this->content(0) ?>

                        {{ disqus('getherbie') }}

                    {% else %}

                        {% set items = site.posts.filterItems %}
                        {% set filteredBy = site.posts.filteredBy %}

                        {% if filteredBy %}
                            <div class="posts-filteredby">
                                <span>{{ filteredBy.label }} <strong>{{ filteredBy.value }}</strong>.</span>
                            </div>
                        {% else %}
                            {{ githublink() }}
                        {% endif %}

                        <div class="posts">
                        {% for item in items %}
                            <section class="post">
                                <header class="post-header">
                                    <h2 class="post-title">{{ link(item.route, item.title) }}</h2>
                                </header>
                                <div class="blog-meta">
                                    {{ item.date|strftime("%e. %B %Y") }}
                                </div>
                                {% if item.image %}
                                    <div class="post-image">
                                        <a href="{{ url(item.route) }}"><img class="pure-img" src="{{ item.image|imagine('t560x260') }}" alt="" /></a>
                                    </div>
                                {% endif %}
                                <div class="post-description">
                                    <p>{{ item.excerpt }}</p>
                                </div>
                            </section>
                        {% else %}
                            <section class="post">
                                <header class="post-header">
                                    <h2 class="post-title">Blog</h2>
                                </header>
                                <div class="post-description">
                                    <p>Es sind noch keine Blogposts erfasst.</p>
                                </div>
                            </section>
                        {% endfor %}
                        </div>

                    {% endif %}

                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="sidebar">
                    {{ include('includes/simplesearch.html') }}
                    {{ include('@widget/blog/recent_posts.html', {title: 'Letzte Artikel', showDate:false}) }}
                    {{ include('@widget/blog/categories.html', {title: 'Kategorien', showCount:true}) }}
                    {{ include('@widget/blog/tags.html', {title: 'Tags', showCount:true}) }}
                    {{ include('@widget/blog/archives.html', {title: 'Archiv', showCount:true}) }}
                </div>
            </div>
        </div>
    </div>
</div>
