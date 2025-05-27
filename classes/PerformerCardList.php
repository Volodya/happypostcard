<?php

class PerformerCardList extends Performer_Abstract
{
	private array $result;
	private array $needs;
	private string $orderby;
	public __construct()
	{
		$this->result = [];
		$this->needs = [];
	}
	public function addAdditionalParameters(array $params)
	{
		switch($params['type'])
		{
			case 'sender':
				$this->needs['sender'] = true;
		}
		$this->orderby = $params['orderby'];
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$conditionsSql = [];
		$conditionsPdo = [];
		if($needReceived)
		{
			$conditionsSql[] = '`received_at` NOT NULL';
		}
		if(isset($this->needs['sender']))
		{
			try
			{
				$sender = $request->getPageAdditionalUrl()[0];
			}
			catch(Exception $ex)
			{
				$sender = $request->getLoggedInUser()->getLogin();
			}
			
			$conditionsSql[] = '`sender`.`login`=:sender_login';
			$conditionsPdo[':sender_login'] = $sender;
		}
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT
				`postcard`.`code`,
				`sender`.`id` AS `sender_id`,
				`sender`.`login` AS `sender_login`,
				`receiver`.`id` AS `receiver_id`,
				`receiver`.`login` AS `receiver_login`,
				`postcard`.`sent_at`,
				`postcard`.`received_at`,
				`hash`,
				`extension`,
				`mime`
			FROM `postcard`
			INNER JOIN `receiver
			LEFT JOIN `postcard_image`
				ON `postcard`.`id`=`postcard_image`.`postcard_id`
			WHERE
				`received_at` NOT NULL
					AND
				`postcard_image`.`id` IN
					(SELECT MIN(`id`) FROM `postcard_image` GROUP BY `postcard_id`)
			ORDER BY '.$this->orderby.' DESC
			LIMIT :count
		');
		$stmt->bindValue(':count', $count, PDO::PARAM_INT);
		$stmt->execute();
		
		$this->result = $stmt->fetchAll();
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}