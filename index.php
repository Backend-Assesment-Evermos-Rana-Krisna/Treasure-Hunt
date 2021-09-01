<?php
    if(defined('STDIN') || ( empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0))
    {
        require "TreasureHunt.php";
        $treasureHunt = new TreasureHunt();
        $treasureHunt->startHunt();
    }
    else
    {
        echo 'Must be running on command line.';
    }
?>