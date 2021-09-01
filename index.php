<?php
    require "TreasureHunt.php";
    
    // Check if environment is cli or not
    if(php_sapi_name() == 'cli') {                    
        $treasureHunt = new TreasureHunt();
        $treasureHunt->startHunt();
    } else print_r('Must be running on command line.');
?>