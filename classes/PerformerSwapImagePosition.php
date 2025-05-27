<?php

class PerformerSwapImagePosition extends Performer_Abstract
{
	private $config;
	
	private function swapUserImage(Response $response, UserExisting $user, string $owner, string $a, string $b) : Response
	{
		try
		{
			$ownerUser = UserExisting::constructByLogin($owner);
		}
		catch(Exception $e)
		{
			$db->rollBack();
			return $this->abandon($response, 'user does not exist');
		}
		try
		{
			$imageA = PicturePhoto::constructByHash($a);
			$imageB = PicturePhoto::constructByHash($b);
		}
		catch(Exception $e)
		{
			return $this->abandon($response, 'picture does not exist');
		}
		if(!Performer_Permissions::canChangePositionOfUserImage($user, $ownerUser, $imageA, $imageB))
		{
			return $this->abandon($response, 'Not allowed!');
		}
		
		$db = Database::getInstance();
		$db->beginTransaction();
		
		$stmt = $db->prepare('
			UPDATE `user_image`
			SET num=:num
			WHERE user_id=:user_id AND hash=:hash
		');
		$stmt->bindValue(':user_id', $ownerUser->getId());
		$stmt->bindValue(':hash', $imageA->getHash());
		$stmt->bindValue(':num', $imageB->getNum());
		$stmt->execute();
		$stmt->bindValue(':user_id', $ownerUser->getId());
		$stmt->bindValue(':hash', $imageB->getHash());
		$stmt->bindValue(':num', $imageA->getNum());
		$stmt->execute();
		
		$db->commit();
		
		return $response;
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$this->config = $config;
		
		if(!$request->allSetPOST(['type', 'what', 'a', 'b']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$post = $request->getPOST([
			'type' => [
				'filter' => FILTER_UNSAFE_RAW, // will be run throught switch-case
			],
			'what' => [
				'custom_filter' => 'FILTER_SANITIZE_LOGIN',
			],
			'a' => [
				'filter' => FILTER_UNSAFE_RAW, // will check the database of card numbers/user logins
			],
			'b' => [
				'filter' => FILTER_UNSAFE_RAW, // will check the database of card numbers/user logins
			]
		]);
		
		$user = $request->getLoggedInUser();
		$page = (new PageRedirector())->withRedirectTo('/userphotos/'.$user->getLogin());
		$response = $response->withPage($page);
		
		switch($post['type'])
		{
			case 'card':
				return $this->swapPostcardImage($response, $user, $post['what'], $post['a'], $post['b']);
			case 'photo':
				return $this->swapUserImage($response, $user, $post['what'], $post['a'], $post['b']);
			default:
				return $this->abandon($response, 'what type?');
		}
	}
}