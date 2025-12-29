<?php

class ComWid_address_waitingapproval implements ComWid
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
	public function invoke() : void
	{
		$user = $this->user;
		$viewer = $this->editor;
		
		if(!$viewer->isAdmin())
		{
			echo 'You are not admin!';
			return;
		}
		if(!$user instanceof UserExisting)
		{
			echo 'This user does not exist';
			return;
		}
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `user_waiting_approval`.*, `address`.`addr`,  `address`.`language_code`, `user`.`login`
			FROM `user_waiting_approval`
			INNER JOIN `address` ON `user_waiting_approval`.`user_id`=`address`.`user_id`
			INNER JOIN `user` ON `user_waiting_approval`.`user_id`=`user`.`id`
			WHERE `user_waiting_approval`.`user_id`=:user_id
		');
		$stmt->bindValue(':user_id', $user->getId());
		$stmt->execute();
		$result = false;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><form method='post' action='/performapproveaddress'><?php
				?><input type='hidden' name='id' value='<?= $row['id'] ?>' /><?php
				?><input type='hidden' name='login' value='<?= $row['login'] ?>' /><?php
				?><div class='addresses'><?php
					HtmlSnippets::printAddress($row['addr'], $row['language_code']);
				?></div><?php
				?><button type='submit'>approve</button><?php
			?></form><?php
			$result = true;
		}
		$this->displayed = $result;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}