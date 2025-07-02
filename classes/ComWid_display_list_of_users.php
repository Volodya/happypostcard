<?php

class ComWid_display_list_of_users implements ComWid
{
	private string $performerResultsKey;
	private array $users;
	private string $listType;
	private string $listClass;
	private string $itemType;
	private string $itemDelim;
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->users = [];
		$this->listType='ol';
		$this->listClass='';
		$this->itemType='li';
		$this->itemDelim='';
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		if(isset($parameter['performer_results_key'])) $this->performerResultsKey = $parameter['performer_results_key'];
		if(isset($parameter['list_type'])) $this->listType = $parameter['list_type'];
		if(isset($parameter['list_class'])) $this->listClass = $parameter['list_class'];
		
		if(in_array($this->listType, ['ul', 'ol']))
		{
			$this->itemType = 'li';
		}
		if(in_array($this->listType, ['p', 'span']))
		{
			$this->itemType = 'span';
			$this->itemDelim = ', ';
		}
		else
		{
			$this->itemType = 'div';
		}
	}
	public function setPerformerResults($performerResults) : void
	{
		if(isset($performerResults[$this->performerResultsKey])) $this->users = $performerResults[$this->performerResultsKey];
	}
	public function invoke() : void
	{
		?><<?= $this->listType ?> class='<?= $this->listClass ?>'><?php
		$first = true;
		foreach($this->users as $user)
		{
			if(!$first) echo $this->itemDelim;
			?><<?= $this->itemType ?>><?php
				HtmlSnippets::printUserPoliteName($user['login'], $user['polite_name'], true);
			?></<?= $this->itemType ?>><?php
			$first=false;
		}
		?></<?= $this->listType ?>><?php
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}