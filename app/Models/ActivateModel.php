<?php

namespace App\Models;

use App\Database\Database;
use App\Models\Model;

class ActivateModel extends Model
{
    public function activate($pdo, $verification_code)
    {
        $database = new Database();
        $params = array($verification_code);
        try {
            $database->nonQuery($pdo, "UPDATE `user` SET `verified` = 1 WHERE `verification_code` = ?", $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verification_code_exists($pdo, $verification_code)
    {
        $database = new Database();
        $params = array($verification_code);
        try {
            $res = $database->selectQuery($pdo, "SELECT COUNT(*) FROM `user` WHERE `verification_code` = ?", $params);
            return (int)$res[0][0];
        } catch (PDOException $e) {
            return false;
        }
    }
}
