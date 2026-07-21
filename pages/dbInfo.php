<?php
error_reporting(0);
date_default_timezone_set('Asia/Kolkata');

// InsForge PostgreSQL connection URL
$insforge_url = "postgresql://postgres:f76260816cf7fd8b63cd4a11314a0c8f@san4iv3j.us-east.database.insforge.app:5432/insforge?sslmode=require";

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

// Global PDO instance
$_GLOBAL_PDO = null;

function connect_database() {
    global $_GLOBAL_PDO, $insforge_url;
    if ($_GLOBAL_PDO !== null) return $_GLOBAL_PDO;
    
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . parse_url($insforge_url, PHP_URL_PORT) . ";dbname=" . DB_NAME . ";sslmode=require";
    try {
        $_GLOBAL_PDO = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $_GLOBAL_PDO;
    } catch (PDOException $e) {
        die("Database Connection failed: " . $e->getMessage());
    }
}

// MYSQLI POLYFILL FUNCTIONS

function db_connect($host, $user, $pass, $db) {
    return connect_database();
}

function db_query($con, $query) {
    if (!$con || !($con instanceof PDO)) $con = connect_database();
    
    // MySQL to PostgreSQL syntax fixes
    $query = str_replace('`', '"', $query);
    
    $stmt = $con->query($query);
    if ($stmt === false) {
        // Save error for db_error()
        $GLOBALS['db_last_error'] = $con->errorInfo()[2] ?? 'Unknown error';
        return false;
    }
    return $stmt;
}

function db_fetch_assoc($result) {
    if ($result && $result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}

function db_fetch_row($result) {
    if ($result && $result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_NUM);
    }
    return null;
}

function db_fetch_array($result) {
    if ($result && $result instanceof PDOStatement) {
        return $result->fetch(PDO::FETCH_BOTH);
    }
    return null;
}

function db_num_rows($result) {
    if ($result && $result instanceof PDOStatement) {
        return $result->rowCount();
    }
    return 0;
}

function db_error($con) {
    return $GLOBALS['db_last_error'] ?? 'Unknown error';
}

function db_real_escape_string($con, $string) {
    if (!$con || !($con instanceof PDO)) $con = connect_database();
    if ($string === null) return '';
    $quoted = $con->quote($string);
    // PDO quote() adds surrounding quotes, mysqli doesn't. Strip them.
    return substr($quoted, 1, -1);
}

function db_close($con) {
    global $_GLOBAL_PDO;
    $_GLOBAL_PDO = null;
    return true;
}

function db_insert_id($con) {
    if (!$con || !($con instanceof PDO)) $con = connect_database();
    return $con->lastInsertId();
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