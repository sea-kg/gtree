<?php

function update0006_getRandomString($length = 10) {
    mt_srand((double)microtime()*10000); //optional for php 4.2.0 and up.
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function update0006($conn){
    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD `uid` VARCHAR(128) NOT NULL DEFAULT 'no';
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }

    $stmt = $conn->prepare("SELECT * FROM persons;");
    $stmt->execute();

    while ($row = $stmt->fetch()) {
        if ($row['uid'] != '') {
            $uid = update0006_getRandomString(128);
            $stmt2 = $conn->prepare("UPDATE persons SET uid = ? WHERE id = ?;");
            $stmt2->execute(array($uid, $row['id']));
        }
    }

    $stmt = $conn->prepare("
        ALTER TABLE `persons` ADD UNIQUE(`uid`);
    ");

    if (!$stmt->execute()) {
        error_log(print_r($stmt->errorInfo(),true));
        return false;
    }

    return true;
}


