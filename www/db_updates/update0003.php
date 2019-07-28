<?php

function update0003($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `private` VARCHAR(10) NOT NULL DEFAULT 'no';
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }
    return true;
}


