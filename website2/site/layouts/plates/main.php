<?php
$assets = Herbie\Di::get('Assets');
$assets->addCss([
    '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css',
    'https://fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C700italic%2C400%2C300%2C700%2C800&#038;ver=3.8.1',
    '@asset/default/css/pure-min.css',
    '@asset/default/css/styles.css',
    '@asset/default/css/colors.css'
], [], null, false, 0);
$assets->addJs('@asset/default/js/scripts.js');
?>
<!DOCTYPE html>
<html lang="{{ site.language }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="{{ page.meta.robots ? page.meta.robots : 'index, follow' }}">
    <title>{{ pagetitle({delim:' / ', siteTitle:'Herbie Flat-File CMS & Blog', rootTitle: 'Flat-File CMS und Blog - Herbie', reverse:true}) }}</title>
    <link rel="alternate" type="application/rss+xml" title="RSS-Feed" href="<?= $baseUrl ?>/feed.rss">
    <?php $assets->outputCss() ?>
    <?php /* @todo: actually we can't add stylesheets without publishing them */ ?>
    <?php $assets->addCss(['@asset/default/css/pure-grids-responsive-old-ie-min.css', '@asset/default/css/pure-grids-responsive-min.css']) ?>
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/default/css/pure-grids-responsive-old-ie-min.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/default/css/pure-grids-responsive-min.css">
    <!--<![endif]-->
</head>
<body class="{{ bodyclass() }}">
<div class="header">
    <div class="container">
        <div class="inner-container">
            <div class="pure-hidden-tablet pure-hidden-desktop">
                <a href="#menu" id="menuLink" class="menu-link">
                    <!-- Hamburger icon -->
                    <span></span>
                </a>
            </div>
            <div class="pure-g">
                <div class="pure-u-3-5 pure-u-md-1-5">
                    <h1 class="brand-title"><?= $this->link('index', '<span class="e">E</span> Herbie') ?></h1>
                </div>
                <div class="pure-u-2-5 pure-u-md-4-5 text-right pure-hidden-phone">
                    <?php $this->insert('partials/menu') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->insert('partials/mobile_menu') ?>

<?= $this->section('content')?>
<?/*= $this->content(0) */?>

<div class="footer">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1 pure-u-md-1-4 footer-about">
                <h3>Über Herbie</h3>
                <p>Herbie ist ein Flat-File CMS und Blog. Es basiert auf einfachen Markdown- und Textile-Dateien, bietet volle Composer-Unterstützung, ist schnell installiert und einfach erweiterbar.</p>
            </div>
            <div class="pure-u-1 pure-u-md-1-4">
            </div>
            <div class="pure-u-1 pure-u-md-1-4 footer-links">
                <h3>Links</h3>
                <ul>
                    <li><?= $this->link('sitemap', 'Sitemap') ?></li>
                    <li><?= $this->link('kontakt', 'Kontakt') ?></li>
                    <li><?= $this->link('impressum', 'Impressum') ?></li>
                    <li><?= $this->link('datenschutz', 'Datenschutz') ?></li>
                </ul>
            </div>
            <div class="pure-u-1 pure-u-md-1-4">
                <h3>Code</h3>
                <ul>
                    <li><a target="_blank" href="https://www.github.com/getherbie">Herbie bei GitHub</a></li>
                    <li><a target="_blank" href="https://packagist.org/packages/getherbie/">Herbie bei Packagist</a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="pure-g">
            <div class="pure-u-1 footer-copyright">
                &copy; <?= date('Y') ?> Handgemacht mit Herbie
            </div>
        </div>
    </div>
</div>

<?php $assets->outputJs() ?>

</body>
</html>