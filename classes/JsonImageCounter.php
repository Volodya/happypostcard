<?php

class JsonImageCounter extends JsonGenerator
{
	private string $hash;
	public function __construct(array $additionalUrl)
	{
		$this->hash = $additionalUrl[0];
	}
	
	public function toString() : string
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT
				(`postcard`.`cnt` + `user`.`cnt`) AS `total_count`,
				`postcard`.`cnt` AS `postcard_image_count`,
				`user`.`cnt` AS `user_image_count`
			FROM
			(SELECT COUNT(*) `cnt` FROM `postcard_image` WHERE hash=:hash) `postcard`,
			(SELECT COUNT(*) `cnt` FROM `user_image`     WHERE hash=:hash) `user`
		');
		$stmt->bindParam(':hash', $this->hash);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return json_encode($row);
	}
}
