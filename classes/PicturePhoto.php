<?php

class PicturePhoto extends Picture
{
	private int $userId;
	private int $num;
	
	private function __construct()
	{
	}
	
	public static function constructByArray(Array $array) : PicturePhoto
	{
		$new = new PicturePhoto;
		$new->hash = $array['hash'];
		$new->ext = $array['ext'];
		$new->userId = intval($array['user_id']);
		$new->num = intval($array['num']);
		return $new;
	}
	public static function constructByHash(string $hash) : PicturePhoto
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT DISTINCT `hash`, `extension` AS `ext`, `num`, `default`, `user_id`
			FROM `user_image` WHERE `hash`=:hash
		');
		$stmt->bindParam(':hash', $hash);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			throw new Exception('No such image');
		}
		return PicturePhoto::constructByArray($result);
	}
	public static function constructByUser(UserExisting $user) : array // [ PicturePhoto ]
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT DISTINCT `hash`, `extension` AS `ext`, `num`, `default`, `user_id`
			FROM `user_image` WHERE `user_id`=:user_id
			ORDER BY `num`
		');
		$stmt->bindParam(':user_id', $user->getId());
		$stmt->execute();
		$result = [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$result[] = PicturePhoto::constructByArray($row);
		}
		return $result;
	}
	
	public function getUserId() : int
	{
		return $this->userId;
	}
	public function getNum() : int
	{
		return $this->num;
	}
}
