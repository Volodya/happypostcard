<?php

class ComWid_user_statistics implements ComWid
{
	private User $user;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->user = $this->user ?? $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{
		if(isset($parameter['user'])) $this->user = $parameter['user'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		if(!($this->user instanceof UserExisting))
		{
			?>
				This user is not registered!
			<?php
			return;
		}
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				COUNT(DISTINCT `out`.`id`) `sent`,
				COUNT(DISTINCT `out`.`received_at`) `sent_arrived`,
				COUNT(DISTINCT `in`.`id`) `incoming`,
				COUNT(DISTINCT `in`.`received_at`) `incoming_arrived`
				FROM `user`
					LEFT JOIN `postcard` `out` ON `user`.`id`=`out`.`sender_id`
					LEFT JOIN `postcard` `in` ON `user`.`id`=`in`.`receiver_id`
				WHERE `user`.`login`=:login
		');
		$stmt->bindValue(':login', $this->user->getLogin());
		$stmt->execute();
		if($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$travelling = intval($row['sent'])-intval($row['sent_arrived']);
			$waiting = intval($row['incoming'])-intval($row['incoming_arrived']);
			?>
				<div><a href='/sent/<?= $this->user->getLogin() ?>'>Sent</a>: <?= $row['sent_arrived'] ?></div>
				<div>Travelling: <?= $travelling ?></div>
				<div><a href='/received/<?= $this->user->getLogin() ?>'>Received</a>: <?= $row['incoming_arrived'] ?></div>
				<div>Waiting: <?= $waiting ?></div>
			<?php
		}
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}