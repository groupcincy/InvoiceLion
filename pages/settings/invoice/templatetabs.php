<ul class="tabs">
<?php foreach ($languages as $language): ?>
    <li><a href="/settings/languages/view/<?php e($language['languages']['id']);?>">Template <?php e(strtoupper($language['languages']['code']));?></a></li>
<?php endforeach;?>
</ul>