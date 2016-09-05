<?php
function open_connect() {
    return pg_connect("host=".TRACKING_DB_HOST." port=".TRACKING_DB_PORT." dbname=".TRACKING_DB_NAME." user=".TRACKING_DB_USER." password=".TRACKING_DB_PASS);
}

function close_connect() {
    global $CONN;    
    return pg_close($CONN);
}

function get_error() {
    global $CONN;
    return pg_last_error($CONN);
}

function send_query($sql) {

    if ($sql == '')
        return false;

    global $CONN;
    if ($CONN == '')
        $CONN = open_connect();
    $res = pg_query($CONN, $sql);
    if (stripos($sql, 'insert') === 0) {
        $tbl = preg_replace('/^insert into ([^ ]+).*/i', '\1', $sql);
        if ($tbl == $sql)
            return 0;
        $id = pg_fetch_assoc(pg_query($CONN, "SELECT CURRVAL('{$tbl}_id_seq') AS seq" ));
        return $id['seq'];
    }
    else
        return $res;
}


