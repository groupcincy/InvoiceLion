<?php
$language = DB::selectOne('SELECT * FROM `languages` WHERE `id` = ?', $id); // tenant_id not required
$data = DB::selectOne('SELECT * FROM `templates` WHERE `tenant_id` = ? AND `language_id` = ?', $_SESSION['user']['tenant_id'], $language['languages']['id']);
