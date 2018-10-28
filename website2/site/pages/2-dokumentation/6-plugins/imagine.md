---
title: Imagine Plugin
layout: documentation.html
link_to_overview: 1
---

[githubreadme https://raw.githubusercontent.com/getherbie/plugin-imagine/master/README.md]


## Ausführliches Code-Beispiel

Manchmal hilft ein Code-Beispiel mehr als viele Worte. Deshalb folgt hier ein
ausführliches Beispiel, angefangen bei der Konfiguration bis zur Ausgabe über
den Shortcode-Aufruf.

### Konfiguration

In dieser Konfiguration sieht man gut, dass Filter beliebig verkettet werden
können. Das Beispiel 2 durchläuft zuerst den Crop- und danach den Thumbnail-
Filter.

    imagine:
        filter_sets:
            bsp1:
                filters:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp2:
                filters:
                    crop:
                        start: [130, 250]
                        size: [520, 390]
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp3:
                filters:
                    grayscale:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp4:
                filters:
                    colorize:
                        color: '#ff0000'
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp5:
                filters:
                    negative:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp6:
                filters:
                    sharpen:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp7:
                filters:
                    gamma:
                        correction: 0.3
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp8:
                filters:
                    rotate:
                        angle: 90
                    thumbnail:
                        size: [220, 165]
                        mode: outbound
            bsp9:
                filters:
                    flipVertically:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp10:
                filters:
                    flipHorizontally:
                    thumbnail:
                        size: [220, 220]
                        mode: inset
            bsp11:
                filters:
                    resize:
                        size: [220, 165]
            bsp12:
                filters:
                    thumbnail:
                        size: [10, 10]
                        mode: inset
                    upscale:
                        min: [165, 165]
            bsp13:
                filters:
                    relativeResize:
                        method: widen
                        parameter: 200
            bsp14:
                filters:
                    relativeResize:
                        method: heighten
                        parameter: 150
            bsp15:
                filters:
                    thumbnail:
                        size: [20, 20]
                        mode: inset
                    relativeResize:
                        method: scale
                        parameter: 10
            bsp16:
                filters:
                    thumbnail:
                        size: [20, 20]
                        mode: inset
                    relativeResize:
                        method: increase
                        parameter: 135


### Twig-Filter

Die ganze Magie steckt im `imagine`-Shortcode respektive in der Twig-Funktion bzw. dem Twig-Filter mit der Angabe des 
vordefinierten Filtersatzes.

    [[imagine tulpen.jpg filter="bsp1"]]
    [[imagine tulpen.jpg filter="bsp2"]]
    [[imagine tulpen.jpg filter="bsp3"]]
    [[imagine tulpen.jpg filter="bsp4"]]
    [[imagine tulpen.jpg filter="bsp5"]]
    [[imagine tulpen.jpg filter="bsp6"]]
    [[imagine tulpen.jpg filter="bsp7"]]
    [[imagine tulpen.jpg filter="bsp8"]]
    [[imagine tulpen.jpg filter="bsp9"]]
    [[imagine tulpen.jpg filter="bsp10"]]
    [[imagine tulpen.jpg filter="bsp11"]]
    [[imagine tulpen.jpg filter="bsp12"]]
    [[imagine tulpen.jpg filter="bsp13"]]
    [[imagine tulpen.jpg filter="bsp14"]]
    [[imagine tulpen.jpg filter="bsp15"]]
    [[imagine tulpen.jpg filter="bsp16"]]


### HTML-Ausgabe

Und das Ganze sieht dann im Browser so aus:

<div class="gallery">
<figure>[imagine tulpen.jpg filter="bsp1"]<figcaption>Thumbnail</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp2"]<figcaption>Crop</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp3"]<figcaption>Grayscale</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp4"]<figcaption>Colorize</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp5"]<figcaption>Negative</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp6"]<figcaption>Sharpen</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp7"]<figcaption>Gamma</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp8"]<figcaption>Rotate 90°</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp9"]<figcaption>Flip vertically</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp10"]<figcaption>Flip horizontally</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp11"]<figcaption>Resize</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp12"]<figcaption>Upscale</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp13"]<figcaption>Relative resize (widen)</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp14"]<figcaption>Relative resize (heighten)</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp15"]<figcaption>Relative resize (scale)</figcaption></figure>
<figure>[imagine tulpen.jpg filter="bsp16"]<figcaption>Relative resize (increase)</figcaption></figure>
</div>


[1]: http://imagine.readthedocs.org
[2]: https://github.com/avalanche123/Imagine
