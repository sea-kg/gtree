<?php

function update0007($conn){
    $stmt = $conn->prepare("
        CREATE TABLE `photos` (
            `id` int(11) NOT NULL,
            `uid` varchar(127) NOT NULL,
            `name` varchar(255) NOT NULL,
            `year` int(11) NOT NULL,
            `year_notexactly` int(11) NOT NULL,
            `description` TEXT NOT NULL,
            `created` DATETIME NOT NULL,
            `updated` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }

    return true;
}


