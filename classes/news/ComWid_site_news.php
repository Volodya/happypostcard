<?php

class ComWid_site_news implements ComWid
{
	private User $user;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->user = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT * FROM(
				SELECT "new_user" as `type`, `registered_at` AS `ts`, `login`,
					CASE WHEN LENGTH(`polite_name`)<>0 THEN `polite_name` ELSE `login` END AS `polite_name`,
					NULL as `card_code`
					FROM `user`
				UNION ALL
				SELECT "postcard_sent_received", `sent_at`, `login`, 
					CASE WHEN LENGTH(`polite_name`)<>0 THEN `polite_name` ELSE `login` END AS `polite_name`,
					`code`
					FROM `postcard` INNER JOIN `user` ON `postcard`.`sender_id`=`user`.`id`
					WHERE `received_at` IS NOT NULL
				UNION ALL
				SELECT "postcard_sent", `sent_at`, `login`, 
					CASE WHEN LENGTH(`polite_name`)<>0 THEN `polite_name` ELSE `login` END AS `polite_name`,
					`code`
					FROM `postcard` INNER JOIN `user` ON `postcard`.`sender_id`=`user`.`id`
					WHERE `received_at` IS NULL
				UNION ALL
				SELECT "postcard_received", `received_at`, `login`, 
					CASE WHEN LENGTH(`polite_name`)<>0 THEN `polite_name` ELSE `login` END AS `polite_name`,
					`code` FROM `postcard` INNER JOIN `user` ON `postcard`.`receiver_id`=`user`.`id`
					WHERE `received_at` IS NOT NULL
			) AS t ORDER BY `ts` DESC LIMIT 150
		');
		$stmt->execute();
		
		$rows = [];
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$collapsed = false;
			$row['Date'] = new DateTime($row['ts']);
			foreach($rows as $rowKey => $rowVal)
			{
				$dateDiff = $row['Date']->diff($rowVal['Date']);
				if($rowVal['type'] == $row['type'] and $rowVal['login'] == $row['login'] and 
					($dateDiff->h < 3 and $dateDiff->d == 0 and $dateDiff->m == 0 and $dateDiff->y == 0)
				)
				{
					$rows[$rowKey]['card_code'][] = $row['card_code'];
					$collapsed = true;
					break;
				}
			}
			if(!$collapsed)
			{
				$row['card_code'] = [ $row['card_code'] ];
				$rows[] = $row;
			}
		}
		$rows = array_slice($rows, 0, 15);
		
		foreach($rows as $row)
		{
			$len = count($row['card_code']);
			
			echo '<div class="newsitem">';
			$userLink = HtmlSnippets::getUserPoliteName($row['login'], $row['polite_name'], true);
			switch($row['type'])
			{
				case 'new_user':
					echo '&#12320; ';
					echo "{$userLink} has joined  «Happy Postcard».";
					break;
				case 'postcard_sent_received':
					echo '&#128238; ';
					if($len > 2)
					{
						?><?= $userLink ?> has sent happy postcards:
						<?php
						$first = true;
						foreach($row['card_code'] as $cardCode)
						{
							if(!$first)
							{
								echo ', ';
								if($len == 1)
								{
									echo 'and ';
								}
							}
							$first = false;
							?><a href='/card/<?= $cardCode ?>'>📨</a><?php
							--$len;
						}
					}
					else if($len == 2)
					{
						?><?= $userLink ?> has sent happy postcards:
						<?php
						$first = true;
						foreach($row['card_code'] as $cardCode)
						{
							if(!$first)
							{
								echo ' and ';
							}
							$first = false;
							?><a href='/card/<?= $cardCode ?>'>📨</a><?php
						}
					}
					else
					{
						?><?= $userLink ?> has sent a <a href='/card/<?= $row['card_code'][0] ?>'>happy postcard</a><?php
					}
					echo '.';
					break;
				case 'postcard_sent':
					echo '&#128238; ';
					if($len > 1)
					{
						?><?= $userLink ?> has sent <?php HtmlSnippets::printCircledDigits($len) ?> happy postcards.<?php
					}
					else
					{
						?><?= $userLink ?> has sent a <span title='Number hidden'>happy postcard</span>.<?php
					}
					break;
				case 'postcard_received':
					echo '&#128236; ';
					if($len > 2)
					{
						?><?= $userLink ?> has received <?php HtmlSnippets::printCircledDigits($len) ?> happy postcards:
						<?php
						$first = true;
						foreach($row['card_code'] as $cardCode)
						{
							if(!$first)
							{
								echo ', ';
								if($len == 1)
								{
									echo 'and ';
								}
							}
							$first=false;
							?><a href='/card/<?= $cardCode ?>'>📩</a><?php
							--$len;
						}
					}
					else if($len == 2)
					{
						?><?= $userLink ?> has received <?php HtmlSnippets::printCircledDigits($len) ?> happy postcards:
						<?php
						$first = true;
						foreach($row['card_code'] as $cardCode)
						{
							if(!$first)
							{
								echo ' and ';
							}
							$first=false;
							?><a href='/card/<?= $cardCode ?>'>📩</a><?php
						}
					}
					else
					{
						?><?= $userLink ?> has received a <a href='/card/<?= $row['card_code'][0] ?>'>happy postcard</a><?php
					}
					echo '.';
					break;
			}
			echo '</div>';
		}
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}