<?php

class ComWid_site_statistics extends SimpWid
{
	private User $user;
	
	public function invoke() : void
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('SELECT COUNT(`id`) `cnt`, COUNT(`received_at`) AS `cnt_received` FROM `postcard`');
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><div>Total postcards sent: <?= $row['cnt'] ?></div><?php
			?><div>Total postcards sent and arrived at destination: <?= $row['cnt_received'] ?></div><?php
		}
		
		$stmt = $db->prepare('
			SELECT COUNT(`id`) `cnt`, COUNT(`received_at`) AS `cnt_received`
			FROM `postcard` WHERE `year` = strftime(\'%Y\')
		');
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><div>This year sent: <?= $row['cnt'] ?></div><?php
			?><div>This year postcards sent and arrived at destination: <?= $row['cnt_received'] ?></div><?php
		}
		$stmt = $db->prepare('
			SELECT COUNT(`id`) `cnt`
			FROM `postcard`
			WHERE `year` < strftime(\'%Y\') AND `received_at` > DATE(\'now\', \'start of year\')
		');
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><div>Arrived this year from previous years: <?= $row['cnt'] ?></div><?php
		}
		
		$stmt = $db->prepare('
			SELECT COUNT(`postcard`.`id`) `cnt`, `location`.`code` `loc_code`, `location`.`name` `loc_name`
			FROM `postcard`
				INNER JOIN `location_code` `location` ON `location`.`id` = `postcard`.`send_location_id`
			GROUP BY `location`.`id`
			ORDER BY COUNT(`postcard`.`id`) DESC
			LIMIT 3
		');
		$stmt->execute();
		?><ol>The top sending locations:<?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><li><a href='/location/<?= $row['loc_code'] ?>'><?= $row['loc_name'] ?></a> — <?= $row['cnt'] ?></li><?php
		}
		?></ol><?php
		
		$stmt = $db->prepare('
			SELECT COUNT(`postcard`.`id`) `cnt`, `location`.`code` `loc_code`, `location`.`name` `loc_name`
			FROM `postcard`
				INNER JOIN `location_code` `location` ON `location`.`id` = `postcard`.`send_location_id`
			WHERE  `year` = strftime(\'%Y\')
			GROUP BY `location`.`id`
			ORDER BY COUNT(`postcard`.`id`) DESC
			LIMIT 3
		');
		$stmt->execute();
		?><ol>The top sending locations this year:<?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><li><a href='/location/<?= $row['loc_code'] ?>'><?= $row['loc_name'] ?></a> — <?= $row['cnt'] ?></li><?php
		}
		?></ol><?php
		
		
		$stmt = $db->prepare('
			WITH RECURSIVE dates(dt) AS (
				VALUES(DATE(\'now\', \'-1 month\'))
				UNION ALL SELECT DATE(`dt`, \'+1 day\') FROM dates WHERE dt<DATE(\'now\')
			)
			SELECT COUNT(`postcard`.`id`) `cnt`, `dates`.`dt` `sent_at`
			FROM `postcard`
			RIGHT JOIN `dates` ON `dates`.`dt` = DATE(`postcard`.`sent_at`)
			GROUP BY `dates`.`dt`
			ORDER BY `dates`.`dt` ASC
		');
		$stmt->execute();
		$svgGraph = new SvgGraph();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$svgGraph->addPoint(floatval($row['cnt']));
		}
		$svgGraph->print();
	}
}