<?php

class ComWid_sitemenu implements ComWid
{
	private User $editor;
	private string $menutype;
	
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
		$this->menutype = $parameter['menutype'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$viewer = $this->editor;
		$menu = new SiteMenu($this->menutype);
		$menu->generateMenu($viewer);
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}