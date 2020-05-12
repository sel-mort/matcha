<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class ResetModel extends Model
{
    public function reset($pdo, $username, $password)
    {
        $database = new Database();
        $params = array($password, $username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `password` = ? WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function username_count($pdo, $username, $email)
    {
        $database = new Database();
        $params = array($username, $email);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE LOWER(`username`) = LOWER(?) AND LOWER(`email`) = LOWER(?)", $params);
            return (int) $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }
}
