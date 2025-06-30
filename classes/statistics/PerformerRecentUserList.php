<?php

class PerformerRecentUserList extends Performer_Abstract
{
	private array $result;
	public function __construct()
	{
		$this->result = [];
	}
	public function addAdditionalParameters(array $params) : void
	{}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT
				`login`,
				`polite_name`
			FROM `user`
			WHERE
				`loggedin_at` > date("now", "-7 days")
			ORDER BY `loggedin_at` DESC
		');
		$stmt->execute();
		
		$this->result['recent_users'] = $stmt->fetchAll();
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}