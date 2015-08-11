<?php
    global $session;
    $equipmentbuffs = db_prefix("magicitembuffs");
    $equipment = db_prefix("magicitems");
    $new_items = array('buffid'=> array('name'=>'buffid', 'type'=>'tinyint unsigned','null'=>'0'));
    $buff_table = array(
        'buffid'=> array('name'=>'buffid', 'type'=>'tinyint unsigned','null'=>'0', 'extra'=>'auto_increment'),
        'buffname'=> array('name'=>'buffname', 'type'=>'varchar(255)','null'=>'0'),
        'itembuff'=> array('name'=>'itembuff', 'type'=>'text', 'null'=>'1'),
        'key-PRIMARY' => array('name'=>'PRIMARY', 'type'=>'primary key', 'unique'=>'1', 'columns'=>'buffid'));
    require_once("lib/tabledescriptor.php");
    synctable($equipment, $new_items, true);
    synctable($equipmentbuffs, $buff_table, true);
    module_addhook("mysticalshop-editor");
    module_addhook("mysticalshop-buy");
    module_addhook("mysticalshop-preview");
    module_addhook("mysticalshop-sell-after");
    module_addhook("newday");
?>