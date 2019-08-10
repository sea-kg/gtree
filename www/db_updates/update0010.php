<?php

function update0010($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `tree_x` INT NOT NULL DEFAULT 0;
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }

    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `tree_y` INT NOT NULL DEFAULT 0;
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }
    return true;
}


