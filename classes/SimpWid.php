<?php

abstract class SimpWid implements ComWid
{
	public function setTemplateParameter(array $parameter) : void {}
	public function setPerformerResults(array $performerResults) : void {}
	
	public function setEditor(User $editor) : void{}
	
	public abstract function invoke() : void;
	public function haveDisplayed() : bool
	{
		return true;
	}
}