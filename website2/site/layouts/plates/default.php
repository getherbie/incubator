<?php $this->layout('main') ?>

<div class="content-container">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-2-3">
                <nav class="breadcrumb">
                    {{ menu_breadcrumb({homeurl:'', homelabel: '<i class="fa fa-home"></i>', delim: ' / '}) }}
                </nav>
                <div class="content">
                    {{ githublink() }}
                    <?= $this->content(0) ?>
                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="sidebar">
                    {{ include('includes/simplesearch.html') }}

                    {{ include('@widget/blog/recent_posts.html', {title: 'Letzte Artikel', showDate:false}) }}
                    {{ include('@widget/blog/categories.html', {title: 'Kategorien', showCount:true}) }}
                    {{ include('@widget/blog/tags.html', {title: 'Tags', showCount:true}) }}
                    {{ include('@widget/blog/archives.html', {title: 'Archiv', showCount:true}) }}

                    <aside class="widget widget_text" id="text-3">
                        <h4>HTML5 &amp; CSS3</h4>
                        <div class="textwidget">
                            <a href="http://www.w3.org/html/logo/">
                                <img width="165" height="64" alt="HTML5 Powered with CSS3 / Styling, and Semantics" src="https://www.w3.org/html/logo/badge/html5-badge-h-css3-semantics.png">
                            </a>
                        </div>
                    </aside>

                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}
