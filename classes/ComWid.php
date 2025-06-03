<?php

interface ComWid
{
	public function setTemplateParameter(array $parameter) : void;
	public function setPerformerResults(array $performerResults) : void;
	
	public function setEditor(User $editor) : void;
	
	public function invoke() : void;
	public function haveDisplayed() : bool;
}