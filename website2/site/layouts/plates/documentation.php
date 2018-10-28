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
                    {% if page.link_to_overview %}
                    {% else %}
                        {{ pager(
                            limit='dokumentation',
                            template='<div class="pagination">{prev} {next}</div>',
                            linkClass='pure-button',
                            prevPageLabel='Vorherige Seite',
                            prevPageIcon='<i class="fa fa-chevron-left"></i>',
                            nextPageLabel='Nächste Seite',
                            nextPageIcon='<i class="fa fa-chevron-right"></i>'
                        ) }}
                    {% endif %}
                </div>
            </div>
            <div class="pure-u-1 pure-u-md-1-3">
                <div class="sidebar">
                    {{ include('includes/simplesearch.html') }}
                    <h4>Inhalt</h4>
                    {{ menu_html({route:'dokumentation'}) }}
                </div>
            </div>
        </div>
    </div>
</div>
