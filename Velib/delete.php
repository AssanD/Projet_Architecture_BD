<?php
    include_once 'connection.php';

    $id = $_GET['id'];

    $cursor = $velib->deleteOne(["_id"=>$id]);

    header("location: index.php");
?>