<?php
class DbHelper
{
    private static $server = 'localhost', $bd_name = 'mino', $username = 'test', $password = '',
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

    public static function connect()
    {
        try {
            return new PDO("mysql:host=" . self::$server . ";dbname=" . self::$bd_name, self::$username, self::$password, self::$options);
        } catch (Exception $e) {
            die("Fatal Error: " . $e->getMessage());
        }
    }

    public static function createDatabase()
    {
        try {
            $pdo = new PDO("mysql:host=" . self::$server, self::$username, self::$password, self::$options);
            $query = "CREATE DATABASE IF NOT EXISTS mino";
            $result = $pdo->prepare($query);
            $result->execute();
            if ($result->rowCount() >= 1) {
                $query = "CREATE TABLE IF NOT EXISTS mino.USERS(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, USERNAME TEXT NOT NULL UNIQUE, PASSWORD TEXT NOT NULL);
                    CREATE TABLE IF NOT EXISTS mino.NOTES(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, TITLE TEXT NOT NULL, BODY TEXT NOT NULL, TIME_STAMP INTEGER NOT NULL, USER_ID INTEGER NOT NULL, FOREIGN KEY (USER_ID) REFERENCES mino.USERS(ID));";
                $result = $pdo->prepare($query);
                $result->execute();
            }
        } catch (Exception $e) {
            die("Fatal Error: " . $e->getMessage());
        }
    }

    public static function isUsernameRegistered($username)
    {
        try {
            $pdo = self::connect();
            $query = "SELECT * FROM mino.USERS WHERE USERNAME=:username;";
            $result = $pdo->prepare($query);
            $result->bindParam(':username', $username);
            $result->execute();
            if ($result->rowCount() >= 1)
                return true;
        } catch (Exception $e) {
            die("Fatal Error: " . $e->getMessage());
        }
        return false;
    }
}
?>