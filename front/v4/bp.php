<?php
//fron end comm scripts has include incoming.php
require_once dirname(__FILE__).'/../include_common.php';

try {
    global $T_SESSION, $T_TPLID;    

    if (!($T_SESSION > 0)) {
        throw new Exception ("");
    }

    set_breakpoints(isset($_REQUEST['w'])? $_REQUEST['w'] : 0, isset($_REQUEST['h'])? $_REQUEST['h'] : 0, isset($_REQUEST['t'])? $_REQUEST['t'] : '');
}
catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}


