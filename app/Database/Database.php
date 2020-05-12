<?php

namespace App\Database;

class Database
{
    public function nonQuery($pdo, $query, $params = [])
	{
		$stm = $pdo->prepare($query);
		if ($stm === false)
			return (false);
		$ret = $stm->execute($params);
		$stm->closeCursor();
		return ($ret !== false);
	}
	public function selectQuery($pdo, $query, $params = [])
	{
		$stm = $pdo->prepare($query);
		if ($stm === false)
			return (false);
		$error = $stm->execute($params) === false;
		if ($error === false)
			$result = $stm->fetchAll();
		$stm->closeCursor();
		if ($error === true)
			return (true);
		return ($result);
    }
}