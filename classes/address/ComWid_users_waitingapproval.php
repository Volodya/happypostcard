<?php

class ComWid_users_waitingapproval implements ComWid
{
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
	{}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$viewer = $this->editor;
		
		if(!$viewer->isAdmin())
		{
			echo 'You are not admin!';
			return;
		}
		
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT DISTINCT `user`.`login`
			FROM `user_waiting_approval`
			INNER JOIN `user` ON `user_waiting_approval`.`user_id`=`user`.`id`
			INNER JOIN `address` ON `user_waiting_approval`.`user_id`=`address`.`user_id`
		');
		$stmt->execute();
		$result = false;
		?><ul><?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			?><li><?php
				?><a href='/user/<?= $row['login'] ?>'/><?= $row['login'] ?></a><?php
			?></li><?php
			$result = true;
		}
		$this->displayed = $result;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}