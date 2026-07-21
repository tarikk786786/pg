<?php
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');

// InsForge PostgreSQL connection URL
$insforge_url = "postgresql://postgres:f76260816cf7fd8b63cd4a11314a0c8f@san4iv3j.us-east.database.insforge.app:5432/insforge?sslmode=disable";

// Parse URL for PDO
$parsed = parse_url($insforge_url);
$db_host = $parsed['host'];
$db_port = $parsed['port'];
$db_user = $parsed['user'];
$db_pass = $parsed['pass'];
$db_name = ltrim($parsed['path'], '/');

define('DB_HOST', $db_host);
define('DB_USERNAME', $db_user);
define('DB_PASSWORD', $db_pass);
define('DB_NAME', $db_name);

// Global PG connection instance
$_GLOBAL_PG = null;

function connect_database() {
    global $_GLOBAL_PG, $insforge_url;
    if ($_GLOBAL_PG !== null) return $_GLOBAL_PG;
    
    $conn_string = "host=" . DB_HOST . " port=" . parse_url($insforge_url, PHP_URL_PORT) . " dbname=" . DB_NAME . " user=" . DB_USERNAME . " password=" . DB_PASSWORD . " sslmode=require";
    
    $_GLOBAL_PG = pg_connect($conn_string);
    if (!$_GLOBAL_PG) {
        die("Database Connection failed: " . pg_last_error());
    }
    return $_GLOBAL_PG;
}

// MYSQLI POLYFILL FUNCTIONS

function db_connect($host, $user, $pass, $db) {
    return connect_database();
}

function db_query($con, $query) {
    $con = connect_database();
    
    // MySQL to PostgreSQL syntax fixes
    $query = str_replace('`', '"', $query);
    
    $result = @pg_query($con, $query);
    if ($result === false) {
        $GLOBALS['db_last_error'] = pg_last_error($con);
        return false;
    }
    return $result;
}

function db_fetch_assoc($result) {
    if ($result) {
        return pg_fetch_assoc($result);
    }
    return null;
}

function db_fetch_row($result) {
    if ($result) {
        return pg_fetch_row($result);
    }
    return null;
}

function db_fetch_array($result) {
    if ($result) {
        return pg_fetch_array($result, null, PGSQL_BOTH);
    }
    return null;
}

function db_num_rows($result) {
    if ($result) {
        return pg_num_rows($result);
    }
    return 0;
}

function db_error($con) {
    return $GLOBALS['db_last_error'] ?? 'Unknown error';
}

function db_real_escape_string($con, $string) {
    $con = connect_database();
    if ($string === null) return '';
    return pg_escape_string($con, $string);
}

function db_close($con) {
    global $_GLOBAL_PG;
    if ($_GLOBAL_PG) {
        pg_close($_GLOBAL_PG);
        $_GLOBAL_PG = null;
    }
    return true;
}

function db_insert_id($con) {
    // In PostgreSQL, last insert id is usually retrieved using RETURNING id
    // Since this is a polyfill for mysql_insert_id, it is tricky.
    // We can try to query lastval()
    $con = connect_database();
    $res = @pg_query($con, "SELECT lastval()");
    if ($res) {
        $row = pg_fetch_row($res);
        return $row[0];
    }
    return 0;
}

function db_multi_query($con, $query) {
    return db_query($con, $query);
}

function db_more_results($con) {
    return false;
}

function db_next_result($con) {
    return false;
}

function db_init() {
    return true;
}

function db_options($con, $opt, $val) {
    return true;
}

function db_real_connect($con, $host, $user, $pass, $db) {
    connect_database();
    return true;
}
?>