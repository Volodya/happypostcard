<?php

class ComWid_user_edit_button extends SimpWid
{
	private User $user;
	
	public function setTemplateParameter(array $parameter) : void
	{
		$this->user = $parameter['user'];
	}
	public function invoke() : void
	{
		?><p>You are allowed to edit this profile.</p>
		<form method='GET' action='/useredit/<?= $this->user->getLogin() ?>'>
			<button type='submit'>Edit</button>
		</form><?php
	}
}