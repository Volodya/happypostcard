<?php

class Card
{
	private int $id;
	private string $code;
	private int $senderId;
	private int $receiverId;
	private int $sendLocationId;
	private int $receiveLocationId;
	private string $sentAt;
	private string $receivedAt;
	private int $type;
	
	private function __construct()
	{
	}
	
	public static function constructByArray(Array $array) : Card
	{
		$new = new Card;
		$new->id = intval($array['id']);
		$new->code = $array['code'];
		$new->senderId = intval($array['sender_id']);
		$new->receiverId = intval($array['receiver_id']);
		$new->sendLocationId = intval($array['send_location_id']);
		$new->receiveLocationId = intval($array['receive_location_id']);
		$new->sentAt = $array['sent_at'];
		$new->receivedAt = $array['received_at']===null ? '' : $array['received_at'];
		$new->type = empty($array['type']) ? 0 : $array['type'];
		return $new;
	}
	public static function constructById(int $id) : Card
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `code`, `sender_id`, `receiver_id`, `sent_at`, `received_at`,
				`send_location_id`, `receive_location_id`, `type`
			FROM `postcard` WHERE `id`=:id
		');
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			throw new Exception('No such card');
		}
		return Card::constructByArray($result);
	}
	public static function constructByCode(string $code) : Card
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT `id`, `code`, `sender_id`, `receiver_id`, `sent_at`, `received_at`,
				`send_location_id`, `receive_location_id`, `type`
			FROM `postcard` WHERE `code`=:code
		');
		$stmt->bindParam(':code', $code);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			throw new Exception('No such card');
		}
		return Card::constructByArray($result);
	}
	
	private static function generateRecepientId(
		UserExisting $sender,
		array $algorithms = ['karma', 'new', 'any'],
		int $backoffDaysTravelling = 60,
		int $backoffDaysArrived = 14
	) : int
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			CREATE TEMPORARY TABLE `postcard_blocked_users` AS
			SELECT DISTINCT `user`.`id` AS `id`
			FROM `user`
			CROSS JOIN (SELECT :id AS `id`) AS `cur_user`
			WHERE `user`.`id` = `cur_user`.`id`
				OR `user`.`id` NOT IN ( SELECT `user_id` FROM `address` )
				OR `user`.`id` IN
				(
					SELECT `id` AS `disabled_and_travelling`
					FROM `user` WHERE
						`deleted_on` IS NOT NULL OR
						`blocked_on` IS NOT NULL OR
						`disabled_on` IS NOT NULL OR
						`travelling_location_id` IS NOT NULL
					
					UNION ALL
					
					SELECT `id` AS `month_not_logged_in`
					FROM `user`
					WHERE `loggedin_at` < DATETIME("now", "-30 day")
					
					UNION ALL
					
					SELECT `user_id` FROM `user_waiting_approval`
					
					UNION ALL
					
					SELECT `enemy_user_id` AS `have_blacklisted_us`
					FROM `user_blacklist` WHERE `user_id`=`cur_user`.`id`
					
					UNION ALL
					
					SELECT `user_id` AS `we_have_blacklisted`
					FROM `user_blacklist` WHERE `enemy_user_id`=`cur_user`.`id`
					
					UNION ALL
					
					SELECT `receiver_id` AS `waiting_5_sent_nothing`
					FROM `postcard`
					WHERE `receiver_id` NOT IN (SELECT `sender_id` FROM `postcard`)
					GROUP BY `receiver_id`
					HAVING COUNT(*)>=5
					
					UNION ALL
					
					SELECT `receiver_id` AS `waiting_15_sent_nothing_that_arrived`
					FROM `postcard`
					WHERE `receiver_id` NOT IN (SELECT `sender_id` FROM `postcard` WHERE `received_at` IS NOT NULL)
					GROUP BY `receiver_id`
					HAVING COUNT(*)>=15
					
					UNION ALL
					
					SELECT `waiting_count`.`receiver_id`  AS `waiting_count_twice_sent`
					FROM (
						SELECT `receiver_id`, COUNT(*) AS `cnt` FROM `postcard` GROUP BY `receiver_id`
					) AS `waiting_count`
					INNER JOIN (
						SELECT `sender_id`, COUNT(*) AS `cnt` FROM `postcard` GROUP BY `sender_id`
					) AS `sent_count`
					ON `waiting_count`.`receiver_id`=`sent_count`.`sender_id`
					WHERE `waiting_count`.`cnt` >= MAX(2 * `sent_count`.`cnt`, 5)
				)
		');
		if($stmt===false)
		{
			echo 'Huge error';
			die();
		}
		$stmt->bindValue(':id', $sender->getId());
		$res = $stmt->execute();
		
		if($res === false)
		{
			echo 'SQL Error in generateRecepientId';
			die();
		}
		
		$result = -1;
		foreach($algorithms as $algo)
		{
			switch($algo)
			{
				case 'karma':
					$result = Card::generateRecepientId_karma($db, $sender, $backoffDaysTravelling, $backoffDaysArrived);
					break;
				case 'new':
					$result = Card::generateRecepientId_new($db, $sender);
					break;
				case 'any':
					$result = Card::generateRecepientId_any($db, $sender, $backoffDaysTravelling, $backoffDaysArrived);
					break;
			}
			if($result != -1) return $result;
		}
		return $result;
	}

	private static function generateRecepientId_new(
		$db,
		UserExisting $sender
	) : int
	{
		$stmt = $db->prepare('
			SELECT `user`.`id`
			FROM `user`
			CROSS JOIN (SELECT :id AS `id`) AS `cur_user`
			WHERE `user`.`id` NOT IN
				(
					SELECT `id` AS `common_disabled`
					FROM `postcard_blocked_users`
					
					UNION ALL
					
					SELECT `receiver_id` AS `previously_exchanged`
					FROM `postcard`
					WHERE `sender_id`=`cur_user`.`id`
				)
			ORDER BY Random() LIMIT 1
		');
		if($stmt===false)
		{
			echo 'Huge error';
			die();
		}
		$stmt->bindValue(':id', $sender->getId());
		$res = $stmt->execute();
		
		if($res === false)
		{
			echo 'SQL Error in generateRecepientId';
			die();
		}
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			return -1;
		}
		
		return intval($result['id']);
	}
	private static function generateRecepientId_karma(
		$db,
		UserExisting $sender,
		int $backoffDaysTravelling,
		int $backoffDaysArrived
	) : int
	{
		$stmt = $db->prepare('
			SELECT `user`.`id`
			FROM `user`
			CROSS JOIN (SELECT :id AS `id`) AS `cur_user`
			WHERE `user`.`id` NOT IN
				(
					SELECT `id` AS `common_disabled`
					FROM `postcard_blocked_users`
					
					UNION ALL
					
					SELECT `receiver_id` AS `backoff_travelling`
					FROM `postcard`
					WHERE `sender_id`=`cur_user`.`id`
					AND `sent_at` > DATETIME("now", :backoff_travelling)
					AND `received_at` IS NULL
					
					UNION ALL
					
					SELECT `receiver_id` AS `backoff_arrived`
					FROM `postcard`
					WHERE `sender_id`=`cur_user`.`id`
					AND `sent_at` > DATETIME("now", :backoff_arrived)
					AND `received_at` IS NOT NULL
				)
			AND
				`user`.`id` IN
				(
					SELECT `sent`.`sender_id`
					FROM (
						SELECT `sender_id`, COUNT(*) AS `sent_count`
						FROM `postcard`
						WHERE `received_at` IS NOT NULL
						GROUP BY `sender_id`
					) AS `sent`
					LEFT JOIN (
						SELECT `receiver_id`, COUNT(*) as `waiting_count`
						FROM `postcard`
						GROUP BY `receiver_id`
					) AS `waiting`
					ON `sent`.`sender_id` = `waiting`.`receiver_id`
					WHERE `waiting_count` < `sent_count`
				)
			ORDER BY Random() LIMIT 1
		');
		if($stmt===false)
		{
			echo 'Huge error';
			die();
		}
		$stmt->bindValue(':id', $sender->getId());
		$stmt->bindValue(':backoff_travelling', "-{$backoffDaysTravelling} day");
		$stmt->bindValue(':backoff_arrived', "-{$backoffDaysArrived} day");
		$res = $stmt->execute();
		
		if($res === false)
		{
			echo 'SQL Error in generateRecepientId';
			die();
		}
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			return -1;
		}
		
		return intval($result['id']);
	}
	private static function generateRecepientId_any(
		$db,
		UserExisting $sender,
		int $backoffDaysTravelling,
		int $backoffDaysArrived
	) : int
	{
		$stmt = $db->prepare('
			SELECT `user`.`id`
			FROM `user`
			CROSS JOIN (SELECT :id AS `id`) AS `cur_user`
			WHERE `user`.`id` NOT IN
				(
					SELECT `id` AS `common_disabled`
					FROM `postcard_blocked_users`
					
					UNION ALL
					
					SELECT `receiver_id` AS `backoff_travelling`
					FROM `postcard`
					WHERE `sender_id`=`cur_user`.`id`
					AND `sent_at` > DATETIME("now", :backoff_travelling)
					AND `received_at` IS NULL
					
					UNION ALL
					
					SELECT `receiver_id` AS `backoff_arrived`
					FROM `postcard`
					WHERE `sender_id`=`cur_user`.`id`
					AND `sent_at` > DATETIME("now", :backoff_arrived)
					AND `received_at` IS NOT NULL
				)
			ORDER BY Random() LIMIT 1
		');
		if($stmt===false)
		{
			echo 'Huge error';
			die();
		}
		$stmt->bindValue(':id', $sender->getId());
		$stmt->bindValue(':backoff_travelling', "-{$backoffDaysTravelling} day");
		$stmt->bindValue(':backoff_arrived', "-{$backoffDaysArrived} day");
		$res = $stmt->execute();
		
		if($res === false)
		{
			echo 'SQL Error in generateRecepientId';
			die();
		}
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result===false)
		{
			return -1;
		}
		
		return intval($result['id']);
	}
	public static function sendCard(
		UserExisting $sender,
		int $sendLocationId,
		array $algorithms = ['karma', 'new', 'any']
	) : Card
	{
		if($sender->getId() == 1)
		{
			//return Card::sendCardToUser($sender, $sendLocationId, UserExisting::constructByLogin(''));
		}
		
		$receiverId = Card::generateRecepientId($sender);
		
		if($receiverId === -1)
		{
			throw new Exception('No address available');
		}
		
		$receiver = UserExisting::constructById($receiverId);
		
		return Card::sendCardToUser($sender, $sendLocationId, $receiver);
	}
	public static function sendCardToUser(UserExisting $sender, int $sendLocationId, UserExisting $receiver, int $type = 0) : Card
	{
		$card['sender_id'] = $sender->getId();
		$card['receiver_id'] = $receiver->getId();
				
		$sendLocation = Location::getLocationById($sendLocationId);
		$receiverLocation = $receiver->getHomeLocation();
		
		$card['send_location_id'] = $sendLocation['id'];
		$card['receive_location_id'] = $receiverLocation['id'];
		
		$card['type'] = $type;
		
		$codePrefix = "{$sendLocation['code']}-{$receiverLocation['code']}";
		
		$db = Database::getInstance();
		$stmt = $db->prepare(
			'INSERT INTO `postcard` (`year`, `sender_id`, `send_location_id`, `receiver_id`, `receive_location_id`, `type`)
			 VALUES(STRFTIME("%Y", "NOW"), :sender_id, :sender_location_id, :receiver_id, :receiver_location_id, :type)'
		);
		$stmt->bindParam(':sender_id', $card['sender_id']);
		$stmt->bindParam(':sender_location_id', $card['send_location_id']);
		$stmt->bindParam(':receiver_id', $card['receiver_id']);
		$stmt->bindParam(':receiver_location_id', $card['receive_location_id']);
		$stmt->bindParam(':type', $card['type']);
		
		$result = $stmt->execute();
		
		$card['id'] = intval($db->lastInsertId());
		$card['sent_at'] = 'CURRENT_TIMESTAMP';
		$card['received_at'] = '';
		
		// CONSIDER: https://stackoverflow.com/a/70317435/2893496
		$stmt = $db->prepare('SELECT `year` FROM `postcard` WHERE `id`=:id');
		
		$stmt->bindValue(':id', $card['id']);
		
		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$year = $result['year'];
		
		$stmt = $db->prepare(
			'SELECT IFNULL(MAX(`id`), 0) `max_id` FROM `postcard`
			WHERE `year`<:year'
		);
		
		$stmt->bindValue(':year', $year);
		
		$result = $stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$previousMax = intval($result['max_id']);
		
		$codeSuffix = $card['id']-$previousMax;
		
		$card['code'] = "{$codePrefix}-{$year}-{$codeSuffix}";
		
		$stmt = $db->prepare('UPDATE `postcard` SET `code`=:code, `num`=:num WHERE `id`=:id');
		
		$stmt->bindValue(':code', $card['code']);
		$stmt->bindValue(':num', $codeSuffix);
		$stmt->bindValue(':id', $card['id']);
		
		$stmt->execute();
		
		return Card::constructByArray($card);
	}
	public static function getRandomCardImage() : Array
	{
		$db = Database::getInstance();
		
		// ORDER BY RANDOM is expensive, need to redo later!
		$stmt = $db->prepare(
			'SELECT `postcard`.`code`, `hash`, `extension`, `mime`
				FROM `postcard_image`
				INNER JOIN `postcard` ON `postcard`.`id`=`postcard_image`.`postcard_id`
				WHERE
					`received_at` NOT NULL
					AND
					`postcard_image`.`id` IN
						(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
				ORDER BY RANDOM() DESC
				LIMIT 1'
			);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row === false) throw new Exception('No cards available');
		return $row;
	}
	public function changeReceiver(int $sendLocationId) : Card
	{
		if($this->isRegistered()) // absurd!
		{
			return $this;
		}
		
		$sender = $this->getSender();
		$receiver_id = Card::generateRecepientId($sender, ['any']);
		if($receiver_id === -1)
		{
			throw new Exception('No address available');
		}
		$receiver = UserExisting::constructById($receiver_id);
		//$receiver = UserExisting::constructByLogin('nataliatabbi'); $receiver_id = $receiver->getId();
		
		$sendLocation = Location::getLocationById($sendLocationId);
		$receiverLocation = $receiver->getHomeLocation();
		
		$db = Database::getInstance();
		$stmt = $db->prepare('SELECT `year`, `num` FROM `postcard` WHERE `id`=:id');
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$year = $result['year'];
		$codeSuffix = $result['num'];
		
		$codePrefix = "{$sendLocation['code']}-{$receiverLocation['code']}";
		$code = "{$codePrefix}-{$year}-{$codeSuffix}";
		
		$stmt = $db->prepare(
			'UPDATE `postcard`
			SET `code`=:code, `receiver_id`=:receiver_id, `receive_location_id`=:receive_location_id,
				`sent_at`=CURRENT_TIMESTAMP
			WHERE `id`=:id'
		);
		
		$stmt->bindValue(':code', $code);
		$stmt->bindValue(':receiver_id', $receiver->getId());
		$stmt->bindValue(':receive_location_id', $receiverLocation['id']);
		$stmt->bindValue(':id', $this->id);
		
		$stmt->execute();
		
		$new = clone $this;
		$new->code = $code;
		$new->receiverId = intval($receiver_id);
		$new->receiveLocationId = intval($receiverLocation['id']);
		return $new;
	}
	public function register() : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('UPDATE `postcard` SET `received_at` = CURRENT_TIMESTAMP WHERE `id`=:id');
		$stmt->bindValue(':id', $this->id);
		$stmt->execute();
		
		$this->receivedAt = 'CURRENT_TIMESTAMP';
	}
	public function isRegistered() : bool
	{
		return !empty($this->receivedAt);
	}
	public function getCode() : string
	{
		return $this->code;
	}
	public function getCardUrl() : string
	{
		return "/card/{$this->code}";
	}
	public function getId() : int
	{
		return $this->id;
	}
	public function getSenderId() : int
	{
		return $this->senderId;
	}
	public function getReceiverId() : int
	{
		return $this->receiverId;
	}
	public function getUserIds() : Array
	{
		return [
			'sender' => $this->senderId,
			'receiver' => $this->receiverId
		];
	}
	public function getOtherUser(UserExisting $user) : UserExisting
	{
		if($user->getId() == $this->receiverId)
		{
			return $this->getSender();
		}
		else if($user->getId() == $this->senderId)
		{
			return $this->getReceiver();
		}
		
		throw new Exception('Not a user in this card');
	}
	public function getSender(): UserExisting
	{
		return UserExisting::constructById($this->senderId);
	}
	public function getReceiver() : UserExisting
	{
		return UserExisting::constructById($this->receiverId);
	}
	public function getSentDateTime() : DateTime
	{
		return new DateTime($this->sentAt);
	}
	public function getReceivedDateTime() : DateTime
	{
		if($this->receivedAt == 'CURRENT_TIMESTAMP') return new DateTime();
		return new DateTime($this->receivedAt);
	}
}