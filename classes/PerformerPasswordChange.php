<?php

class PerformerPasswordChange extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['currpassword', 'newpassword', 'newpassword2']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'currpassword' => [
				'filter' => FILTER_UNSAFE_RAW, // needs to be as is
			],
			'newpassword' => [
				'filter' => FILTER_UNSAFE_RAW, // needs to be as is
			],
			'newpassword2' => [
				'filter' => FILTER_UNSAFE_RAW, // needs to be as is
			],
		]);
		
		if($post['newpassword'] != $post['newpassword2'])
		{
			return $this->abandon($response, 'passwords do not match');
		}
		
		$user = $request->getLoggedInUser();
		if(!($user instanceof UserExisting))
		{
			return $this->abandon($response, 'must be logged in');
		}
		if(!$user->confirmPassword($post['currpassword']))
		{
			return $this->abandon($response, 'current password is incorrect');
		}
		
		$user->updatePassword($config, $post['newpassword']);
		
		$page = (new PageRedirector())->withRedirectTo('/account');
		return $response;
	}
}