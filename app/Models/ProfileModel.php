<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class ProfileModel extends Model
{
    public function get_user_profile($pdo, $username)
    {
        $database = new Database();
        $params = array($username);
        try {
            $res = $database->selectQuery($pdo, "SELECT * FROM `user` WHERE LOWER(`username`) = LOWER(?)", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_user_profile_by_id($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT * FROM `user` WHERE `id` = ?", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_interest($pdo, $interest)
    {
        $database = new Database();
        $params = array($interest);
        try {
            $database->nonQuery($pdo, "INSERT IGNORE INTO `interest`(`name`) values(?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_interest_id($pdo, $name)
    {
        $database = new Database();
        $params = array($name);
        try {
            $res = $database->selectQuery($pdo, "SELECT * FROM `interest` WHERE LOWER(`name`) = LOWER(?)", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_user_interests($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT `interest`.`name`
            FROM `interest`
            INNER JOIN `user_interest` ON `interest`.`id`=`user_interest`.`interest_id`
            WHERE `user_interest`.`user_id` = ?", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_user_interest($pdo, $user_id, $interest_id)
    {
        $database = new Database();
        $params = array($user_id, $interest_id);
        try {
            $database->nonQuery($pdo, "INSERT IGNORE INTO `user_interest`(`user_id`,`interest_id`) values(?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete_user_interest($pdo, $id)
    {
        $database = new Database();
        $params = array($id);
        try {
            $database->nonQuery($pdo, "DELETE FROM `user_interest` WHERE `user_id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update_profile_avatar($pdo, $username, $avatar)
    {
        $database = new Database();
        $params = array($avatar, $username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `picture` = ? WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update_profile($pdo, $newusername, $firstname, $lastname, $password, $email, $birthdate, $gender, $orientation, $bio, $username)
    {
        $database = new Database();
        $params = array($newusername, $firstname, $lastname, $password, $email, $birthdate, $gender, $orientation, $bio, $username);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `username` = ? , `first_name` = ? , `last_name` = ? , `password` = ? , `email` = ? , `birthdate` = ? , `gender` = ? , `orientation` = ? , `bio` = ? WHERE `username` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
