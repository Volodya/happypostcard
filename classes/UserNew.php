<?php

class UserNew extends User
{
	private string $login;
	
	private static $__friends = array('User');
	public function __set($key, $value)
	{
		$trace = debug_backtrace();
		if(isset($trace[1]['class']) && in_array($trace[1]['class'], UserNew::$__friends))
		{
			return $this->$key = $value;
		}
		
		// normal __set() code here
		trigger_error('Cannot access private property ' . __CLASS__ . '::$' . $key, E_USER_ERROR);
	}
	

	
	public function __construct()
	{
	}
	public function canLogin(string $password, Config $config) : bool
	{
		if(password_verify($password, $this->pass_hash))
		{
			$passhash_options = $config->getProperty('passhash_options');
			if(password_needs_rehash($this->pass_hash, $passhash_options['algorythm'], $passhash_options['options']))
			{
				$new_hash = password_hash($password, $passhash_options['algorythm'], $passhash_options['options']);
				$db = Database::getInstance();
				$stmt->prepare('UPDATE `user` SET `pass_hash`=:pass_hash');
				$stmt->bindParam(':pass_hash', $new_hash);
				$stmt->execute();
			}
			return true;
		}
		return false;
	}
	public static function constructById(string $id) : User
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `pass_hash`, `login` FROM `user` WHERE `id`=:id');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			throw new Exception('No user with such id');
		}
		
		$new = new UserExisting();
		
		$new->login = $result['login'];
		$new->id = $result['id'];
		$new->pass_hash = $result['pass_hash'];
		
		return $new;
	}
	
	public function getLogin() : string
	{
		return $this->login;
	}
	/*
	public static function generateUser(Config $config, string $login, string $password, string $email = '')
	{
		$passhash_options = $config->getProperty('passhash_options');
		$pass_hash = password_hash($password, $passhash_options['algorythm'], $passhash_options['options']);
		
		$db = Database::getInstance();
		$stmt = $db->prepare('INSERT INTO `user` (`login`, `pass_hash`, `email`) VALUES (:login, :pass_hash, :email)');
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
	}
	*/
}