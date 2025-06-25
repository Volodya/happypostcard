<?php

class PerformerProfileEdit extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['login', 'home_location']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		
		$post = $request->getPOST([
			'login'			=>['custom_filter'=>'FILTER_SANITIZE_LOGIN'],
			'polite_name'	=>['filter'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			'birthday'		=>['custom_filter'=>'FILTER_SANITIZE_DATE'],
			'home_location'	=>['filter'=>FILTER_SANITIZE_FULL_SPECIAL_CHARS],
			'about'			=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT'],
			'desires'		=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT'],
			'hobbies'		=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT'],
			'languages'		=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT'],
			'phobias'		=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT'],
			'email'			=>['filter'=>FILTER_VALIDATE_EMAIL],
			'addr_id'		=>['filter'=>FILTER_VALIDATE_INT, 'isArray'=>true],
			'addr_addr'		=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT', 'isArray'=>true],
			'addr_lang_code'=>['custom_filter'=>'FILTER_SANITIZE_NOSCRIPT', 'isArray'=>true],
		]);
		
		$editor = $request->getLoggedInUser();
		$user = User::constructByLogin($post['login']);
		
		if($editor->getId() != $user->getId() and !$editor->isAdmin())
		{
			return $this->abandon($response, 'Cannot edit another person\'s account.');
		}
		
		$db = Database::getInstance();
		
		$homeLocation = Location::getIdByCode($post['home_location']);
		$stmt = $db->prepare(
			'UPDATE `user`
			SET
				`polite_name` = :polite_name,
				`birthday` = :birthday,
				`about` = :about,
				`desires` = :desires,
				`hobbies` = :hobbies,
				`phobias` = :phobias,
				`languages` = :languages,
				`home_location_id` = :home_location_id
			WHERE `id`=:user_id'
			);
		$stmt->bindValue(':user_id', $user->getId());
		$stmt->bindValue(':birthday', $post['birthday']);
		$stmt->bindValue(':polite_name', $post['polite_name']);
		$stmt->bindValue(':about', $post['about']);
		$stmt->bindValue(':desires', $post['desires']);
		$stmt->bindValue(':hobbies', $post['hobbies']);
		$stmt->bindValue(':phobias', $post['phobias']);
		$stmt->bindValue(':languages', $post['languages']);
		$stmt->bindValue(':home_location_id', $homeLocation);
		$stmt->execute();
		$user->setPreference('home_location', $post['home_location']); // TODO: REMOVE
		
		$addr = [];
		for($i = 0; $i < min( count($post['addr_id']), count($post['addr_addr']), count($post['addr_lang_code'])); $i++)
		{
			$addr[] = [
				'id' => $post['addr_id'][$i],
				'addr' => $post['addr_addr'][$i],
				'lang_code' => $post['addr_lang_code'][$i]
				];
		}
		
		foreach($addr as $ad)
		{
			if($ad['id']==0 and !empty($ad['addr']))
			{
				$user->addAddress($ad['addr'], $ad['lang_code'], $editor->isAdmin());
			}
			else if(!empty($ad['addr']))
			{
				$user->changeAddress(intval($ad['id']), $ad['addr'], $ad['lang_code'], $editor->isAdmin());
			}
			else
			{
				$user->removeAddress(intval($ad['id']), $editor->isAdmin());
			}
		}
		
		$page = (new PageRedirector())->withRedirectTo('/user/'.urlencode($user->getLogin()));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Your pofile has been updated.');
		
		return $response;
	}
}