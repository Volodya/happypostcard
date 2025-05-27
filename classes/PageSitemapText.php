<?php

class PageSitemapText implements Page
{
	public function __construct()
	{
	}
	public function withExtraSiteNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraUserNotices(array $userNotices) : Page
	{
		return $this;
	}
	public function withExtraErrors(array $userNotices) : Page
	{
		return $this;
	}
	public function withUser(User $user) : Page
	{
		return $this;
	}
	public function applyAdditionalHeaders() : void
	{}
	public function contentType() : string
	{
		return 'text/plain';
	}
	public function toString() : string
	{
		$content='';
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('SELECT `code` FROM `postcard` WHERE `received_at` IS NOT NULL');
		$res = $stmt->execute();
		
		while($row = $stmt->fetch())
		{
			$content .= "https://www.happypostcard.fun/card/{$row[0]}\n";
		}
		
		$stmt = $db->prepare('
			SELECT
				`user`.`login`
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
			GROUP BY `user`.`id`
			HAVING
				COUNT(DISTINCT `sent_postcard`.`id`) > 0 OR
				COUNT(DISTINCT `sent_postcard`.`received_at`) > 0 OR
				COUNT(DISTINCT `received_postcard`.`id`) > 0 OR
				COUNT(DISTINCT `received_postcard`.`received_at`) > 0 OR
				JULIANDAY(\'now\') - JULIANDAY(`user`.`loggedin_at`) < 30
			ORDER BY `user`.`id`
		');
		$res = $stmt->execute();
		
		while($row = $stmt->fetch())
		{
			$content .= "https://www.happypostcard.fun/user/{$row[0]}\n";
		}
		
		return $content;
	}
	public function isDisplayed() : bool
	{
		return false;
	}
}