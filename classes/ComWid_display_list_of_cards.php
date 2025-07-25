<?php

class ComWid_display_list_of_cards implements ComWid
{
	private bool $displayed;
	private array $postcards;
	private array $displayedColumns;
	
	public function __construct()
	{
		$this->displayed = false;
		$this->guessedLocation = [];
	}
	public function setEditor(User $editor) : void
	{}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->displayedColumns = [];
		foreach($parameter['columns'] as $col)
		{
			$this->displayedColumns[$col] = true;
		}
	}
	public function setPerformerResults($performerResults) : void
	{
		if(isset($performerResults['postcards'])) $this->postcards=$performerResults['postcards'];
	}
	public function invoke() : void
	{
		$disp = $this->displayedColumns;
		?><table><?php
			?><thead><?php
				if($disp['sender'])
				{
					?><th scope='col'>Sender</th><?php
				}
				if($disp['sender_loc'])
				{
					?><th scope='col'>Sent from</th><?php
				}
				if($disp['sent_at'])
				{
					?><th scope='col'>Date Sent</th><?php
				}
				if($disp['days_travelling'])
				{
					?><th scope='col'>Days travelling</th><?php
				}
				if($disp['postcard_code'])
				{
					?><th scope='col'>Code</th><?php
				}
				if($disp['receiver'])
				{
					?><th scope='col'>Receiver</th><?php
				}
				if($disp['receive_loc'])
				{
					?><th scope='col'>Destination</th><?php
				}
				if($disp['received_at'])
				{
					?><th scope='col'>Date Received</th><?php
				}
				if($disp['first_image_hash'])
				{
					?><th scope='col' title='picture'>&#9215;</th><?php
				}
			?></thead><?php
			?><tbody><?php
			foreach($this->postcards as $row)
			{
				?><tr><?php
					if($disp['sender'])
					{
						?><td><?php HtmlSnippets::printUserPoliteName($row['sender_login'], $row['sender_polite_name'], true); ?></td><?php
					}
					if($disp['sender_loc'])
					{
						?><td><a href='/location/<?= $row['send_loc_code'] ?>'><?= $row['send_loc_name'] ?></a></td><?php
					}
					if($disp['sent_at'])
					{
						?><td><?php HtmlSnippets::printTimestamp($row['sent_at']) ?></td><?php
					}
					if($disp['days_travelling'])
					{
						?><td><?= $row['days_travelling'] ?></span></td><?php
					}
					if($disp['postcard_code'])
					{
						?><td><a href='/card/<?= $row['postcard_code'] ?>'><?= $row['postcard_code'] ?></a></td><?php
					}
					if($disp['receiver'])
					{
						?><td><?php HtmlSnippets::printUserPoliteName($row['receiver_login'], $row['receiver_polite_name'], true); ?></td><?php
					}
					if($disp['receive_loc'])
					{
						?><td><a href='/location/<?= $row['receive_loc_code'] ?>'><?= $row['receive_loc_name'] ?></a></td><?php
					}
					if($disp['received_at'])
					{
						?><td><?php HtmlSnippets::printTimestamp($row['received_at']) ?></td><?php
					}
					if($disp['first_image_hash'])
					{
						if(!empty($row['first_image_hash']))
						{
							?><td>&#9745;</td><?php
						}
						else
						{
							?><td>&#9744;</td><?php
						}
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