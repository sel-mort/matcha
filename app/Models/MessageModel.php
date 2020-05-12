<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class MessageModel extends Model
{
    public function get_user_message($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id, $user0_id, $user1_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT * FROM `message` WHERE (`user1_id` = ? AND `user0_id` = ?) OR (`user0_id` = ? AND `user1_id` = ?)", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function set_user_message($pdo, $connected_user_id, $user_id, $message)
    {
        $database = new Database();
        $params = array($message, $connected_user_id, $user_id);
        try {
            $database->nonQuery($pdo, "INSERT INTO `message`(`value`, `user0_id`,`user1_id`) values(?,?,?)", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
