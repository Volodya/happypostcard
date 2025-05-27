<?php

class PerformerUnlinkImage extends Performer_Abstract
{
	private function performUnlinkCardImage(Request $request, Response $response, Config $config) : Response
	{
		$user = $request->getLoggedInUser();
		
		$post = $request->getPOST([
			'code'	=>['filter'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			'hash'	=>['filter'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			]);
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			DELETE FROM `postcard_image`
			WHERE `hash`=:hash AND `postcard_id`=
				(SELECT `id` FROM `postcard` WHERE `code`=:code) AND
				`uploader_profile_id`=:uploader_id
		');
		$stmt->bindValue(':code', $post['code']);
		$stmt->bindValue(':hash', $post['hash']);
		$stmt->bindValue(':uploader_id', $user->getId());
		$stmt->execute();
		
		$page = (new PageRedirector())->withRedirectTo('/card/'.urlencode($post['code']));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Image deleted');
		
		return $response;
	}
	private function performUnlinkUserImage(Request $request, Response $response, Config $config) : Response
	{
		$user = $request->getLoggedInUser();
		
		$post = $request->getPOST([
			'user'	=>['custom_filter' => 'FILTER_SANITIZE_LOGIN',],
			'hash'	=>['filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			]);
		
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			DELETE FROM `user_image`
			WHERE `hash`=:hash AND `user_id`=
				(SELECT `id` FROM `user` WHERE `login`=:login) AND
				`user_id`=:uploader_id
		');
		$stmt->bindValue(':login', $post['user']);
		$stmt->bindValue(':hash', $post['hash']);
		$stmt->bindValue(':uploader_id', $user->getId());
		$res = $stmt->execute();
		
		if($stmt->rowCount() == 0)
		{
			$response = $response->withErrorMessage('No such image exists');
		}
		
		$page = (new PageRedirector())->withRedirectTo('/user/'.urlencode($post['user']));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Image deleted');
		
		return $response;
	}
	
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if($request->allSetPOST(['code', 'hash']))
		{
			return $this->performUnlinkCardImage($request, $response, $config);
		}
		if($request->allSetPOST(['user', 'hash']))
		{
			return $this->performUnlinkUserImage($request, $response, $config);
		}
		return $this->abandon($response, 'all fields must exist');
	}
}