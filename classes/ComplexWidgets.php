<?php

class ComplexWidgets
{
	private Template $template;
	private Array $options;
	
	public function __construct(Template $template, Array $options)
	{
		$this->template = $template;
		$this->options = $options;
	}
	
	public function latestpostcard200thumbs(int $count, bool $link=false) : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `postcard`.`code`, `hash`, `extension`, `mime`, `postcard`.`received_at`
			FROM `postcard_image`
			INNER JOIN `postcard` ON `postcard`.`id`=`postcard_image`.`postcard_id`
			WHERE
				`received_at` NOT NULL
				AND
				`postcard_image`.`id` IN
					(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
			ORDER BY `received_at` DESC
			LIMIT :count
		');
		$stmt->bindValue(':count', $count, PDO::PARAM_INT);
		$stmt->execute();
		$this->postcard200thumbs($stmt->fetchAll(), $link);
	}
	public function postcard200thumbs(array $ar, bool $link) : void
	{
		foreach($ar as $row)
		{
			$received = $row['received_at'] !== null;
			HtmlSnippets::printPostcardThumb200($row['hash'], $row['extension'], $row['code'], $link, false, $received);
		}
	}
	
	public function siteNotices(array $siteNotices) : void
	{
		foreach($siteNotices as $notice)
		{
			?>
			<section class='sitenotice'>
				<h1><?= $notice['header'] ?></h1>
				<strong><?= $notice['text'] ?></strong>
			</section>
			<?php
		}
	}
	public function userNotices(array $userNotices) : void
	{
		foreach($userNotices as $notice)
		{
			?>
			<section class='usernotice'>
				<h1>Notice</h1>
				<?= $notice ?>
			</section>
			<?php
		}
	}
	public function errors(array $errors) : void
	{
		foreach($errors as $error)
		{
			?>
			<section class='error'>
				<h1>Error</h1>
				<?= $error ?>
			</section>
			<?php
		}
	}
	
	public function card_information(string $cardCode, User $user)
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				`postcard`.`id`,
				`sender`.`id` AS `sender_id`,
				`sender`.`login` AS `sender_login`,
				CASE WHEN LENGTH(`sender`.`polite_name`)<>0 THEN `sender`.`polite_name` ELSE `sender`.`login` END AS `sender_name`,
				`receiver`.`login` AS `receiver_login`,
				CASE WHEN LENGTH(`receiver`.`polite_name`)<>0 THEN `receiver`.`polite_name` ELSE `receiver`.`login` END AS `receiver_name`,
				`receiver`.`id` as `receiver_id`,
				`sent_loc`.`code` AS `sent_location_code`, `sent_loc`.`name` AS `sent_location_name`,
				`received_loc`.`code` AS `received_location_code`, `received_loc`.`name` AS `received_location_name`,
				`postcard`.`sent_at`, `postcard`.`received_at`,
				CAST(JulianDay(`received_at`) - JulianDay(`sent_at`) AS INTEGER) AS `days_travelled`,
				CAST(JulianDay("now") - JulianDay(`sent_at`) AS INTEGER) AS `days_travelling`,
				`type`,
				COALESCE(`count_images`, 0) AS `count_images`
			FROM `postcard`
				INNER JOIN `user` `sender` ON `postcard`.`sender_id` = `sender`.`id`
				INNER JOIN `user` `receiver` ON `postcard`.`receiver_id` = `receiver`.`id`
				INNER JOIN `location_code` AS `sent_loc` ON `postcard`.`send_location_id`=`sent_loc`.`id`
				INNER JOIN `location_code` AS `received_loc` ON `postcard`.`receive_location_id`=`received_loc`.`id`
				LEFT JOIN (
					SELECT COUNT(*) AS `count_images`, `postcard_id` FROM `postcard_image` GROUP BY `postcard_id`
				) AS `cnt_images` ON `cnt_images`.`postcard_id` = `postcard`.`id`
			WHERE `postcard`.`code` = :postcard_code
		');
		$stmt->bindParam(':postcard_code', $cardCode);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if(!$row)
		{
			?>
				<h1><?= $cardCode ?></h1>
				<div>This postcard does not exist</div>
			<?php
			return;
		}
		
		$cardOfSender = ($user instanceof UserExisting and $row['sender_id'] == $user->getId());
		$cardOfReceiver = ($user instanceof UserExisting and $row['receiver_id'] == $user->getId());
		$cardWasReceived = isset($row['received_at']);
		$allowToChangeReceiver = 
		(
			!$cardWasReceived and
			($row['count_images'] == '0') and
			(intval($row['days_travelling']) <= 3)
		);
		
