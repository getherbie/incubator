---
title: Blogpost erstellen
layout: documentation.html
---

# Blogpost erstellen

Einen Blogpost zu erstellen ist so einfach wie das Erstellen einer normalen
Seite. Du erstellst die Text-Datei einfach im `posts`-Verzeichnis anstatt im
`pages`-Verzeichnis.


## Namenskonvention

Alle Blogposts müssen im `posts`-Verzeichnis abgelegt sein, und der Dateiname
muss der folgenden Namenskonvention folgen, wobei Monat und Jahr zweistellig
geschrieben werden.

    <jahr>-<monat>-<tag>-<name>.<endung>

Hier sind ein paar Beispiele:

    posts
    |- 2014-03-01-das-neue-flat-file-cms.md
    |- 2014-03-13-warum-flat-file-cms-so-schnell-sind.md
    |- 2010-04-04-wordpress-vs-flat-file-cms.md


## Konfigurations-Einstellungen

Bei Blogposts kommen die Seiteneigenschaften `authors`, `categories` und `tags`
besonders zum Tragen. Ein Blog mit vielen Posts kann somit nach Autor, Kategorie
und Tag gefiltert werden.


## Twig-Widgets

Vorgefertigte Twig-Widgets erlauben es dir, die üblichen Blog-Funktionalitäten
mit einer Zeile Twig-Code im Layout einzubinden.

Momentan stehen die folgenden Widgets zur Verfügung:  
**Letzte Artikel**: Anzeige der letzten Posts.  
**Kategorien**: Anzeige aller Kategorien mit der Anzahl Posts pro Kategorie.  
**Tags**: Anzeige aller Tags mit der Anzahl Posts pro Tag.  
**Archiv**: Anzeige der archivierten Posts gruppiert nach Monat.  
**Links**: Anzeige von Links zu Kategorien, Tags und Autoren.  

Eingebunden werden die Widgets wie folgt. Zu beachten ist, dass Twig-Widgets nur
in Layout-Dateien funktionieren.

    {{ include('@widget/blog/recent_posts.html', {title: 'Letzte Artikel', showDate:false}) }}
    {{ include('@widget/blog/categories.html', {title: 'Kategorien', showCount:true}) }}
    {{ include('@widget/blog/tags.html', {title: 'Tags', showCount:true}) }}
    {{ include('@widget/blog/archives.html', {title: 'Archiv', showCount:true}) }}
    {{ include('@widget/blog/links.html', {page: page}) }}

Falls du ein Widget erweitern willst, kopierst du es einfach in den `site`-Ordner, passt es an
und änderst anschliessend den Pfad in der `include`-Anweisung.
