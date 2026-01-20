<?php

class ComWid_sendpostcard implements ComWid
{
	private bool $displayed;
	private User $editor;
	private bool $sendMany;
	private int $howMany;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->sendMany = false;
		$this->howMany = 1;
	}
	public function setEditor(User $editor) : void
	{
		$this->editor = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{
		if(isset($parameter['howmany']) and intval($parameter['howmany']) > 1)
		{
			$this->sendMany = true;
			$this->howMany = intval($parameter['howmany']);
		}
	}
	public function setPerformerResults($performerResults) : void
	{
	}
	public function invoke() : void
	{
		?><form method='POST' action='/performselectaddress'><?php
			?><div><label>Your location:<?php
				?><select name='location'><?php
					$homeLocation = $this->editor->getActiveLocation();
					HtmlSnippets::printLocationSelectOptionList($homeLocation['code']);
				?></select><?php
			?></label></div><?php
			if($this->sendMany)
			{
				?><div><?php
					?><label>How many postcards are you willing to send:<?php
						?><input type='number' min='1' max='<?= $this->howMany ?>' name='howmany' value='1' /><?php
					?></label><?php
				?></div><?php
			}
			?><div><?php
				?><label><?php
					?>Confirm that you are willing to send a postcard to a random person. <?php
					?><input type='checkbox' name='confirm' /><?php
				?></label><?php
			?></div><?php
			?><div><?php HtmlSnippets::printOneTimeButton(['type'=> 'submit'], 'Get an address') ?></div><?php
		?></form><?php
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}