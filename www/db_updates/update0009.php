<?php

function update0009($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `photos` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }
    return true;
}


