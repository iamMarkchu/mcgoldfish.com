<?php
function open_connect() {
    global $CONN;    
    $pdo = "mysql:host=" . TRACKING_DB_HOST . ";dbname=" . TRACKING_DB_NAME;
    $CONN = new PDO($pdo, TRACKING_DB_USER, TRACKING_DB_PASS);
    return $CONN;
}

function close_connect() {
    global $CONN;    
    return $CONN = null;
}

function get_error() {
    global $CONN;
    return $CONN->errorInfo();
}

function send_query($sql) {
    if ($sql == '')
        return false;

    global $CONN;
    if ($CONN == '')
        $CONN = open_connect();

    $res = $CONN->query($sql);

    if (stripos($sql, 'insert') === 0)
        return $CONN->lastInsertId();
    else
        return $res;
}



