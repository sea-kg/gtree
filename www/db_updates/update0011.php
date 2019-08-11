<?php

function update0011($conn){
    $stmt = $conn->prepare("
        CREATE TABLE `biographies` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `personid` int(11) NOT NULL,
            `type` varchar(20) NOT NULL,
            `year` int(11) NOT NULL,
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


