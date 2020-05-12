<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class LoginModel extends Model
{
    public function login($pdo, $username, $password)
    {
        $database = new Database();
        $params = array($username, $password);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE LOWER(`username`) = LOWER(?) AND `password` = ?", $params);
            return (int) $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function check_verification($pdo, $username, $password)
    {
        $database = new Database();
        $params = array($username, $password);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE LOWER(`username`) = LOWER(?) AND `password` = ? AND `verified` = 1", $params);
            return (int) $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update_position($pdo, $username, $latitude, $longitude)
    {
        $database = new Database();
        $params = array($latitude, $longitude, $username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `latitude` = ? , `longitude` = ? WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update_connection_status($pdo, $username)
    {
        $database = new Database();
        $params = array($username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `last_connection` = NOW(), `online` = 1 WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
}
