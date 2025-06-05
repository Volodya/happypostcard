<?php

class PerformerLinkImageToCard extends Performer_Abstract
{
	private $config;
	private array $result;
	
	private function linkPostcardImage(Response $response, User $uploader, string $cardCode, string $hash) : Response
	{
		try
		{
			$card = Card::constructByCode($cardCode);
			
			$cardId = $card->getID();
			$uploaderId = $uploader->getId();
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'Something went horribly wrong, no such postcard!');
		}
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `hash`, `extension`, `mime`
			FROM `postcard_image`
			WHERE `hash`=:hash
			UNION
			SELECT `hash`, `extension`, `mime`
			FROM `user_image`
			WHERE `hash`=:hash
		');
		$stmt->bindValue(':hash', $hash);
		$stmt->execute();
		$info = $stmt->fetch(PDO::FETCH_ASSOC);
		if($info === false)
		{
			return $this->abandon($response, 'Hash does not exist');
		}
		
		$stmt = $db->prepare('
			INSERT INTO `postcard_image`(`postcard_id`, `uploader_profile_id`, `hash`, `extension`, `mime`)
			VALUES(:postcard_id, :uploader_profile_id, :hash, :extension, :mime)
		');
		$stmt->bindValue(':postcard_id', $cardId);
		$stmt->bindValue(':uploader_profile_id', $uploaderId);
		$stmt->bindValue(':hash', $info['hash']);
		$stmt->bindValue(':extension', $info['extension']);
		$stmt->bindValue(':mime', $info['mime']);
		$res = $stmt->execute();
		if(!$res)
		{
			return $this->abandon($response, 'Error adding image to the DB');
		}
		
		$this->result = ['image_link_status'=>'ok'];
		return $response;
	}
	private function receiveUserImage(Response $response, User $uploader, string $userLogin, array $files) : Response
	{
		// todo: do!
		return $response;
	}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$this->config = $config;
		
		if(!$request->allSetPOST(['type', 'what']))
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
			'hash' => [
				'custom_filter' => 'FILTER_SANITIZE_ALPHANUMERIC',
			],
		]);
		
		$user = $request->getLoggedInUser();
		
		switch($post['type'])
		{
			case 'card':
				return $this->linkPostcardImage($response, $user, $post['what'], $post['hash']);
			case 'photo':
				return $this->linkUserImage($response, $user, $post['what'], $post['hash']);
			default:
				return $response->withErrorMessage('Unknown type');

		}
	}
	public function getResult() : array
	{
		return $this->result;
	}
}