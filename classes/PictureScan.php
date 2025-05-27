<?php

class PictureScan extends Picture
{
	private array $cardIds;
	
	private function __construct()
	{
	}
	
	public static function constructByArray(Array $array) : PictureScan
	{
		$new = new PictureScan;
		$new->hash = $array['hash'];
		$new->ext = $array['ext'];
		$new->cardIds = $array['card_ids'];
		return $new;
	}
	public static function constructByHash(string $hash) : PictureScan
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT DISTINCT `hash`, `extension` AS `ext`
			FROM `postcard_image` WHERE `hash`=:hash
		');
		$stmt->bindParam(':hash', $hash);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			throw new Exception('No such image');
		}
		
		$stmt = $db->prepare('
			SELECT `postcard_id`
			FROM `postcard_image` WHERE `hash`=:hash
		');
		$stmt->bindParam(':hash', $hash);
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
		if($res === false)
		{
			$result['card_ids'] = array();
		}
		else
		{
			$result['card_ids'] = $res;
		}
		return PictureScan::constructByArray($result);
	}
	public function getCardIds() : array
	{
		return $this->cardIds;
	}
}
