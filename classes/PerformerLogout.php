<?php

class PerformerLogout extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$session = $request->getSESSION();
		if(isset($session['login_id']))
		{
			echo 'here';
			$db = Database::getInstance();
			
			$stmt = $db->prepare('DELETE FROM `user_persistent_login` WHERE `user_id`=:user_id');
			$stmt->bindValue(':user_id', $session['login_id']);
			
			$stmt->execute();
			
			$response->setCookie('test', 'test');
			$response->unsetSession('login_id');
		}
		$response->unsetCookie('persistent');
		
		$page = (new PageRedirector())->withRedirectTo('/home');
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('You have been logged out');
		
		return $response;
	}
	
}