<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class SearchModel extends Model
{
    public function get_interest_users($pdo, $interest_name)
    {
        $database = new Database();
        $params = array($interest_name);
        try {
            $res = $database->selectQuery($pdo, "SELECT `user`.*
            FROM `user` INNER JOIN `user_interest` ON `user`.`id` = `user_interest`.`user_id`
            INNER JOIN `interest` ON `interest`.`id` = `user_interest`.`interest_id`
            WHERE `interest`.`name` = ?", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_no_interest_users($pdo)
    {
        $database = new Database();
        $params = array();
        try {
            $res = $database->selectQuery($pdo, "SELECT `user`.*
            FROM `user` LEFT JOIN `user_interest` ON `user`.`id` = `user_interest`.`user_id`
            WHERE `user_interest`.`user_id` IS NULL", $params);
            return $res;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get_distance($pdo, $lat1, $lon1, $lat2, $lon2)
    {
        $database = new Database();
        $params = array($lon1, $lat1, $lon2, $lat2);
        try {
            $res = $database->selectQuery($pdo, "SELECT ST_Distance_Sphere(point(?, ?), point(?, ?))", $params);
            return $res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }
}
