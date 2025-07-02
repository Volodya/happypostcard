<?php

class ComWid_user_addresses implements ComWid
{
	private User $user;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->user = $parameter['user'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		foreach($this->user->getUserAddresses() as $row)
		{
			?><div lang='<?= $row['language_code'] ?>' class='address'><?= $row['addr'] ?></div><?php
		}
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}