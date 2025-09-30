<?php

class PerformerDirectSql extends Performer_Abstract
{
	private array $result;
	public function __construct()
	{
		$this->result =
		[
			'sql_query' => '',
			'sql_result' => [],
		];
	}
	
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		// SUPERADMIN SANITY CHECK
		if(!$request->isLoggedIn() or !$request->getLoggedInUser()->isAdmin())
		{
			return $this->abandon($response, 'Must be admin!');
		}
		
		if(!$request->allSetPOST(['sql']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'sql' => [
				'filter' => FILTER_UNSAFE_RAW, // WARNING, NEED TO MAKE SURE THE PERSON IS SUPERADMIN
			],
		]);
		
		$db = Database::getInstance();
		
		try
		{
			$stmt = $db->prepare($post['sql']);
			$res = $stmt->execute();
			if($res == false)
			{
				throw new PDOException;
			}
		}
		catch(PDOException $ex)
		{
			return $this->abandon($response, 'SQL error '.$ex->getMessage());
		}
		
		$this->result['sql_query'] = $post['sql'];
		$this->result['sql_result'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}