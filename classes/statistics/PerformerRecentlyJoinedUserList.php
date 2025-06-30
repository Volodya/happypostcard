<?php

class PerformerRecentlyJoinedUserList extends Performer_Abstract
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
				`registered_at` > date("now", "-1 month")
			ORDER BY `registered_at` DESC
		');
		$stmt->execute();
		
		$this->result['recently_joined_users'] = $stmt->fetchAll();
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}