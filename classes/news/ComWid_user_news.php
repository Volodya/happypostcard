<?php

class ComWid_user_news implements ComWid
{
	private User $user;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->user = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$user = $this->user;
		if(!($user instanceof UserExisting))
		{
			?>This user is not registered!<?php
			$this->displayed = true;
			return;
		}
		
		$news = $user->getUserNews();
		?><table><?php
			?><thead><?php
				?><th>Date</th><?php
				?><th>Event</th><?php
			?></thead><?php
			?><tbody><?php
				
				$count = 15;
				foreach($news as $row)
				{
					$willDisplay = false;
					ob_start();
					
					?><tr><?php
						?><td><?php HtmlSnippets::printTimestamp($row['ts']) ?></td><?php
						
						switch($row['type'])
						{
							case 'registration':
								$willDisplay=true;
								?><td><?php
									?><a href='/user/<?= $row['login'] ?>'><?= $row['polite_name'] ?></a> <?php
									?>has registered on this site.<?php
								?></td><?php
								break;
							case 'postcard_yousent':
								$willDisplay=true;
								?><td><?php
									?>You sent a <?php
									?><a href='/card/<?= $row['card_code'] ?>' title='<?= $row['card_code'] ?>'>happy postcard</a> <?php
									?>to <a href='/user/<?= $row['login'] ?>'><?= $row['polite_name'] ?></a>.<?php
								?></td><?php
								break;
							case 'postcard_yousent_received':
								$willDisplay=true;
								?><td><?php
									?>Your sent <?php
									?><a href='/card/<?= $row['card_code'] ?>' title='<?= $row['card_code'] ?>'>happy postcard</a> <?php
									?>was received by <a href='/user/<?= $row['login'] ?>'><?= $row['polite_name'] ?></a>.<?php
								?></td><?php
								break;
							case 'postcard_othersent':
								$willDisplay=true;
								?><td><?php
									?>Somebody has sent you a <span title='Number hidden'>happy postcard</a> <?php
									?>you can wait for it in your letterbox.<?php
								?></td><?php
								break;
							case 'postcard_othersent_received':
								$willDisplay=true;
								?><td><?php
									?>You received a <?php
									?><a href='/card/<?= $row['card_code'] ?>' title='<?= $row['card_code'] ?>'>happy postcard</a> <?php
									?>from <a href='/user/<?= $row['login'] ?>'><?= $row['polite_name'] ?></a>.<?php
								?></td><?php
								break;
						}
					?></tr><?php
					if($willDisplay)
					{
						ob_end_flush();
						if(--$count == 0) break;
					}
					else
					{
						ob_end_clean();
					}
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