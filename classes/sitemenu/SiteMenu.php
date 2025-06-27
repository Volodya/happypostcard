<?php

class SiteMenu
{
	private string $menuUlClass;
	private string $submenuUlClass;
	private string $helpLiClass;
	
	public function __construct(string $menuType /* top|bottom */)
	{
		if($menuType=='top')
		{
			$this->menuUlClass = 'menu top-menu';
			$this->submenuUlClass = 'menu dropdown-menu';
			$this->helpLiClass = 'help';
		}
		else if($menuType=='bottom')
		{
			$this->menuUlClass = 'bottom-sitemap';
			$this->submenuUlClass = '';
			$this->helpLiClass = '';
		}
	}
	public function generateMenu(User $viewer) : void
	{
		$isLoggedIn = $viewer instanceof UserExisting;
		$userLogin = $viewer->getLogin();
		
		?><nav aria-label='main'><?php
			?><ul class='<?= $this->menuUlClass ?>'><?php
				?><li><a href='/home'>Home</a></li><?php
				if($viewer->isAdmin())
				{
					?><li><a href='/admin'>Admin</a></li><?php
				}
				?><li><a href='/news'>News</a><?php
					?><ul class='<?= $this->submenuUlClass ?>'><?php
						?><li><a href='/development_news/'>Development</a></li><?php
					?></ul><?php
				?></li><?php
				if($isLoggedIn)
				{
					?><li><a href='/travelling'>Travelling</a><?php
						?><ul class='<?= $this->submenuUlClass ?>'><?php
							?><li><a href='/sent/<?= $userLogin ?>'>Sent</a></li><?php
							?><li><a href='/received/<?= $userLogin ?>'>Received</a></li><?php
						?></ul><?php
					?></li><?php
					?><li><a href='/send'>Send</a><?php
						?><ul class='<?= $this->submenuUlClass ?>'><?php
							?><li><a href='/birthday'>Birthday</a></li><?php
						?></ul><?php
					?></li><?php
					?><li><a href='/receive'>Register</a></li><?php
					?><li><a href='/statistics'>Statistics</a><?php
						?><ul class='<?= $this->submenuUlClass ?>'><?php
							?><li><a href='/users'>User list</a></li><?php
							?><li><a href='/wpd_cards'>WPD&nbsp;gallery</a></li><?php
						?></ul><?php
					?></li><?php
					?><li><a href='/user/<?= $userLogin ?>'>Profile</a><?php
						?><ul class='<?= $this->submenuUlClass ?>'><?php
							?><li><a href='/useredit/<?= $userLogin ?>'>Edit</a></li><?php
						?></ul><?php
					?></li><?php
					?><li><a href='/account'>Account</a><?php
						?><ul class='<?= $this->submenuUlClass ?>'><?php
							?><li><a href='/performlogout'>Logout</a></li><?php
						?></ul><?php
					?></li><?php
			}
			else
			{
				?><li><a href='/login'>Login</a></li><?php
			}
			if($isLoggedIn)
			{
				?><li class='<?= $this->helpLiClass ?>'><a href='/help'>Help</a><?php
					?><ul class='<?= $this->submenuUlClass ?>'><?php
						?><li><a href='/faq'>FAQ</a></li><?php
					?></ul><?php
				?></li><?php
			}
			?></ul><?php
		?></nav><?php
	}
}