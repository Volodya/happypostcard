<?php

class PerformerApproveAddress extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['id', 'login']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'id' => [
				'filter' => FILTER_VALIDATE_INT, // will check in database
			],
			'login' => [
				'custom_filter' => 'FILTER_SANITIZE_LOGIN',
			],
		]);
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			DELETE FROM `user_waiting_approval`
			WHERE `id`=:id AND `user_id`=(
				SELECT `id` FROM `user` WHERE `login`=:login
			)
		');
		$stmt->bindValue(':id', $post['id']);
		$stmt->bindValue(':login', $post['login']);
		$stmt->execute();
		
		$deleted = $stmt->rowCount();
		$page = (new PageRedirector())->withRedirectTo('/user/'.urlencode($post['login']));
		$response = $response->withPage($page);
		if($deleted == 0)
		{
			$response = $response->withErrorMessage('Nothing to approve with that login and ID');
		}
		else
		{
			$response = $response->withNoticeMessage('Address approved');
		}
		
		return $response;
	}
}