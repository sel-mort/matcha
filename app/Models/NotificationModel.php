<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class NotificationModel extends Model
{
    public function get_user_notification($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT * FROM `notification` WHERE `user1_id` = ? ORDER BY `date` DESC", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_activated_user_notification_count($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `notification` WHERE `user1_id` = ? AND `activated` = 1", $params);
            return (int) $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deactivate_user_notification($pdo, $user_id)
    {
        $database = new Database();
        $params = array($user_id);
        try {
            $res = $database->nonQuery($pdo, "UPDATE `notification` SET `activated` = 0 WHERE `user1_id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
