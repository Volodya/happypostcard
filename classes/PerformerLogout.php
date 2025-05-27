<?php

class PerformerLogout extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$session = $request->getSESSION();
		if(isset($session['login_id']))
		{
			$db = Database::getInstance();
			
			$stmt = $db->prepare('DELETE FROM `user_persistent_login` WHERE `user_id`=:user_id');
			$stmt->bindValue(':user_id', $session['login_id']);
			
			$stmt->execute();
			
			$response->unsetSession('login_id');
		}
		
		$page = (new PageRedirector())->withRedirectTo('/home');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('You have been logged out');
		
		return $response;
	}
	
}