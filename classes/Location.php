<?php

class Location
{
	public static function getCodeById(int $id) : string
	{
		return Location::getLocationById($id)['code'];
	}
	public static function getIdByCode(string $code) : int
	{
		return Location::getLocationByCode($code)['id'];
	}
	public static function getLocationById(int $id) : array // ['id' => int, 'code' => string, 'name' => string]
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `code`, `name` FROM `location_code` WHERE `id`=:id');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row)
		{
			throw new Exception('Location not found');
		}
		$row['id'] = intval($row['id']);
		return $row;
	}
	public static function getLocationByCode(string $code) : array // ['id' => int, 'code' => string, 'name' => string]
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `code`, `name` FROM `location_code` WHERE `code`=:code');
		$stmt->bindParam(':code', $code);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row)
		{
			throw new Exception('Location not found');
		}
		$row['id'] = intval($row['id']);
		return $row;
	}
	public static function getSublocations(int $id) : array // of arrays
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `code`, `name` FROM `location_code` WHERE `parent`=:parent_id');
		$stmt->bindParam(':parent_id', $id);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$rows = array_map(function(array $loc): array {
			$loc['id'] = intval($loc['id']);
			return $loc;
		}, $rows);
		return $rows;
	}
	public static function getLocationByISO3166_1_a2(string $country) : array
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `code`, `name` FROM `location_code` WHERE `iso3166_1_a2`=:country');
		$stmt->bindParam(':country', $country);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row)
		{
			throw new Exception('Location not found');
		}
		$row['id'] = intval($row['id']);
		return $row;
	}
	public static function getLocationByUN_sub(string $country, string $region) : array
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `id`, `code`, `name` FROM `location_code` WHERE `un_sub`=:ext_code');
		$ext_code="{$country}-{$region}";
		$stmt->bindParam(':ext_code', $ext_code);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row)
		{
			var_dump($ext_code);
			throw new Exception('Location not found');
		}
		$row['id'] = intval($row['id']);
		return $row;
	}
	public static function getUserCounts(string $code) : array // ['now' => int, 'ever_from' => int, 'ever_to' => int]
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `now`, `ever_from`, `ever_to` FROM
				(
				SELECT COUNT(*) AS `now`
				FROM `user`
				WHERE `id` IN
					(
					SELECT `user_id`
					FROM `user_preference`
					WHERE `key`="home_location" AND `val`=:loc_code
					)
				) AS `n`
				CROSS JOIN
				(
					SELECT COUNT(DISTINCT `receiver_id`) AS `ever_to`
					FROM `postcard`
					WHERE `receive_location_id` = 
					(
						SELECT `id` FROM `location_code` WHERE `code`=:loc_code
					)
				) AS `et`
				CROSS JOIN
				(
					SELECT COUNT(DISTINCT `sender_id`) AS `ever_from`
					FROM `postcard`
					WHERE `send_location_id` = 
					(
						SELECT `id` FROM `location_code` WHERE `code`=:loc_code
					)
				) AS `ef`
			');
		$stmt->bindValue(':loc_code', $code);
		
		$res = $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$result['now'] = intval($result['now']);
		$result['ever_from'] = intval($result['ever_from']);
		$result['ever_to'] = intval($result['ever_to']);
		
		return $result;
	}
	public static function getPostcardCounts(string $code) : array // ['sent_from' => int, 'sent_to' => int]
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `sent_from`, `sent_to` FROM
				(
					SELECT COUNT(DISTINCT `id`) AS `sent_to`
					FROM `postcard`
					WHERE `receive_location_id` = 
					(
						SELECT `id` FROM `location_code` WHERE `code`=:loc_code
					)
				) AS `et`
				CROSS JOIN
				(
					SELECT COUNT(DISTINCT `id`) AS `sent_from`
					FROM `postcard`
					WHERE `send_location_id` = 
					(
						SELECT `id` FROM `location_code` WHERE `code`=:loc_code
					)
				) AS `ef`
			');
		$stmt->bindValue(':loc_code', $code);
		
		$res = $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$result['sent_from'] = intval($result['sent_from']);
		$result['sent_to'] = intval($result['sent_to']);
		
		return $result;
	}
	public static function guessLocationByInputCode(string $input) : string
	{
		$input = trim(strtoupper($input));
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
		$stmt->bindValue(':code', $input);
		$stmt->execute();
		if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
		{
			return $input;
		}
		
		$lookAlikeLetter = ['O', 'I', 'S', 'B'];
		$lookAlikeNumber = ['0', '1', '5', '8'];
		if(strlen($input) == 3) // country or world-region
		{
			$attempt = str_replace($lookAlikeLetter, $lookAlikeNumber, $input);
			if(is_numeric($attempt))
			{
				$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
				$stmt->bindValue(':code', $attempt);
				$stmt->execute();
				if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
				{
					return $attempt;
				}
			}
			
			$attempt = str_replace($lookAlikeNumber, $lookAlikeLetter, $input);
			$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
			$stmt->bindValue(':code', $attempt);
			$stmt->execute();
			if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
			{
				return $attempt;
			}
		}
		if(strlen($input) == 4) // sub-country regions
		{
			$attempt = str_replace($lookAlikeNumber, $lookAlikeLetter, $input);
			$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
			$stmt->bindValue(':code', $attempt);
			$stmt->execute();
			if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
			{
				return $attempt;
			}
			
			$part1 = substr($input, 0, 3);
			$part2 = substr($input, 3, 1);
			$attempt =
				str_replace($lookAlikeNumber, $lookAlikeLetter, $input) . 
				str_replace($lookAlikeLetter, $lookAlikeNumber, $part2);
			$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
			$stmt->bindValue(':code', $attempt);
			$stmt->execute();
			if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
			{
				return $attempt;
			}
			
			$part1 = substr($input, 0, 2);
			$part2 = substr($input, 2, 2);
			$attempt =
				str_replace($lookAlikeNumber, $lookAlikeLetter, $input) . 
				str_replace($lookAlikeLetter, $lookAlikeNumber, $part2);
			$stmt = $db->prepare('SELECT COUNT(*) AS `cnt` FROM `location_code` WHERE `code`=:code');
			$stmt->bindValue(':code', $attempt);
			$stmt->execute();
			if($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] == '1')
			{
				return $attempt;
			}
		}
		return $input;
	}
}