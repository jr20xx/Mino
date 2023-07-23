<?php
class DbHelper
{
    private static $server = 'localhost', $bd_name = 'mino', $username = 'test', $password = '',
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
    $pdo = null, $instance = null;

    private function __construct()
    {
        try {
            if (self::$pdo == null)
                self::$pdo = new PDO("mysql:host=" . self::$server . ";dbname=" . self::$bd_name, self::$username, self::$password, self::$options);
        } catch (Exception $e) {
            die("Fatal Error: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new DbHelper();
        return self::$instance;
    }

    public function createDatabase()
    {
        try {
            $pdo = new PDO("mysql:host=" . self::$server, self::$username, self::$password, self::$options);
            $result = $pdo->prepare("CREATE DATABASE IF NOT EXISTS mino");
            $result->execute();
            if ($result->rowCount() >= 1) {
                $query = "CREATE TABLE IF NOT EXISTS mino.USERS(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, USERNAME TEXT NOT NULL UNIQUE, PASSWORD TEXT NOT NULL);
                    CREATE TABLE IF NOT EXISTS mino.NOTES(ID INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, TITLE TEXT NOT NULL, BODY TEXT NOT NULL, TIME_STAMP INTEGER NOT NULL, USER_ID INTEGER NOT NULL, FOREIGN KEY (USER_ID) REFERENCES mino.USERS(ID) ON DELETE CASCADE);";
                $result = $pdo->prepare($query);
                $result->execute();
            }
        } catch (Exception $e) {
            die("Fatal Error: " . $e->getMessage());
        }
    }

    public function isUernameRegistered($username)
    {
        $result = self::$pdo->prepare("SELECT * FROM mino.USERS WHERE USERNAME=:username;");
        $result->bindParam(':username', $username);
        $result->execute();
        return $result->rowCount() >= 1;
    }

    public function checkCredentials($username, $password)
    {
        try {
            session_start();
            $result = self::$pdo->prepare("SELECT * FROM USERS WHERE USERNAME=:username AND PASSWORD=:password;");
            $result->bindParam(':username', $username);
            $result->bindParam(':password', $password);
            $result->execute();
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) >= 1) {
                $_SESSION["s_user"] = $username;
                $_SESSION["s_user_id"] = $rows[0]['ID'];
            } else {
                $_SESSION["s_user"] = null;
                $_SESSION["s_user_id"] = null;
                $rows = null;
            }
            return json_encode($rows, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function deauthUser()
    {
        try {
            session_start();
            unset($_SESSION["s_user"]);
            unset($_SESSION["s_user_id"]);
            session_destroy();
            return 200;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function registerUser($username, $password)
    {
        try {
            if (self::$instance->isUernameRegistered($username))
                return 400;
            else {
                $result = self::$pdo->prepare("INSERT INTO USERS(USERNAME, PASSWORD) VALUES(:username, :password)");
                $result->bindParam(':username', $username);
                $result->bindParam(':password', $password);
                $result->execute();
                return $result->rowCount() >= 1 ? 201 : null;
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public function changePassword($user_id, $password, $newPassword)
    {
        try {
            $result = self::$pdo->prepare("UPDATE USERS SET PASSWORD=:newPassword WHERE ID=:user_id AND PASSWORD=:password;");
            $result->bindParam(':newPassword', $newPassword);
            $result->bindParam(':user_id', $user_id);
            $result->bindParam(':password', $password);
            $result->execute();
            return $result->rowCount() >= 1 ? 200 : 400;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function removeAccount($user_id)
    {
        try {
            $result = self::$pdo->prepare("DELETE FROM USERS WHERE ID=" . $user_id . ";");
            $result->execute();
            if ($result->rowCount() >= 1) {
                self::deauthUser();
                return 200;
            } else
                return 400;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function createNote($title, $body, $user_id)
    {
        try {
            $result = self::$pdo->prepare("INSERT INTO NOTES(TITLE, BODY, TIME_STAMP, USER_ID) VALUES(" . self::$pdo->quote($title) . ", " . self::$pdo->quote($body) . ", " . time() . ", " . $user_id . ");");
            $result->execute();
            $result = self::$pdo->prepare("SELECT * FROM NOTES ORDER BY ID DESC LIMIT 1");
            $result->execute();
            return json_encode($result->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getNotes($user_id)
    {
        try {
            $result = self::$pdo->prepare("SELECT * FROM NOTES WHERE USER_ID=" . $user_id . " ORDER BY TIME_STAMP;");
            $result->execute();
            return json_encode($result->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function updateNote($note_id, $title, $body, $user_id)
    {
        try {
            $result = self::$pdo->prepare("UPDATE NOTES SET TITLE=" . self::$pdo->quote($title) . ", BODY=" . self::$pdo->quote($body) . ", TIME_STAMP=" . time() . " WHERE ID=" . $note_id . " AND USER_ID=" . $user_id . ";");
            $result->execute();
            $result = self::$pdo->prepare("SELECT * FROM NOTES WHERE ID='$note_id' AND USER_ID='$user_id'");
            $result->execute();
            return json_encode($result->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return $e;
        }
    }

    public function removeNote($note_id, $user_id)
    {
        try {
            $result = self::$pdo->prepare("DELETE FROM NOTES WHERE ID='$note_id' AND USER_ID ='$user_id'");
            return $result->execute();
        } catch (Exception $e) {
            return $e;
        }
    }
}
?>