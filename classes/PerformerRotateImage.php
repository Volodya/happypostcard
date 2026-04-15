<?php

class PerformerRotateImage extends Performer_Abstract
{
	private function performRotateCardImage(Request $request, Response $response, string $hash, int $rotate) : Response
	{
		$user = $request->getLoggedInUser();
		
		$db = Database::getInstance();
		
		if($user->isAdmin())
		{
			$stmt = $db->prepare('
				UPDATE `postcard_image`
				SET `rotate`=:rotate
				WHERE `hash`=:hash
			');
			$stmt->bindValue(':hash', $hash);
			$stmt->bindValue(':rotate', $rotate);
		}
		else
		{
			$stmt = $db->prepare('
				UPDATE `postcard_image`
				SET `rotate`=:rotate
				WHERE `hash`=:hash AND `uploader_profile_id`=:uploader_id
			');
			$stmt->bindValue(':hash', $hash);
			$stmt->bindValue(':uploader_id', $user->getId());
			$stmt->bindValue(':rotate', $rotate);
		}
		$stmt->execute();
		
		$page = (new PageRedirector())->withRedirectTo('/image/'.urlencode($hash));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Image rotated');
		
		return $response;
	}
	private function performRotateUserImage(Request $request, Response $response, Config $config) : Response
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
		if(!$request->allSetPOST(['type', 'hash', 'rotate']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'type'  	=>['filter' => FILTER_UNSAFE_RAW,], // will white-list check
			'hash'  	=>['filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			'rotate'	=>['custom_filter' => 'FILTER_SANITIZE_NUMERIC'],
		]);
		$rotate = intdiv(intval($post['rotate']), 90) * 90;
		
		if($post['type'] == 'card')
		{
			return $this->performRotateCardImage($request, $response, $post['hash'], $rotate);
		}
		else if($post['type'] == 'user')
		{
			return $this->performRotateUserImage($request, $response, $post['hash'], $rotate);
		}
		else if($post['type'] == 'unknown')
		{
			// temporarily card image only
			return $this->performRotateCardImage($request, $response, $post['hash'], $rotate);
		}
	}
}