<?php

class User
{
	private function __construct()
	{
	}
	
	public static function constructByLogin(string $login) : User
	{
		try
		{
			return UserExisting::constructByLogin($login);
		}
		catch(Exception $e)
		{
			$new = new UserNew();
			$new->login = $login;
			return $new;
		}
		
	}
	public static function exists(string $login) : bool
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT COUNT(`id`) `cnt` FROM `user` WHERE `login`=:login');
		$stmt->bindParam(':login', $login);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return ($result['cnt'] > 0);
	}
	public static function existsEmail(string $email) : bool
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT COUNT(`id`) `cnt` FROM `user` WHERE UPPER(`email`)=UPPER(:email)');
		$stmt->bindParam(':email', $email);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return ($result['cnt'] > 0);
	}
	public static function generateUser(Config $config, string $login, string $password, string $email = '') : UserExisting
	{
		$passhash_options = $config->getProperty('passhash_options');
		$pass_hash = password_hash($password, $passhash_options['algorythm'], $passhash_options['options']);
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			INSERT INTO `user` (`login`, `pass_hash`, `email`, `home_location_id`)
			VALUES (:login, :pass_hash, :email, (SELECT `id` FROM `location_code` WHERE `code`="SOL3"))
		');
		$stmt->bindParam(':login', $login);
		$stmt->bindParam(':pass_hash', $pass_hash);
		$stmt->bindParam(':email', $email);
		$res = $stmt->execute();
		if(!$res)
		{
			var_dump($stmt->errorInfo());
			die();
		}
		$id = $db->lastInsertId();
		
		$stmt = $db->prepare('INSERT INTO `user_waiting_approval` (`user_id`) VALUES (:id)');
		$stmt->bindParam(':id', $id);
		$res = $stmt->execute();
		if(!$res)
		{
			var_dump($stmt->errorInfo());
			die();
		}
		
		
		$stmt = $db->prepare('INSERT INTO `user_preference` (`user_id`, `key`, `val`) VALUES (:id, :key, :val)');
		$stmt->bindParam(':id', $id);
		$stmt->bindValue(':key', 'home_location');
		$stmt->bindValue(':val', 'SOL3');
		$res = $stmt->execute();
		if(!$res)
		{
			var_dump($stmt->errorInfo());
			die();
		}
		return UserExisting::constructById($id);
	}
	public static function getPreferenceOrDefaultById(int $id, string $key, string $default) : string
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `val` FROM `user_preference` WHERE `key`=:key AND `user_id`=:user_id');
		$stmt->bindParam(':key', $key);
		$stmt->bindParam(':user_id', $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row || $row['val'] == null)
		{
			return $default;
		}
		return $row['val'];
	}
	public static function removeSecret(string $secret) : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			DELETE FROM `user_persistent_login`
			WHERE `secret` = :secret
		');
		$stmt->bindParam(':secret', $secret);
		$res = $stmt->execute();
	}
}