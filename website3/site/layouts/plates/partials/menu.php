<div class="topmenu" id="topmenu">
    <div id="topmenu-inner" class="pure-menu pure-menu-open pure-menu-horizontal">
        <ul class="pure-menu-list">
            <?php
            foreach ($menu->find(["parent=","hidden<1"]) as $item) {
                echo "<li class=\"pure-menu-item\"><a class=\"pure-menu-link\" href=\"{$baseUrl}/{$item['route']}\">{$item['title']}</a></li>";
            }
            ?>
            <?php /*
            {% for item in site.pageTree|visible %}
                {% set menuItem = item.menuItem %}
                {% set active = menuItem.routeEquals(route) ? 'pure-menu-selected' : '' %}
                {% set rootline = menuItem.routeInRootPath(route) ? 'pure-menu-selected' : '' %}
                <li class="pure-menu-item {{ active }} {{ rootline }}"><a class="pure-menu-link" href="{{ url(menuItem.route) }}">{{ menuItem.title }}</a></li>
            {% endfor %}
            */ ?>
        </ul>
    </div>
</div>


