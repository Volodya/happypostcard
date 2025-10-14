<?php

class ComWid_user_info implements ComWid
{
	private User $user;
	private User $editor;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->editor = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->user = $parameter['user'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	private static function smartCode(string $text) : string
	{
		// https://stackoverflow.com/questions/19372458/convert-multiple-new-lines-to-paragraphs
		$result = preg_replace('~(*BSR_ANYCRLF)\R\R\K(?>[^<\r\n]++|<(?!h[1-6]\b)|\R(?!\R))+(?=\R\R|$)~u',
			'<p>$0</p>', $text);
		$result = preg_replace("/\R\R+/", '', $result);
		$result = nl2br($result);
		return $result;
	}
	public function invoke() : void
	{
		$user = $this->user;
		if(!($user instanceof UserExisting))
		{
			?>
				This user is not registered!
			<?php
			return;
		}
		$viewOfSelf = ($this->editor instanceof UserExisting and $user->getId() == $this->editor->getId());
		if($viewOfSelf)
		{
			?>
			<div>You can <a href='/useredit/<?= $user->getLogin() ?>'>edit</a> this information.</div>
			<?php
		}
		$userInfo = $user->getUserInfo();
		$userConfirmedSender = $user->isConfirmedSender();
		$userConfirmedReceiver = $user->isConfirmedReceiver();
		
		if(!empty($userInfo))
		{
			$userAbout = self::smartCode( $userInfo['about'] );
			$userDesires = self::smartCode( $userInfo['desires'] );
			
			?><div>Name: <?= $userInfo['polite_name'] ?></div><?php
			?><div>Days on this site: <?= $userInfo['days_registered'] ?></div><?php
			?><div>Birthday: <?= $userInfo['birthday'] ?></div><?php
			if(!$userConfirmedSender and !$userConfirmedReceiver)
			{
				?><div>This user has not confirmed to be able to send nor to receive postcards.</div><?php
			}
			else if(!$userConfirmedSender)
			{
				?><div>This user has not confirmed to be able to send postcards.</div><?php
			}
			else if(!$userConfirmedReceiver)
			{
				?><div>This user has not confirmed to be able to receive postcards.</div><?php
			}
			
			if(empty($userInfo['home_location_code']))
			{
				?><div>Home location has not been set</div><?php
			}
			else
			{
				?><div>Home location:<?php
					?><a href='/location/<?= $userInfo['home_location_code'] ?>'><?= $userInfo['home_location'] ?></a><?php
				?></div><?php
			}
			
			?><div>What is shared about oneself: <blockquote><?= $userAbout ?></blockquote></div><?php
			?><div>Postcard desires: <blockquote><?= $userDesires ?></blockquote></div><?php
			?><div>Hobbies: <?= $userInfo['hobbies'] ?></div><?php
			?><div>Languages: <?= $userInfo['languages'] ?></div><?php
			
			if(!empty($userInfo['phobias']))
			{
				?><div>Phobias: <?= $userInfo['phobias'] ?></div><?php
			}
			else
			{
				?><div>Phobias: None</div><?php
			}
		}
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}