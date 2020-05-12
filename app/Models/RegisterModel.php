<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class RegisterModel extends Model
{
    public function register($pdo, $username, $firstname, $lastname, $password, $email, $verification_code)
    {
        $database = new Database();
        $params = array($username, $firstname, $lastname, $password, $email, $verification_code);
        try {
            $database->nonQuery($pdo, "INSERT INTO `user`(`username`, `first_name`, `last_name`, `password`, `email`, `verification_code`) values(?,?,?,?,?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function username_count($pdo, $username)
    {
        $database = new Database();
        $params = array($username);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE LOWER(`username`) = LOWER(?)", $params);
            return (int)$res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function email_count($pdo, $email)
    {
        $database = new Database();
        $params = array($email);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE LOWER(`email`) = LOWER(?)", $params);
            return (int)$res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }
}
