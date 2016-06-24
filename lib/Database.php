<?php
class Database {
    private static $db = null;

    public static function get(): PDO {
        if (!self::$db) {
            self::$db = new PDO('mysql:host='.Config::DB_HOST.';dbname='.Config::DB_NAME, Config::DB_USERNAME, Config::DB_PASSWORD);
        }
        return self::$db;
    }
}