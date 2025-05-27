<?php

class PerformerProfileEnable extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['enabled']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$user = $request->getLoggedInUser();
		
		$post = $request->getPOST([
			'enabled' =>['filter'=>FILTER_UNSAFE_RAW],
			]);
		
		if($post['enabled'] == 'off')
		{
			$user->disable();
		}
		else
		{
			$user->enable();
		}
		
		$page = (new PageRedirector())->withRedirectTo('/user/'.urlencode($user->getLogin()));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Your pofile has been updated.');
		
		return $response;
	}
}