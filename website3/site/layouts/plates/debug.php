<?php $this->layout('main') ?>

<div class="content-container">
    <div class="container">
        <div class="pure-g">
            <div class="pure-u-1">
                <?php foreach ($menu->find("hidden<=1") as $item): ?>
                    <?= $item["title"] ?> //
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
