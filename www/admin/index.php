<?php

$dir_index = dirname(__FILE__);
include_once($dir_index."/../gtree.php");
GTree::startAdminPage();
include_once("head.php");
?>
<h1>Профиль</h1>
<br>
<a class="btn btn-danger" href="logout.php">Выйти из под административной учетной записи</a>

<hr>
<a class="btn btn-primary" target="_blank" href="data_export.php">Экспорт всех данных</a>
<hr>
<a class="btn btn-primary" href="data_import.php">Импорт данных (перезапись данных)</a>

<?php

include_once("footer.php");