		$birthdayMark = '';
		if($row['type'] == 2)
		{
			$birthdayMark = ' &#127874';
		}
		
		if(!$cardWasReceived and !$cardOfSender and !$user->isAdmin())
		{
			?>
				<h1><?= $cardCode ?><?= $birthdayMark ?></h1>
				<div>This is not your postcard!</div>
			<?php
			return;
		}
		?>
		<h1><?= $cardCode ?><?= $birthdayMark ?></h1>
		<div class='card_information card_information_sender'><?php
			$wpd='';
			if(substr($row['sent_at'], 5, 5)=='10-01') $wpd = " <a href='/wpd_cards'>Word Postcard Day</a>";
			?>
			<div>Sent: <?php HtmlSnippets::printTimestamp($row['sent_at']) ?><?= $wpd ?></div>
			<div>Sender: <a href='/user/<?= $row['sender_login'] ?>'><?= $row['sender_name'] ?></a></div>
			<div>Sent from: <a href='/location/<?= $row['sent_location_code'] ?>'><?= $row['sent_location_name'] ?></a></div>
		</div>
		<div class='card_information card_information_receiver'>
		<?php
		if($cardWasReceived)
		{
			$wpd='';
			if(substr($row['received_at'], 5, 5)=='10-01') $wpd = " <a href='/wpd_cards'>Word Postcard Day</a>";
			?>
			<div>Received: <?php HtmlSnippets::printTimestamp($row['received_at']) ?><?= $wpd ?></div>
			<div>Days travelled: <?=$row['days_travelled'] ?></div>
			<?php
		}
		else
		{
			?>
			<div>Received: (not yet)</div>
			<div>Days travelling: <?= $row['days_travelling'] ?></div>
			<?php
		}
		if($cardWasReceived or $cardOfSender or $user->isAdmin())
		{
			?>
			<div>Receiver: <a href='/user/<?= $row['receiver_login'] ?>'><?= $row['receiver_name'] ?></a></div>
			<div>Destination: <a href='/location/<?= $row['received_location_code'] ?>'><?= $row['received_location_name'] ?></a></div>
			<?php
			if($allowToChangeReceiver)
			{
				?>
				<div>If you are unable to send this card, you can
					<a href='/changereceiver/<?= $cardCode ?>'>request to change a receiver</a>.
				</div>
				<?php
			}
		}
		else
		{
			?>
			<div>Receiver: <a href='/home'>Happy Postcard user</a></div>
			<?php
		}
		?>
			</div>
		<?php
		if(!$cardWasReceived and $cardOfSender)
		{
			$receiver = UserExisting::constructById($row['receiver_id']);
			
			?>
			<div class='card_information card_information_travelling'>
				<div>Number: <strong><?= $cardCode ?></strong> (write it on your card)</div>
				<div class="addresses"><?php
				foreach($receiver->getUserAddresses() as $addr)
				{
					?><div lang='<?= $addr['language_code'] ?>' class='address'><?= $addr['addr'] ?></div><?php
				}
				?></div>
			</div>
			
			<section>
				<h1>Personal description of the receiver</h1><?php
				$wid = new ComWid_user_info();
				$wid->setEditor($user);
				$wid->setTemplateParameter(['user' => $receiver]);
				$wid->invoke();
				
			?></section><?php
		}
		
		$this->card_gallery(intval($row['id']), $cardCode, $cardOfSender or $cardOfReceiver);
		
