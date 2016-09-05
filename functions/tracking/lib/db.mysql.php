<?php
function open_connect() {
    global $CONN;    
    $CONN = mysql_connect(TRACKING_DB_HOST, TRACKING_DB_USER, TRACKING_DB_PASS, true);
    mysql_select_db(TRACKING_DB_NAME, $CONN);
    return $CONN;
}

function close_connect() {
    global $CONN;    
    return mysql_close($CONN);
}

function get_error() {
    global $CONN;
    return mysql_error($CONN);
}

function send_query($sql) {
    if ($sql == '')
        return false;

    global $CONN;
    if ($CONN == '')
        $CONN = open_connect();

    $res = mysql_query($sql, $CONN);

    if (stripos($sql, 'insert') === 0)
        return mysql_insert_id($CONN);
    else
        return $res;
}



