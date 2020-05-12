<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class ChatModel extends Model
{
    public function get_matched_users($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id, $user_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT DISTINCT(`user1_id`) FROM `like` WHERE `user0_id` = ?
            AND `user1_id` IN (SELECT `user0_id` FROM `like` WHERE `user1_id` = ?)
            AND (SELECT `picture` FROM `user` WHERE `id` = `user0_id`) IS NOT NULL
            AND (SELECT `picture` FROM `user` WHERE `id` = `user1_id`) IS NOT NULL", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function matches($pdo, $user0_id, $user1_id)
    {
        $database = new Database();
        $params = array($user0_id, $user1_id, $user1_id, $user0_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(`user0_id`) FROM `like` WHERE `user0_id` = ? 
            AND `user1_id` = ? OR `user0_id` = ? AND `user1_id` = ? AND (SELECT `picture` FROM `user` WHERE `id` = `user0_id`) IS NOT NULL 
            AND (SELECT `picture` FROM `user` WHERE `id` = `user1_id`) IS NOT NULL", $params);
            if ($res[0][0] == 2)
                return 1;
            else
                return 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
