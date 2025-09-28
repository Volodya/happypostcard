<?php

class ComWid_form implements ComWid
{
	private bool $displayed;
	private array $inputs;
	private string $action;
	private string $method;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->inputs = [];
		$this->action = '/perform';
		$this->method = 'POST';
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		if(isset($parameter['inputs']))
		{
			$this->inputs = $parameter['inputs'];
			
			if(isset($parameter['action']))
			{
				$this->action = $parameter['action'];
			}
			if(isset($parameter['method']))
			{
				$this->action = $parameter['method'];
			}
		}
		else
		{
			echo '<div>Form inputs are not set</div>';
		}
	}
	public function setPerformerResults($performerResults) : void
	{
	}
	public function invoke() : void
	{
		?><form action='<?= $this->action ?>' method='<?= $this->method ?>'><?php
			foreach($this->inputs as $input)
			{
				$divved = !isset($input['divved']) or $input['divved'];
				$labeled = isset($input['label']);
				
				if($divved)
				{
					?><div><?php
				}
				if($labeled)
				{
					?><label><?= $input['label'] ?><?php
				}
				switch($input['tag'])
				{
					case 'hidden':
					case 'text':
					case 'password':
					case 'checkbox':
					case 'radio':
					case 'email':
					case 'number':
						?><input type='<?= $input['tag'] ?>'<?php
							foreach($input as $key => $val)
							{
								if($key == 'tag') continue;
								?> <?= $key ?>='<?= $val ?>'<?php
							}
						?> /><?php
						break;
					case 'date':
						break;
					case 'file':
						break;
					case 'submit':
					case 'reset':
					case 'button':
						if(!isset($input['inner_text']))
						{
							$input['inner_text'] = $input['value'];
						}
						?><button type='<?= $input['tag'] ?>'<?php
							foreach($input as $key => $val)
							{
								if($key == 'tag') continue;
								?> <?= $key ?>='<?= $val ?>'<?php
							}
						?>><?= $input['inner_text'] ?></button><?php
						break;
					case 'textarea':
						if(!isset($input['value']))
						{
							$input['value'] = '';
						}
						?><textarea<?php
							foreach($input as $key => $val)
							{
								if($key == 'tag') continue;
								if($key == 'value') continue;
								?> <?= $key ?>='<?= $val ?>'<?php
							}
						?>><?= $input['value'] ?></textarea><?php
						break;
					default:
						echo "<div>Unknown form input tag: {$input['tag']}</div>";
						break;
				}
				if($labeled)
				{
					?></label><?php
				}
				if($divved)
				{
					?></div><?php
				}
			}
		?></form><?php
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}