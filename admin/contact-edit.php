<?php

require("../shared/inc/functions.inc.php");
require("../shared/inc/db.inc.php");

$id = $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM `messages` WHERE `id`=:id");
$stmt->bindParam(":id", $id);
$stmt->execute();
$message = $stmt->fetch();

// Keine Nachricht gefunden
if (empty($message)){
    header("Location: contact.php");
    die();
}

if (!empty($_POST)) {
    $isValidForm = (
        !empty($_POST['timestamp_date']) &&
        !empty($_POST['timestamp_time']) &&
        !empty($_POST['name']) &&  /*isset anstatt !empty ist auch möglich, dadurch werden leere Strings übermittelt*/ 
        !empty($_POST['email']) && 
        !empty($_POST['subject']) && 
        !empty($_POST['message'])
    );

    if($isValidForm){

        $date = explode("-", $_POST['timestamp_date']);
        $time = explode(":", $_POST['timestamp_time']);

        $timestamp = mktime(
            $time[0], $time[1], $time[2], 
            $date[1], $date[2], $date[0]);

        // Daten in DB schreiben

        $stmt = $pdo->prepare("UPDATE messages
            SET
            `name`=:name,
            `email`=:email,
            `subject`=:subject,
            `message`=:message,
            `timestamp`=:timestamp
            WHERE `id`=:id");

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $_POST['name']);
        $stmt->bindParam(":email", $_POST['email']);
        $stmt->bindParam(":subject", $_POST['subject']);
        $stmt->bindParam(":message", $_POST['message']);
        $stmt->bindParam(":timestamp", $timestamp);

        $stmt->execute();

        header("Location: contact.php");
        die();
    }
}

ob_start();
require("./views/contact-edit.view.php");
$content = ob_get_contents();
ob_end_clean();

require("./layouts/layout.php");

?>