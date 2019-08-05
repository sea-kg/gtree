<?php

$dir_index = dirname(__FILE__);
include_once($dir_index."/../gtree.php");
GTree::startAdminPage();
include_once("head.php");
?>
<h1>Профиль</h1>
<br>
<a class="btn btn-danger" href="logout.php">Выйти из под административной учетной записи</a>

<?php

include_once("footer.php");