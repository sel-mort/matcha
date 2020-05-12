<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class ScoreModel extends Model
{
    public function update_score($pdo, $user_id, $score)
    {
        $database = new Database();
        $params = array($score, $user_id);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `score` = `score` + ? WHERE `id` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
