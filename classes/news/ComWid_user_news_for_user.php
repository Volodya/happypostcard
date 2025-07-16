<?php

class ComWid_user_news_for_user implements ComWid
{
	private User $user;
	private User $viewer;
	
	private bool $displayed;
	
	public function __construct()
	{
		$this->displayed = false;
	}
	public function setEditor(User $editor) : void
	{
		$this->viewer = $editor;
	}
	public function setTemplateParameter(array $parameter) : void
	{
		$this->user = $parameter['user'];
	}
	public function setPerformerResults($performerResults) : void
	{}
	public function invoke() : void
	{
		$user = $this->user;
		$viewer = $this->viewer;
		
		if(!($user instanceof UserExisting))
		{
			?>This user is not registered!<?php
			$this->displayed = true;
			return;
		}
		
		$viewOfSelf = $user->getLogin() == $viewer->getLogin();
		
		$userName = $user->getPoliteName();
		$userNameUC = $userName;
		$userNamePossesive = "{$userName}&apos;s";
		$userNameUCPossesive = $userNamePossesive;
		if($viewOfSelf)
		{
			$userName = 'you';
			$userNameUC = 'You';
			$userNamePossesive = 'your';
			$userNameUCPossesive = 'Your';
		}
		
		$news = $user->getUserNews();
		?><table><?php
			?><thead><?php
				?><th scope='col'>Date</th><?php
				?><th scope='col'>Event</th><?php
			?></thead><?php
			?><tbody><?php
				$count = 15;
				foreach($news as $row)
				{
					$willDisplay = false;
					ob_start();
					
					$date = substr($row['ts'], 0, 10);
					$time = substr($row['ts'], 11);
					?><tr>
						<td><span title='<?= $time ?>'><?= $date ?></span></td>
						<?php
						switch($row['type'])
						{
							case 'registration':
								$willDisplay=true;
								?><td><?php
									?>〠 <?php
									?><?= $userNameUC ?> registered on this site.<?php
								?></td><?php
								break;
							case 'postcard_yousent':
								if($viewOfSelf)
								{
									$willDisplay=true;
									$receiverName = HtmlSnippets::getUserPoliteName($row['login'], $row['polite_name'], true);
									?><td><?php
										?>📮 <?php
										?>You sent a <a href='/card/<?= $row['card_code'] ?>'>happy postcard</a> <?php
										?>to <?= $receiverName ?>.<?php
									?></td><?php
								}
								break;
							case 'postcard_yousent_received':
								$willDisplay=true;
								$receiverName = HtmlSnippets::getUserPoliteName($row['login'], $row['polite_name'], true);
								?><td><?php
									?>📬 <?php
									?><?= $receiverName ?> received a <?php
									?><a href='/card/<?= $row['card_code'] ?>'>happy postcard</a> from <?= $userName ?>.<?php
								?></td><?php
								break;
							case 'postcard_othersent':
								if($viewer->getLogin() == $row['login'])
								{
									$willDisplay=true;
									$senderName = HtmlSnippets::getUserPoliteName($row['login'], 'You', true);
									?><td><?php
										?>📨 <?php
										?><?= $senderName ?> sent <?= $userName ?> a <?php
										?><a href='/card/<?= $row['card_code'] ?>'>happy postcard</a>.<?php
									?></td><?php
								}
								break;
							case 'postcard_othersent_received':
								$willDisplay=true;
								if($viewer->getLogin() == $row['login'])
								{
									$senderName = HtmlSnippets::getUserPoliteName($row['login'], 'Your', true);
								}
								else
								{
									$senderName = HtmlSnippets::getUserPoliteName($row['login'], $row['polite_name'], true).'&apos;s';
								}
									
								?><td><?php
									?>📩 <?php
									?><?= $senderName ?> <a href='/card/<?= $row['card_code'] ?>'>happy postcard</a> <?php
									?>was received by <?= $userName ?>.<?php
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