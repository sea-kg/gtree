<?php

function update0004($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `gtline` INT(11) NOT NULL DEFAULT 0;
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }
    return true;
}