		if($cardOfSender or $cardOfReceiver)
		{
			HtmlSnippets::printImageUploadForm('card', $cardCode);
		}
	}
	public function card_gallery(int $cardId, string $cardCode, bool $canEdit=false) : void
	{
		if(!$this->template->isLoggedIn())
		{
			$canEdit=false;
		}
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				`hash`, `extension`, `uploader_profile_id`, JULIANDAY(\'now\') - JULIANDAY(`uploaded_at`) AS `days_since_upload`
			FROM
				`postcard_image`
			WHERE
				`postcard_id` = :postcard_id
		');
		$stmt->bindParam(':postcard_id', $cardId);
		$stmt->execute();
		echo '<div class=\'gallery\'>';
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$canEditThis = (
				$canEdit and
				intval($row['uploader_profile_id']) == $this->template->getUserId() and
				intval($row['days_since_upload']) <= 3
			);
			HtmlSnippets::printPostcardThumb200($row['hash'], $row['extension'], $cardCode, false, $canEditThis, true);
		}
		echo '</div>';
	}
	public function user_status() : bool
	{
		$user = $this->options['user'];
		if(!$user->isEnabled())
		{
			echo 'This user has disabled their profile';
			return true;
		}
		if($user->isTravelling())
		{
			echo 'This user is currently travelling';
			return true;
		}
		if(!$user->hasAddress())
		{
			echo 'This user has not yet set their address';
			return true;
		}
		return false;
	}
	public function inter_user_news(User $userSelf, User $userOther) : void
	{
		if(!($userOther instanceof UserExisting))
		{
			?>
				This user is not registered!
			<?php
			return;
		}
		if($userSelf->getLogin() == $userOther->getLogin())
		{
			?>
				You are this user
			<?php
			return;
		}
		
		?><h1>Your interaction</h1><?php
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT Date(`sent_at`) `sent_at`, `code`,
				`sender`.`login` `sender_login`, `sender`.`polite_name` `sender_name`,
				`receiver`.`login` `receiver_login`, `receiver`.`polite_name` `receiver_name`
			FROM `postcard`
				INNER JOIN `user` `sender` ON `postcard`.`sender_id`=`sender`.`id`
				INNER JOIN `user` `receiver` ON `postcard`.`receiver_id`=`receiver`.`id`
			WHERE
				`receiver`.`login` = :login_self AND `sender`.`login` = :login_other AND `postcard`.`received_at` IS NOT NULL
				OR
				`receiver`.`login` = :login_other AND `sender`.`login` = :login_self
			ORDER BY `sent_at`
		');
		$stmt->bindValue(':login_self', $userSelf->getLogin());
		$stmt->bindValue(':login_other', $userOther->getLogin());
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?>
				<div>
					<?= $row['sent_at'] ?>:
					<a href='/user/<?= $row['sender_login'] ?>'><?= $row['sender_name'] ?></a>
					has sent <a href='/card/<?= $row['code'] ?>'><?= $row['code'] ?></a> to
					<a href='/user/<?= $row['receiver_login'] ?>'><?= $row['receiver_name'] ?></a>
				</div>
			<?php
		}
	}
	public function location_info($locationCode) : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				`l`.`id`,
				`l`.`code`, `l`.`name`, 
				`l`.`iso3166_1_a2`, `l`.`iso3166_1_a3`, `l`.`iso3166_1_num`, `l`.`iso3166_2_ext`, `l`.`un_m49`, `l`.`un_sub`,
				`l`.`itu`, `l`.`ioc`, `l`.`fifa`, `l`.`icao`, `l`.`iata`,
				`l`.`map_link`, `l`.`description_link`,
				`p`.`code` AS `parent`, `p`.`name` AS `parent_name`
			FROM `location_code` AS `l`
				LEFT JOIN `location_code` AS `p` ON `l`.`parent` = `p`.`id`
			WHERE `l`.`code`=:location_code
		');
		$stmt->bindParam(':location_code', $locationCode);
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$year = date("Y");
			
			?>
				<h1><?= $row['name'] ?></h1>
				<p>This location can be selected by anybody who lives there as their home,
				in this case, when they send a postcard it will receive a code in the form
				<u><?= $row['code'] ?>-LOC-<?= $year ?>-number</u> (where LOC is the location code of the receiver).</p>
			<?php
			if(!empty($row['iso3166_1_a2']))
			{
				?><div>ISO-3166-1 (2 letter): <?= $row['iso3166_1_a2'] ?></div><?php
			}
			if(!empty($row['iso3166_1_a3']))
			{
				?><div>ISO-3166-1 (3 letter): <?= $row['iso3166_1_a3'] ?></div><?php
			}
			if(!empty($row['iso3166_1_num']))
			{
				?><div>ISO-3166-1 (numeric): <?= $row['iso3166_1_num'] ?></div><?php
			}
			if(!empty($row['iso3166_2']))
			{
				?><div>ISO-3166-2: <?= $row['iso3166_1_a2'] ?></div><?php
			}
			if(!empty($row['un_m49']))
			{
				?><div>UN-M49: <?= $row['un_m49'] ?></div><?php
			}
			if(!empty($row['un_sub']))
			{
				?><div>UN subdivision code: <?= $row['un_sub'] ?></div><?php
			}
			if(!empty($row['itu']))
			{
				?><div>ITU: <?= $row['itu'] ?></div><?php
			}
			if(!empty($row['ioc']))
			{
				?><div>IOC: <?= $row['ioc'] ?></div><?php
			}
			if(!empty($row['fifa']))
			{
				?><div>FIFA: <?= $row['fifa'] ?></div><?php
			}
			if(!empty($row['icao']))
			{
				?><div>ICAO: <?= $row['icao'] ?></div><?php
			}
			if(!empty($row['iata']))
			{
				?><div>IATA: <?= $row['iata'] ?></div><?php
			}
			if(!empty($row['parent']))
			{
				?><div>This location is a part of:
					<a href='/location/<?= $row['parent'] ?>'><?= $row['parent_name'] ?></a>
				</div><?php
			}
			if(!empty($row['description_link']))
			{
				?><div>More information:
					<a href='<?= $row['description_link'] ?>'><?= $row['description_link'] ?></a>
				</div><?php
			}
			if(!empty($row['map_link']))
			{
				?><div>Map link:
					<a href='https://commons.wikimedia.org/wiki/File:<?= $row['map_link'] ?>'>commons:<?= $row['map_link'] ?></a>
				</div><?php
			}
			$subLocations = Location::getSublocations($row['id']);
			if(!empty($subLocations))
			{
				?><div>Locations within this one: <?php
				$first = true;
				foreach($subLocations as $subLoc)
				{
					if(!$first) echo ', ';
					$first = false;
					?><a href='/location/<?= $subLoc['code'] ?>'><?= $subLoc['name'] ?></a><?php
				}
				?></div><?php
			}
		}
		else
		{
			?><h1>Unknown location</h1>
			There seems to be no location like this. Are you sure you know what you are doing?<?php
		}
	}
	public function travelling_postcards(User $user) : void
	{
		$travelling = $user->getTravellingPostcards();
		
		?><table>
			<thead>
				<th scope='col'>Date</th>
				<th scope='col'>Days travelling</th>
				<th scope='col'>Code</th>
				<th scope='col'>Destination</th>
				<th scope='col'>Receiver</th>
				<th scope='col' title='picture'>&#9215;</th>
			</thead>
			<tbody>
			<?php
			foreach($travelling as $row)
			{
				$date = substr($row['sent_at'], 0, 10);
				$time = substr($row['sent_at'], 11);
				?><tr>
					<td><?php HtmlSnippets::printTimestamp($row['sent_at']) ?></td>
					<td><?= $row['days_travelling'] ?></span></td>
					<td><a href='/card/<?= $row['postcard_code'] ?>'><?= $row['postcard_code'] ?></a></td>
					<td><a href='/location/<?= $row['loc_code'] ?>'><?= $row['loc_name'] ?></a></td>
					<td><?php HtmlSnippets::printUserPoliteName($row['receiver_login'], $row['receiver_polite_name'], true); ?></td>
					<?php
					if(!empty($row['first_image_hash']))
					{
						?><td>&#9745;</td><?php
					}
					else
					{
						?><td>&#9744;</td><?php
					}
					?>
				</tr><?php
			}
			?>
			</tbody>
		</table><?php
	}
	public function sent_postcards() : void
	{
		$user = $this->options['user'];
		if(!isset($user) or !($user instanceof UserExisting))
		{
			echo 'No user';
			return;
		}
		
		?><p>User: <?php HtmlSnippets::printUserPoliteName($user->getLogin(), $user->getPoliteName(), true); ?></p><?php
		
		$sent = $user->getSentPostcards();
		
		?><table>
			<thead>
				<th scope='col'>Date sent</th>
				<th scope='col'>Date received</th>
				<th scope='col'>Days travelled</th>
				<th scope='col'>Code</th>
				<th scope='col'>Destination</th>
				<th scope='col'>Receiver</th>
			</thead>
			<tbody>
			<?php
			foreach($sent as $row)
			{
				?><tr>
					<td><?php HtmlSnippets::printTimestamp($row['sent_at']) ?></td>
					<td><?php HtmlSnippets::printTimestamp($row['received_at']) ?></td>
					<td><?= $row['days_travelled'] ?></span></td>
					<td><a href='/card/<?= $row['postcard_code'] ?>'><?= $row['postcard_code'] ?></a></td>
					<td><a href='/location/<?= $row['loc_code'] ?>'><?= $row['loc_name'] ?></a></td>
					<td><?php HtmlSnippets::printUserPoliteName($row['receiver_login'], $row['receiver_polite_name'], true); ?></td>
				</tr><?php
			}
			?>
			</tbody>
		</table><?php
	}
	public function received_postcards() : void
	{
		$user = $this->options['user'];
		if(!isset($user) or !($user instanceof UserExisting))
		{
			echo 'No user';
			return;
		}
		
		?><p>User: <?php HtmlSnippets::printUserPoliteName($user->getLogin(), $user->getPoliteName(), true); ?></p><?php
		
		$sent = $user->getReceivedPostcards();
		
		?><table>
			<thead>
				<th scope='col'>Date sent</th>
				<th scope='col'>Date received</th>
				<th scope='col'>Days travelled</th>
				<th scope='col'>Code</th>
				<th scope='col'>Sent from</th>
				<th scope='col'>Sender</th>
			</thead>
			<tbody>
			<?php
			foreach($sent as $row)
			{
				?><tr>
					<td><?php HtmlSnippets::printTimestamp($row['sent_at']) ?></td>
					<td><?php HtmlSnippets::printTimestamp($row['received_at']) ?></td>
					<td><?= $row['days_travelled'] ?></span></td>
					<td><a href='/card/<?= $row['postcard_code'] ?>'><?= $row['postcard_code'] ?></a></td>
					<td><a href='/location/<?= $row['loc_code'] ?>'><?= $row['loc_name'] ?></a></td>
					<td><?php HtmlSnippets::printUserPoliteName($row['sender_login'], $row['sender_polite_name'], true); ?></td>
				</tr><?php
			}
			?>
			</tbody>
		</table><?php
	}
	
	public function list_of_users() : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare(
			'
			SELECT
				/*ROWID*/ `user`.`id` as `num`,
				`user`.`login`,
				`user`.`polite_name`,
				`user`.`registered_at`,
				`user`.`loggedin_at`,
				`user`.`birthday`,
				`home_loc`.`home_location`,
				COUNT(DISTINCT `sent_postcard`.`id`) AS `sent_postcards_1`,
				COUNT(DISTINCT `sent_postcard`.`received_at`) AS `sent_postcards_2`,
				COUNT(DISTINCT `received_postcard`.`id`) AS `received_postcard_1`,
				COUNT(DISTINCT `received_postcard`.`received_at`) AS `received_postcard_2`
			FROM
				`user`
				LEFT JOIN
					(SELECT
						`id`,
						`receiver_id`,
						`received_at`
					FROM `postcard`
					) AS `received_postcard`
					ON `received_postcard`.`receiver_id`=`user`.`id`
				LEFT JOIN
					(SELECT
						`id`,
						`sender_id`,
						`received_at`
					FROM `postcard`
					) AS `sent_postcard`
					ON `sent_postcard`.`sender_id`=`user`.`id`
				LEFT JOIN
					(SELECT
						`val` AS `home_location`,
						`user_id`
					FROM `user_preference`
					WHERE `key` = \'home_location\'
					) AS `home_loc`
					ON `user`.`id` = `home_loc`.`user_id`
			GROUP BY `user`.`id`
			HAVING `sent_postcards_1` > 0 OR `sent_postcards_2` > 0 OR `received_postcard_1` > 0 OR `received_postcard_2` > 0
				OR JULIANDAY(\'now\') - JULIANDAY(`user`.`loggedin_at`) < 30
			ORDER BY `user`.`id`
			'
		);
		$res = $stmt->execute();
		?>
			<table>
				<thead><tr>
					<th scope='col'>№</th>
					<th scope='col'>User</th>
					<th scope='col'>Polite name</th>
					<th scope='col'>Home</th>
					<th scope='col'>Registered at</th>
					<th scope='col'>Last login</th>
					<th scope='col'>Birthday</th>
					<th scope='col'>Mailed by</th>
					<th scope='col'>Sent by</th>
					<th scope='col'>Mailed to</th>
					<th scope='col'>Received by</th>
				</tr></thead>
			<tbody>
		<?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?>
				<tr>
					<th scope='row'><?= $row['num'] ?></th>
					<td><a href='/user/<?= $row['login'] ?>'><?= $row['login'] ?></a></td>
					<td><?= $row['polite_name'] ?></td>
					<td><a href='/location/<?= $row['home_location'] ?>'><?= $row['home_location'] ?></a></td>
					<td><?php HtmlSnippets::printTimestamp($row['registered_at']) ?></td>
					<td><?= $row['loggedin_at'] ?></td>
					<td><?= $row['birthday'] ?></td>
					<td><?= $row['sent_postcards_1'] ?></td>
					<td><?= $row['sent_postcards_2'] ?></td>
					<td><?= $row['received_postcard_1'] ?></td>
					<td><?= $row['received_postcard_2'] ?></td>
				</tr>
			<?php
		}
		?>
			</tbody>
			</table>
		<?php
	}
	public function image() : void
	{
		$hash = $this->options['hash'];
		try
		{
			$image = PictureScan::constructByHash($hash);
		}
		catch(Exception $ex)
		{
			try
			{
				$image = PicturePhoto::constructByHash($hash);
			}
			catch(Exception $ex)
			{
				?>No such image<?php
				return;
			}
		}
		?><img src='<?= $image->getThumb800() ?>' /><?php
	}
	public function image_information() : void
	{
		$hash = $this->options['hash'];
		try
		{
			$image = PictureScan::constructByHash($hash);
			$cardIds = $image->getCardIds();
			$first = true;
			foreach( $cardIds as $cardId )
			{
				$card = Card::constructById($cardId);
				if(
					!$card->isRegistered()
					and
						!(
							$this->template->isLoggedIn()
							and
							$this->template->getUserId() == $card->getSenderId()
						) 
				) continue;
				
				if(!$first) echo ', ';
				$first = false;
				?><a href='<?= $card->getCardUrl() ?>'><?= $card->getCode() ?></a><?php
			}
		}
		catch(Exception $ex)
		{}
		try
		{
			$image = PicturePhoto::constructByHash($hash);
			$userId = $image->getUserId();
			$user = UserExisting::constructById($userId);
			HtmlSnippets::printUserPoliteName($user->getLogin(), $user->getPoliteName(), true);
		}
		catch(Exception $ex)
		{}
	}
	public function user_info_edit_travelling() : void
	{
		$user = $this->options['user'];
		if(!isset($user) or !($user instanceof UserExisting))
		{
			echo 'User is not set';
			return;
		}
		if($user->isTravelling())
		{
			$travellingLocation = $user->getTravellingLocation();
			?>
			<form action='/perform_useredittravelling' method='POST'>
				You are currently in the travel mode. You can:
				<input type='hidden' name='travelling_location' value='off' />
				<button type='submit'>End travelling</button>
			</form>
			<form action='/perform_useredittravelling' method='POST'>
				Alternatively you can change your travel location:
				<select name='travelling_location' id='travelling_location'>
					<?php HtmlSnippets::printLocationSelectOptionList($travellingLocation['code']); ?>
				</select>
				<?php HtmlSnippets::printLocationSelection_CodeEntry('travelling_location'); ?>
				<?php HtmlSnippets::printLocationSelection_About('travelling_location'); ?>
				<button type='submit'>Change</button>
			</form>
			<?php
		}
		else
		{
			$homeLocation = $user->getHomeLocation();
			?>
			<form action='/perform_useredittravelling' method='POST'>
				If you are travelling, you can set your place:
				<select name='travelling_location' id='travelling_location'>
					<?php HtmlSnippets::printLocationSelectOptionList($homeLocation['code']); ?>
				</select>
				<?php HtmlSnippets::printLocationSelection_About('travelling_location_id'); ?>
				<button type='submit'>Set</button>
			</form>
			<p>Note: If you set travelling location, you will not be receiving any postcards until you end your travels.</p>
			<?php
		}
	}
	public function select_birthday_recepient() : void
	{
		if(!$this->template->isLoggedIn())
		{
			echo 'Must be logged in';
			return;
		}
		$sender = $this->template->getUser();
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT * FROM (
				SELECT `id`, `login`, `polite_name`,
					CASE WHEN JULIANDAY(SUBSTR(DATE(\'now\'), 1, 5) || SUBSTR(`birthday`, 6, 5)) > JULIANDAY(\'now\')
					THEN CAST(
							JULIANDAY(SUBSTR(DATE(\'now\'), 1, 5) || SUBSTR(`birthday`, 6, 5)) - JULIANDAY(\'now\')
						AS INTEGER)
					ELSE CAST(
							JULIANDAY(SUBSTR(DATE(\'now\'), 1, 5) || \'12-31\') - JULIANDAY(\'now\') +
							JULIANDAY(SUBSTR(DATE(\'now\'), 1, 5) || SUBSTR(`birthday`, 6, 5)) -
							JULIANDAY(SUBSTR(DATE(\'now\'), 1, 5) || \'01-01\')
						AS INTEGER)
					END AS `days_left`,
					CAST(JULIANDAY(\'now\') - JULIANDAY(`last_sent_at`) AS INTEGER) AS `sent_days_ago`
				FROM `user`
				LEFT JOIN (
					SELECT `receiver_id`, MAX(`sent_at`) AS `last_sent_at`
					FROM `postcard`
					WHERE `sender_id` = :sender_id AND `type` = 2
					GROUP BY `receiver_id`
				) AS `last_birthday_card` ON `user`.`id` = `last_birthday_card`.`receiver_id`
				WHERE
					`deleted_on` IS NULL
					AND `blocked_on` IS NULL
					AND `disabled_on` IS NULL
					AND JULIANDAY(\'now\') - JULIANDAY(`user`.`loggedin_at`) < 30
			) AS `t`
			WHERE `days_left` < 90
			ORDER BY `days_left`
		');
		$stmt->bindValue(':sender_id', $sender->getId());
		$stmt->execute();
		?>
			<form method='POST' action='/performselectaddress'>
			<input type='hidden' name='type' value='2' />
			<table>
				<thead><tr>
					<th scope='col'>Send</th>
					<th scope='col'>User</th>
					<th scope='col'>Days left</th>
				</tr></thead>
			<tbody>
		<?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?>
				<tr>
					<?php
					if(is_numeric($row['sent_days_ago']) and intval($row['sent_days_ago']) <= 90)
					{
						?><td>&#127874;</td><?php
					}
					else if($row['id'] == $sender->getId())
					{
						?><td>&#9786;</td><?php
					}
					else
					{
						?><td><input type='radio' name='receiver_login' value='<?= $row['login'] ?>' /></td><?php
					}
					?>
					<td><?php HtmlSnippets::printUserPoliteName($row['login'], $row['polite_name'], true); ?></td>
					<td><?= $row['days_left'] ?></td>
				</tr>
			<?php
		}
		?>
			</tbody>
			</table>
			<div><label>Your location:
				<select name='location'>
					<?php HtmlSnippets::printLocationSelectOptionList($sender->getActiveLocation()['code']); ?>
				</select>
			</label></div>
			<div>
				<label>
					Confirm that you are willing to send a postcard to a chosen person.
					<input type='checkbox' name='confirm' />
				</label>
			</div>
			<div><button>Get an address</button></div>
			</form>
		<?php
	}
	public function latestpostcards_interuser() : bool
	{
		try
		{
			$user = $this->template->getUser();
			if(isset($this->options['card_code']))
			{
				$card = Card::constructByCode($this->options['card_code']);
				$cardUserIds = $card->getUserIds();
				$secondId = $cardUserIds['sender'];
				$thirdId = $cardUserIds['receiver'];
				$cardCodeToExclude=$card->getCode();
				unset($card);
				unset($cardUserIds);
			}
			else if(isset($this->options['user']))
			{
				$secondId = $this->options['user']->getId();
				$thirdId = -1;
				$cardCodeToExclude='';
			}
			else
			{
				return false;
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `postcard`.`code`, `hash`, `extension`, `mime`, `postcard`.`received_at`
			FROM `postcard_image`
			INNER JOIN `postcard` ON `postcard`.`id`=`postcard_image`.`postcard_id`
			WHERE
				(
					(`sender_id` = :user_id AND `receiver_id` = :second_user_id)
					OR
					(`sender_id` = :user_id AND `receiver_id` = :third_user_id)
					OR
					(`sender_id` = :second_user_id AND `receiver_id` = :user_id AND `received_at` NOT NULL)
					OR
					(`sender_id` = :third_user_id AND `receiver_id` = :user_id AND `received_at` NOT NULL)
				)
				AND
				`postcard_image`.`id` IN
					(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
				AND
				`postcard`.`code` != :card_code
			ORDER BY `sent_at` DESC
			LIMIT :count
		');
		$stmt->bindValue(':user_id', $user->getId());
		$stmt->bindValue(':second_user_id', $secondId);
		$stmt->bindValue(':third_user_id', $thirdId);
		$stmt->bindValue(':card_code', $cardCodeToExclude);
		$stmt->bindValue(':count', 99, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchAll();
		
		if(empty($res)) return false;
		
		$this->postcard200thumbs($res, true);
		
		return true;
	}
	public function latestpostcards_location() : bool
	{
		$location = Location::getLocationByCode($this->options['location_code']);
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `postcard`.`code`, `hash`, `extension`, `mime`, `postcard`.`received_at`
			FROM `postcard_image`
			INNER JOIN `postcard` ON `postcard`.`id`=`postcard_image`.`postcard_id`
			WHERE
				(
					`postcard`.`send_location_id` = :location_id
					OR
					`postcard`.`receive_location_id` = :location_id
				)
				AND
					`postcard`.`received_at` IS NOT NULL
				AND
					`postcard_image`.`id` IN
						(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
			ORDER BY `sent_at` DESC
			LIMIT :count
		');
		$stmt->bindValue(':location_id', $location['id']);
		$stmt->bindValue(':count', 99, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchAll();
		
		if(empty($res)) return false;
		
		$this->postcard200thumbs($res, true);
		
		return true;
	}
	
	public function user_main_image() : void
	{
		if(isset($this->options['user']))
		{
			$user = $this->options['user'];
		}
		else
		{
			$card = Card::constructByCode($this->options['card_code']);
			if($this->template->isLoggedIn())
			{
				try
				{
					$user = $card->getOtherUser($this->template->getUser());
				}
				catch(Exception $e)
				{
					$user = $card->getSender();
				}
			}
			else
			{
				$user = $card->getSender();
			}
		}
		?><div><a href='/userphotos/<?= $user->getLogin() ?>'>all photos</a></div><?php
		$image = $user->getMainImage();
		HtmlSnippets::printPhotoThumb200($image, $user->getLogin(), true, false);
	}
	
	public function user_disable() : void
	{
		$user = $this->options['user'];
		$enabled = $user->isEnabled();
		if($enabled)
		{
			?><h1>Disable postal exchange temporarily</h1><?php
		}
		else
		{
			?><h1>Enable postcard exchange</h1><?php
		}
		
		?>
		<form action='/performenable' method='post' id='disable_user_form'>
			<input type='hidden' name='login' value='<?= $user->getLogin() ?>' />
			I am 
			<?php if($enabled) { ?>
				<input type='hidden' name='enabled' value='off' />
				<button type='submit'>✋ unable to receive ✋</button>
			<?php } else { ?>
				<input type='hidden' name='enabled' value='on' />
				<button type='submit'>✌ able to receive ✌</button>
			<?php } ?>
			 postcards.
		</form>
		<?php
		if($enabled)
		{
			?><dialog id='disable_user_dialog'>
				<p>You are about to disable your account. This means other people will no longer
					get your address to send postcards to you, until you reenable it later.</p>
				<button id='disable_user_confirm_button'>Disable the account</button>
				<button id='disable_user_reject_button'>Do not disable</button>
			</dialog>
			<script>
				const disableUserDialog = document.getElementById('disable_user_dialog');
				const disableUserForm = document.getElementById('disable_user_form');
				const disableUserConfirmButton = document.getElementById('disable_user_confirm_button');
				const disableUserRejectButton = document.getElementById('disable_user_reject_button');
				disableUserForm.addEventListener('submit', (event) => {
					event.preventDefault();
					try
					{
						disableUserDialog.showModal();
					}
					catch(ex) <?php // fall back ?>
					{
						if(confirm('Do you really want to disable your account?'))
						{
							disableUserForm.submit();
						}
					}
				});
				disableUserConfirmButton.addEventListener('click', (event) => {
					disableUserForm.submit();
				});
				disableUserRejectButton.addEventListener('click', (event) => {
					disableUserDialog.close();
				});
			</script><?php
		}
	}
	
	public function user_info_add_photo() : void
	{
		$user = $this->options['user'];
		
		HtmlSnippets::printImageUploadForm('photo', $user->getLogin());
	}
	
	public function user_photographs() : bool
	{
		$viewer = $this->template->getUser();
		$user = $this->options['user'];
		$photos = $user->getUploadedImages();
		
		?><h1>Photographs</h1><?php
		?><p>User: <?php HtmlSnippets::printUserPoliteName($user->getLogin(), $user->getPoliteName(), true); ?></p><?php
		
		$viewOfSelf = ($viewer->getId() == $user->getId());
		$prev = null;
		foreach($photos as $photo)
		{
			if($viewOfSelf and $prev != null)
			{
				?><form method='post' action='/performswapimageposition'><?php
					?><button class='thumblike'>&#8644;</button><?php
					?><input type='hidden' name='type' value='photo' /><?php
					?><input type='hidden' name='what' value='<?= $user->getLogin() ?>' /><?php
					?><input type='hidden' name='a' value='<?= $prev->getHash() ?>' /><?php
					?><input type='hidden' name='b' value='<?= $photo->getHash() ?>' /><?php
				?></form><?php
			}
			HtmlSnippets::printPhotoThumb200($photo, $user->getLogin(), false, $viewOfSelf);
			$prev = $photo;
		}
		
		return true;
	}
	public function world_postcard_day_postcards() : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `postcard`.`code`, `hash`, `extension`, `mime`, `postcard`.`received_at`
			FROM `postcard_image`
			INNER JOIN `postcard` ON `postcard`.`id`=`postcard_image`.`postcard_id`
			WHERE
				`received_at` NOT NULL
				AND
				`postcard_image`.`id` IN
					(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
				AND STRFTIME(\'%m%d\', `postcard`.`sent_at`) = \'1001\'
			ORDER BY `received_at` DESC
		');
		$stmt->execute();
		$this->postcard200thumbs($stmt->fetchAll(), true);
	}
	public function text(string $html) : void
	{
		echo $html;
	}
}