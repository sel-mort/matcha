<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class UserModel extends Model
{
    public function get_user_like_link_count($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `like` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_user_report_link_count($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `report` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_user_block_link_count($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `block` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete_user_like_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->nonQuery($pdo, "DELETE FROM `like` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete_user_report_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->nonQuery($pdo, "DELETE FROM `report` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete_user_block_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $res = $database->nonQuery($pdo, "DELETE FROM `block` WHERE `user0_id` = ? AND `user1_id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_user_like_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $database->nonQuery($pdo, "INSERT INTO `like`(`user0_id`, `user1_id`) values(?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_user_report_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $database->nonQuery($pdo, "INSERT INTO `report`(`user0_id`, `user1_id`) values(?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_user_block_link($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id);
        try {
            $database->nonQuery($pdo, "INSERT INTO `block`(`user0_id`, `user1_id`) values(?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function add_notification($pdo, $user0_id, $user1_id, $action)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id, $action);
        try {
            $database->nonQuery($pdo, "INSERT INTO `notification`(`user0_id`, `user1_id`,`action`) values(?,?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
