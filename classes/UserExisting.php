<?php

class UserExisting extends User
{
	private int $id;
	private string $login;
	private string $polite_name;
	private string $pass_hash;
	
	private int $homeLocationId;
	private int $travellingLocationId;
	
	private bool $enabled;
	
	private static $__friends = array('User');
	public function __set($key, $value)
	{
		$trace = debug_backtrace();
		if(isset($trace[1]['class']) && in_array($trace[1]['class'], UserExisting::$__friends))
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
				$this->updatePassword($config, $password);
			}
			return true;
		}
		return false;
	}
	public static function constructByPersistentLogin(string $secret) : UserExisting
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `pass_hash`, `login`, `polite_name`, `home_location_id`, `travelling_location_id`,
				`address_changed_on`, `address_confirmed_on`,
				`disabled_on`, `deleted_on`, `blocked_on`
			FROM `user` WHERE `id` = (
				SELECT `user_id` FROM `user_persistent_login` WHERE `secret` = :secret
			)');
		$stmt->bindParam(':secret', $secret);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			throw new Exception('No user with such secret');
		}
		
		return UserExisting::constructByArray($result);
	}
	public static function constructByLogin(string $login) : UserExisting
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `pass_hash`, `login`, `polite_name`, `home_location_id`, `travelling_location_id`,
				`address_changed_on`, `address_confirmed_on`,
				`disabled_on`, `deleted_on`, `blocked_on`
			FROM `user` WHERE `login`=:login
		');
		$stmt->bindParam(':login', $login);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			throw new Exception('No user with such login');
		}
		
		return UserExisting::constructByArray($result);
	}
	public static function constructById(string $id) : UserExisting
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `id`, `pass_hash`, `login`, `polite_name`, `home_location_id`, `travelling_location_id`,
				`address_changed_on`, `address_confirmed_on`,
				`disabled_on`, `deleted_on`, `blocked_on`
			FROM `user` WHERE `id`=:id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			throw new Exception('No user with such id');
		}
		
		return UserExisting::constructByArray($result);
	}
	public static function constructByPasswordResetSecret(string $secret) : UserExisting
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `pass_hash`, `login`, `polite_name`, `home_location_id`, `travelling_location_id`,
				`address_changed_on`, `address_confirmed_on`,
				`disabled_on`, `deleted_on`, `blocked_on`
			FROM `user` WHERE `id` = (
				SELECT `user_id` FROM `user_password_recover_secret` WHERE `secret_code` = :secret
			)');
		$stmt->bindParam(':secret', $secret);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$result)
		{
			throw new Exception('No user with such secret');
		}
		
		return UserExisting::constructByArray($result);
	}
	public static function constructByArray(Array $info) : UserExisting
	{
		$new = new UserExisting();
		$new->id			= intval($info['id']);
		$new->login			= $info['login'];
		$new->polite_name	= $info['polite_name'];
		$new->pass_hash		= $info['pass_hash'];
		
		if(empty($info['travelling_location_id']))
		{
			$info['travelling_location_id'] = -1;
		}
		
		$new->enabled = empty($info['disabled_on']);
		
		$new->homeLocationId = $info['home_location_id'];
		$new->travellingLocationId = $info['travelling_location_id'];
		
		return $new;
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	public function getLogin() : string
	{
		return $this->login;
	}
	public function getPoliteName() : string
	{
		if(!isset($this->polite_name) or empty($this->polite_name)) return $this->login;
		return $this->polite_name;
	}
	public function isEnabled() : bool
	{
		return $this->enabled;
	}
	public function getPreferenceOrDefault(string $key, string $default) : string
	{
		return User::getPreferenceOrDefaultById($this->id, $key, $default);
	}
	public function confirmPassword(string $password) : bool
	{
		return password_verify($password, $this->pass_hash);
	}
	public function updatePassword(Config $config, string $password) : void
	{
		$passhash_options = $config->getProperty('passhash_options');
		$pass_hash = password_hash($password, $passhash_options['algorythm'], $passhash_options['options']);
		
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `user` SET `pass_hash` = :pass_hash WHERE `id`=:id');
		$stmt->bindParam(':pass_hash', $pass_hash);
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
		if(!$res)
		{
			var_dump($stmt->errorInfo());
			die();
		}
		$stmt = $db->prepare('DELETE FROM `user_password_recover_secret` WHERE `user_id` =:id');
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
	}
	public function updateLoggedInDate() : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `user` SET `loggedin_at` = DATE("now") WHERE `id`=:id');
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
	}
	public function updateSecret(string $secret) : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			INSERT INTO `user_persistent_login`(`user_id`, `secret`)
			VALUES(:user_id, :secret)
			ON CONFLICT(`user_id`)
			DO UPDATE SET `secret` = excluded.`secret`
		');
		$stmt->bindParam(':user_id', $this->id);
		$stmt->bindParam(':secret', $secret);
		$res = $stmt->execute();
	}
	public function countCardsTravelling(int $maxDays) : int
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT COUNT(*) AS `cnt`
			FROM `postcard`
			WHERE `sender_id` = :user_id AND `received_at` IS NULL
				AND CAST(JulianDay("now") - JulianDay(`sent_at`) AS INTEGER) <= :max_days
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':max_days', $maxDays);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return intval($row['cnt']);
	}
	public function countCardsWaiting(int $maxDays) : int
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT COUNT(*) AS `cnt`
			FROM `postcard`
			WHERE `receiver_id` = :user_id AND `received_at` IS NULL
				AND CAST(JulianDay("now") - JulianDay(`sent_at`) AS INTEGER) <= :max_days
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':max_days', $maxDays);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return intval($row['cnt']);
	}
	public function countCardsSent() : int
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT COUNT(*) AS `cnt`
			FROM `postcard`
			WHERE `sender_id` = :user_id AND `received_at` IS NOT NULL
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return intval($row['cnt']);
	}
	public function countCardsReceived() : int
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT COUNT(*) AS `cnt`
			FROM `postcard`
			WHERE `receiver_id` = :user_id AND `received_at` IS NOT NULL
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return intval($row['cnt']);
	}
	public function hasAddress() : bool
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'SELECT COUNT(*) AS `cnt` FROM `address` WHERE `user_id` = :user_id'
			);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return intval($row['cnt']) > 0;
	}
	public function getAddresses() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'SELECT `addr` FROM `address` WHERE `user_id` = :user_id'
			);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
	}
	public function addAddress(string $addr, string $lang_code, boolean $byAdmin=false) : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'INSERT INTO `address`(`user_id`, `language_code`, `addr`)
			VALUES(:user_id, :lang_code, :addr)'
			);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':lang_code', $lang_code);
		$stmt->bindValue(':addr', $addr);
		$stmt->execute();
		
		if(!$byAdmin)
		{
			$addressId = $db->lastInsertId();
			
			$stmt = $db->prepare(
				'INSERT INTO `address_waiting_approval`(`user_id`, `address_id`, `reason`)
				VALUES(:user_id, :address_id, :reason)'
				);
			$stmt->bindValue(':user_id', $this->id);
			$stmt->bindValue(':address_id', $addressId);
			$stmt->bindValue(':reason', 'address added');
			$stmt->execute();
		}
	}
	public function changeAddress(int $id, string $addr, string $lang_code, boolean $byAdmin=false) : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			UPDATE `address`
			SET
				`language_code` = :lang_code,
				`addr` = :addr
			WHERE id=:id AND `user_id`=:user_id AND `addr`<>:addr
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':lang_code', $lang_code);
		$stmt->bindValue(':addr', $addr);
		$stmt->execute();
		
		if(!$byAdmin)
		{
			if($stmt->rowCount() > 0)
			{
				$stmt = $db->prepare('
					INSERT INTO `address_waiting_approval`(`user_id`, `address_id`, `reason`)
					VALUES(:user_id, :address_id, :reason)
				');
				$stmt->bindValue(':user_id', $this->id);
				$stmt->bindValue(':address_id', $id);
				$stmt->bindValue(':reason', 'address changed');
				$stmt->execute();
			}
			else
			{
				$stmt = $db->prepare('
					UPDATE `address`
					SET
						`language_code` = :lang_code
					WHERE id=:id AND `user_id`=:user_id AND `language_code`<>:lang_code
				');
				$stmt->bindValue(':user_id', $this->id);
				$stmt->bindValue(':id', $id);
				$stmt->bindValue(':lang_code', $lang_code);
				$stmt->execute();
			}
		}
	}
	public function removeAddress(int $id, boolean $byAdmin=false) : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			DELETE FROM `address`
			WHERE `id` = :id AND `user_id`=:user_id
		');
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
	}
	public function enable() : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			UPDATE `user`
			SET `disabled_on` = NULL
			WHERE `id`=:user_id
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
	}
	public function disable() : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			UPDATE `user`
			SET `disabled_on` = DATETIME(\'now\')
			WHERE `id`=:user_id
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->execute();
	}
	public function setPreference(string $key, string $val) : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'UPDATE `user_preference`
			SET
				`val` = :val
			WHERE `key`=:key AND `user_id`=:user_id'
			);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':key', $key);
		$stmt->bindValue(':val', $val);
		$res = $stmt->execute();
		if($res) return;
		
		$stmt = $db->prepare(
			'INSERT INTO `user_preference`(`user_id`, `key`, `val`)
			VALUES(:user_id, :key, :val)'
			);
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':key', $key);
		$stmt->bindValue(':val', $val);
		$res = $stmt->execute();
		if(!$res)
		{
			var_dump($stmt->errorInfo());
			die();
		}
	}
	public function getEmail() : array // ['email' => string, 'polite_name' => string]
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'SELECT
				`email`,
				CASE WHEN LENGTH(`polite_name`)>0 THEN `polite_name` ELSE `login` END as `polite_name`
			FROM `user`
			WHERE `id` = :id'
			);
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
	public function getTextInformation() : array // ['about' => string, 'desires' => string, 'hobbies' => string, 'phobias' => string, 'languages' => string]
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'SELECT
				`about`, `desires`, `hobbies`, `phobias`, `languages`
			FROM `user`
			WHERE `id` = :id'
			);
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
	public function getActiveLocation() : array // ['id' => int, 'code' => string, 'name' => string]
	{
		$locationId = $this->homeLocationId;
		if($this->travellingLocationId > 0)
		{
			$locationId = $this->travellingLocationId;
		}
		return Location::getLocationById($locationId);
	}
	public function getHomeLocation() : array // ['id' => int, 'code' => string, 'name' => string]
	{
		return Location::getLocationById($this->homeLocationId);
	}
	public function isTravelling() : bool
	{
		return ($this->travellingLocationId > 0);
	}
	public function getTravellingLocation() : array // ['id' => int, 'code' => string, 'name' => string]
	{
		if($this->travellingLocationId > 0)
		{
			return Location::getLocationById($this->travellingLocationId);
		}
		throw new Exception('User is not travelling');
	}
	public function updateHomeLocation(int $homeLocationId) : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `user` SET `home_location_id` = :home_location_id WHERE `id`=:id');
		$stmt->bindParam(':home_location_id', $homeLocationId);
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
		
		$this->setPreference('home_location', Location::getCodeById($homeLocationId)); // TODO: remove
		
		$this->homeLocationId = $homeLocationId;
	}
	public function updateTravellingLocation(int $travellingLocationId) : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `user` SET `travelling_location_id` = :travelling_location_id WHERE `id`=:id');
		$stmt->bindParam(':travelling_location_id', $travellingLocationId);
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
		
		$this->travellingLocationId = $travellingLocationId;
	}
	public function removeTravellingLocation() : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `user` SET `travelling_location_id` = NULL WHERE `id`=:id');
		$stmt->bindParam(':id', $this->id);
		$res = $stmt->execute();
		
		$this->travellingLocationId = -1;
	}
	public function reasonCannotSend() : string
	{
		$hasAddress = $this->hasAddress();
		$countCardsTravelling = $this->countCardsTravelling(2147483647);
		$countCardsWaiting = $this->countCardsWaiting(2147483647);
		$countCardsSent = $this->countCardsSent();
		$countCardsReceived = $this->countCardsReceived();
		
		if(!$hasAddress and $countCardsTravelling > 0) return 'NO ADDRESS';
		if($countCardsTravelling >= 24 and $countCardsSent < 6) return '24 TRAVELLING LT6 SENT';
		if($countCardsTravelling >= 24 and $countCardsSent >= 6 and ($countCardsWaiting+$countCardsReceived) == 0) return '24 TRAVELLING 6 SENT 0 WAITING+RECEIVED';
		if($countCardsSent >= 24 and ($countCardsWaiting+$countCardsReceived) == 0) return '24 SENT 0 WAITING+RECEIVED';
		
		return '';
	}
	public function producePasswordRecoverySecret() : string
	{
		$secret = '';
		$alphabet = array_merge(
			range(0, 9),
			range('a', 'z'),
			range('A', 'Z'),
//			str_split('!@#$%^&*()_+-={}[]`~.,:;?')
		);
		
		for ($i = 0; $i < 40; ++$i)
		{
			$secret .= $alphabet[array_rand($alphabet)];
		}
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			INSERT INTO `user_password_recover_secret`(`user_id`, `secret_code`)
			VALUES(:user_id, :secret)
			ON CONFLICT(`user_id`) DO
			UPDATE SET `secret_code`=:secret
		');
		$stmt->bindValue(':user_id', $this->id);
		$stmt->bindValue(':secret', $secret);
		$stmt->execute();
		
		return $secret;
	}
	
	public function getMainImage() : Image
	{
		$graphine = new Graphine($this->id, $this->login);
		$uploadedImages = $this->getUploadedImages();
		if(count($uploadedImages) > 0)
		{
			return $uploadedImages[0];
		}
		return Graphine::constructByUser($this);
	}
	public function getUploadedImages() : array // [ PicturePhoto ]
	{
		return PicturePhoto::constructByUser($this);
	}
	public function isAdmin() : bool
	{
		return $this->id==1;
	}
	public function getTravellingPostcards() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `sent_at`, CAST(JulianDay("now") - JulianDay(`sent_at`) AS INTEGER) AS `days_travelling`,
				`postcard`.`code` AS `postcard_code`,
				`receiver`.`login` AS `receiver_login`, `receiver`.`polite_name` AS `receiver_polite_name`,
				`location_code`.`code` AS `loc_code`, `location_code`.`name` AS `loc_name`,
				`postcard_first_image`.`image_hash` as `first_image_hash`
			FROM `postcard`
			INNER JOIN `user` AS `receiver` ON `postcard`.`receiver_id` = `receiver`.`id`
			INNER JOIN `location_code` ON `location_code`.`id` = `postcard`.`receive_location_id`
			LEFT JOIN (
				SELECT `postcard_id`, `hash` AS `image_hash`, `extension` AS `image_extension`
				FROM `postcard_image`
				GROUP BY `postcard_id`
				HAVING `id` = MIN(`id`)
			) AS `postcard_first_image` ON `postcard_first_image`.`postcard_id` = `postcard`.`id`
			WHERE `received_at` IS NULL AND `sender_id`=:sender_id
			ORDER BY `sent_at` DESC
		');
		$stmt->bindValue(':sender_id', $this->getId());
		$res = $stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function getSentPostcards() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `sent_at`, `received_at`, CAST(JulianDay(`received_at`) - JulianDay(`sent_at`) AS INTEGER) AS `days_travelled`,
				`postcard`.`code` AS `postcard_code`,
				`receiver`.`login` AS `receiver_login`, `receiver`.`polite_name` AS `receiver_polite_name`,
				`location_code`.`code` AS `loc_code`, `location_code`.`name` AS `loc_name`,
				`postcard_first_image`.`image_hash` as `first_image_hash`
			FROM `postcard`
			INNER JOIN `user` AS `receiver` ON `postcard`.`receiver_id` = `receiver`.`id`
			INNER JOIN `location_code` ON `location_code`.`id` = `postcard`.`receive_location_id`
			LEFT JOIN (
				SELECT `postcard_id`, `hash` AS `image_hash`, `extension` AS `image_extension`
				FROM `postcard_image`
				GROUP BY `postcard_id`
				HAVING `id` = MIN(`id`)
			) AS `postcard_first_image` ON `postcard_first_image`.`postcard_id` = `postcard`.`id`
			WHERE `received_at` IS NOT NULL AND `sender_id`=:sender_id
			ORDER BY `sent_at` DESC
		');
		$stmt->bindValue(':sender_id', $this->getId());
		$res = $stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function getReceivedPostcards() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `sent_at`, `received_at`, CAST(JulianDay(`received_at`) - JulianDay(`sent_at`) AS INTEGER) AS `days_travelled`,
				`postcard`.`code` AS `postcard_code`,
				`sender`.`login` AS `sender_login`, `sender`.`polite_name` AS `sender_polite_name`,
				`location_code`.`code` AS `loc_code`, `location_code`.`name` AS `loc_name`,
				`postcard_first_image`.`image_hash` as `first_image_hash`
			FROM `postcard`
			INNER JOIN `user` AS `sender` ON `postcard`.`sender_id` = `sender`.`id`
			INNER JOIN `location_code` ON `location_code`.`id` = `postcard`.`send_location_id`
			LEFT JOIN (
				SELECT `postcard_id`, `hash` AS `image_hash`, `extension` AS `image_extension`
				FROM `postcard_image`
				GROUP BY `postcard_id`
				HAVING `id` = MIN(`id`)
			) AS `postcard_first_image` ON `postcard_first_image`.`postcard_id` = `postcard`.`id`
			WHERE `received_at` IS NOT NULL AND `receiver_id`=:receiver_id
			ORDER BY `sent_at` DESC
		');
		$stmt->bindValue(':receiver_id', $this->getId());
		$res = $stmt->execute();
		
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function getUserInfo() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				CASE WHEN LENGTH(`polite_name`)>0 THEN `polite_name` ELSE `login` END as `polite_name`,
				Cast(JulianDay("now") - JulianDay(`registered_at`) AS INTEGER) `days_registered`,
				Cast(JulianDay("now") - JulianDay(`loggedin_at`) AS INTEGER) `days_since_last_login`,
				`birthday`,
				`location_code`.`code` AS `home_location_code`, `location_code`.`name` AS `home_location`,
				`about`, `desires`, `hobbies`, `phobias`, `languages`
			FROM `user`
				LEFT JOIN (SELECT * FROM `user_preference` WHERE `key`="home_location") AS `home` ON `home`.`user_id`=`user`.`id`
				LEFT JOIN `location_code` ON `location_code`.`code`=`home`.`val`
			WHERE `login`=:login
		');
		$stmt->bindValue(':login', $this->getLogin());
		$stmt->execute();
		if(!($row = $stmt->fetch(PDO::FETCH_ASSOC)))
		{
			return [];
		}
		return $row;
	}
	public function getUserAddresses() : array
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `language_code`, `addr`
			FROM `address`
			WHERE `user_id`=:user_id
		');
		$stmt->bindValue(':user_id', $this->getId());
		$stmt->execute();
		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $res;
	}
}