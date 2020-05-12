<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class LogoutModel extends Model
{
    public function logout($pdo, $username)
    {
        $database = new Database();
        $params = array($username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `last_connection` = NOW(), `online` = 0 WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
