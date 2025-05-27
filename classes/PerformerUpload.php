<?php

class PerformerUpload extends Performer_Abstract
{
	private $config;
	
	private function receiveImage(Response $response, array $files, $addToDatabase) : Response
	{
		if($files['image'][0]['size']===0)
		{
			return $this->abandon($response, 'Nothing uploaded');
		}
		
		// UPLOAD FILE
		$tmpName=$files['image'][0]['tmp_name'];
		$hash = hash_file('sha256', $tmpName, false);
		$mime_subtype = explode('/', $files['image'][0]['type'])[1];
		$fileName = "{$hash}.{$mime_subtype}";
		$uploadDir = $this->config->getPropertyOrThrow('rootdir', new Exception()).'/uploads';
		$filePath = "{$uploadDir}/{$fileName}";
		move_uploaded_file($tmpName, $filePath);
		
		// MAKE THUMBS
		$image = new Imagick($filePath);
		Picture::makeThumbs($image, [[200, 200, "{$uploadDir}/200thumbs/{$hash}.webp"]]);
		Picture::makeThumbs($image, [[800, 800, "{$uploadDir}/800thumbs/{$hash}.webp"]]);
		
		return $addToDatabase($hash, 'webp', $mime_subtype, $response);
	}
	private function receivePostcardImage(Response $response, User $uploader, string $cardCode, array $files) : Response
	{
		try
		{
			$card = Card::constructByCode($cardCode);
			
			$response = $response->withPage(
				$response->getPage()->withRedirectTo("/card/{$cardCode}")
			);
			
			$cardId = $card->getID();
			$uploaderId = $uploader->getId();
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'Something went horribly wrong, no such postcard!');
		}
		
		$addToDatabase = function($hash, $extension, $mime_subtype, $response) use ($cardId, $uploaderId)
		{
			// ADD TO DB
			$db = Database::getInstance();
			
			$db = Database::getInstance();
			$stmt = $db->prepare('
				INSERT INTO `postcard_image`(`postcard_id`, `uploader_profile_id`, `hash`, `extension`, `mime`)
				VALUES(:postcard_id, :uploader_profile_id, :hash, :extension, :mime)
			');
			$stmt->bindValue(':postcard_id', $cardId);
			$stmt->bindValue(':uploader_profile_id', $uploaderId);
			$stmt->bindValue(':hash', $hash);
			$stmt->bindValue(':extension', $extension);
			$stmt->bindValue(':mime', $mime_subtype);
			$res = $stmt->execute();
			if(!$res)
			{
				return $this->abandon($response, 'Error adding image to the DB');
			}
			return $response;
		};
		return $this->receiveImage($response, $files, $addToDatabase);
	}
	private function receiveUserImage(Response $response, User $uploader, string $userLogin, array $files) : Response
	{
		try
		{
			$user = User::constructByLogin($userLogin);
			$userLogin = $user->getLogin();
			
			$response = $response->withPage(
				$response->getPage()->withRedirectTo("/user/{$userLogin}")
			);
			
			$userId = $user->getId();
		}
		catch(Exception $ex)
		{
			return $this->abandon($response, 'Something went horribly wrong, no such user!');
		}
		
		$addToDatabase = function($hash, $extension, $mime_subtype, $response) use ($userId)
		{
			// ADD TO DB
			$db = Database::getInstance();
			
			$db = Database::getInstance();
			$stmt = $db->prepare('
				INSERT INTO `user_image`(`user_id`, `num`, `hash`, `extension`, `mime`)
				VALUES(
					:user_id, 
					(SELECT COUNT(*)+1 FROM `user_image` WHERE `user_id`=:user_id),
					:hash,
					:extension,
					:mime)
			');
			$stmt->bindValue(':user_id', $userId);
			$stmt->bindValue(':hash', $hash);
			$stmt->bindValue(':extension', $extension);
			$stmt->bindValue(':mime', $mime_subtype);
			$res = $stmt->execute();
			if(!$res)
			{
				return $this->abandon($response, 'Error adding image to the DB');
			}
			return $response;
		};
		return $this->receiveImage($response, $files, $addToDatabase);
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
			]
		]);
		
		$files = $request->getFILES();
		
		$user = $request->getLoggedInUser();
		
		switch($post['type'])
		{
			case 'card':
				return $this->receivePostcardImage($response, $user, $post['what'], $files);
			case 'photo':
				return $this->receiveUserImage($response, $user, $post['what'], $files);
			default:
				return $response->withErrorMessage('Unknown type');
		}
	}
}