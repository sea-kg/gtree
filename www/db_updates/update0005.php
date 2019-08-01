<?php

function update0005($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `bornyear_notexactly` VARCHAR(10) NOT NULL DEFAULT 'no';
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }

    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `yearofdeath_notexactly` VARCHAR(10) NOT NULL DEFAULT 'no';
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }
    return true;
}


