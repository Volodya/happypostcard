<?php

class ComWid_display_table implements ComWid
{
	private bool $displayed;
	private bool $showSql;
	private string $sql;
	private array $table;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->showSql = false;
		$this->sql = '';
		$this->table = [];
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		if(isset($parameter['show_sql'])) $this->showSql = $parameter['show_sql'];
	}
	public function setPerformerResults($performerResults) : void
	{
		if($this->showSql and isset($performerResults['sql_result']))
		{
			$this->sql=$performerResults['sql_query'];
		}
		if(isset($performerResults['sql_result']))
		{
			$this->table=$performerResults['sql_result'];
		}
	}
	public function invoke() : void
	{
		if($this->showSql)
		{
			?><code><?= $this->sql ?></code><?php
		}
		if(empty($this->table))
		{
			?><p>No results</p><?php
			$this->displayed = true;
			return;
		}
		
		$columns = array_keys($this->table[0]);
		?><table><?php
			?><thead><?php
				foreach($columns as $col)
				{
					?><th scope='col'><?= $col ?></th><?php
				}
			?></thead><?php
			?><tbody><?php
			foreach($this->table as $row)
			{
				?><tr><?php
					foreach($row as $cell)
					{
						?><td><?= $cell ?></td><?php
					}
				?></tr><?php
			}
			?></tbody><?php
		?></table><?php
		
		$this->displayed = true;
	}
	public function haveDisplayed() : bool
	{
		return $this->displayed;
	}
}