<?php

$dir_index = dirname(__FILE__);
include_once($dir_index."/../gtree.php");
GTree::startAdminPage();
include_once("head.php");

echo '<a href="logout.php">Logout</a>';

include_once("footer.php");