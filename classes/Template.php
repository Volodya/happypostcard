<?php

class Template
{
	private string $template;
	private Array $options;
	private $complexWidgets;
	
	private array $siteNotices;
	private array $userNotices;
	private array $errors;
	
	private array $widgetsLeft;
	private array $widgetsRight;
	private array $widgetsBottom;
	
	private bool $loginStatus;
	private bool $adminStatus;
	private User $user;
	
	public function __construct(string $template, array $op = [])
	{
		$this->template = $template;
		$this->siteNotices = [];
		$this->userNotices = [];
		$this->errors = [];
		$this->options = $op;
		
		$this->widgetsLeft = [];
		$this->widgetsRight = [];
		$this->widgetsBottom = [];
		
		$this->loginStatus = false;
		$this->adminStatus = false;
		$this->user = new UserAnonymous();
	}
	public function withExtraSiteNotices(array $siteNotices) : Template
	{
		$new = clone $this;
		$new->siteNotices = array_merge( $this->siteNotices, $siteNotices );
		return $new;
	}
	public function withExtraUserNotices(array $userNotices) : Template
	{
		$new = clone $this;
		$new->userNotices = array_merge( $this->userNotices, $userNotices );
		return $new;
	}
	public function withExtraErrors(array $errors) : Template
	{
		$new = clone $this;
		$new->errors = array_merge( $this->errors, $errors );
		return $new;
	}
	public function withLeft(array $widgetsLeft) : Template
	{
		$new = clone $this;
		$new->widgetsLeft = array_merge( $this->widgetsLeft, $widgetsLeft );
		return $new;
	}
	public function withRight(array $widgetsRight) : Template
	{
		$new = clone $this;
		$new->widgetsRight = array_merge( $this->widgetsRight, $widgetsRight );
		return $new;
	}
	public function withBottom(array $widgetsBottom) : Template
	{
		$new = clone $this;
		$new->widgetsBottom = array_merge( $this->widgetsBottom, $widgetsBottom );
		return $new;
	}
	public function withLoginStatus(bool $loginStatus) : Template
	{
		$new = clone $this;
		$new->loginStatus = $loginStatus;
		return $new;
	}
	public function withUser(User $user) : Template
	{
		$new = clone $this;
		if($user instanceof UserExisting)
		{
			$new->loginStatus = true;
			if($user->isAdmin())
			{
				$new->adminStatus = true;
			}
		}
		$new->user = $user;
		
		return $new;
	}
	public function isLoggedIn() : bool
	{
		return $this->loginStatus;
	}
	public function getUserId() : int
	{
		return $this->user->getId();
	}
	public function getUser() : User
	{
		return $this->user;
	}
	
	public function additionalTitle() : string
	{
		return isset($this->options['additional_title']) ? ' — '.$this->options['additional_title'] : '';
	}
	
	/*
	 * ['name', 'logged_in' => true]
	 * if logged_in is not present it is displayed always
	 * if it is true it is displayed only if a user is logged in
	 * if it is false it is displayed only if a user is not logged in
	 * 
	 * [ 'name' ]
	 * [ [ 'name' ], [ 'name2' ] ]
	 * If a widget is a file, it always terminates the queue
	 * If a widget is a function inside CustomWidgets, it terminates if returns non-bool
	 * If it returns bool it terminates if it returns true
	 */
	private function includeWidgets(array $widgets, array $performerResults = array())
	{
		foreach($widgets as $widgetQueue)
		{
			if( !isset($widgetQueue['queue']) )
			{
				$widgetQueue = ['queue' => [$widgetQueue]];
			}
			else
			{
				echo '<!--queue['.sizeof($widgetQueue['queue']).']-->';
			}
			$haveDisplayed=false;
			foreach($widgetQueue['queue'] as $w)
			{
				if($haveDisplayed)
				{
					break;
				}
				if( !isset($w[0]) )
				{
					continue;
				}
				
				echo "<!--{$w[0]}-->";
				ob_start();
				
				if(isset($w['logged_in']))
				{
					if($w['logged_in'] != $this->loginStatus)
					{
						ob_end_clean();
						continue;
					}
				}
				if(isset($w['admin']))
				{
					if($w['admin'] != $this->adminStatus)
					{
						ob_end_clean();
						continue;
					}
				}
				if(isset($w['view_of_self']))
				{
					if($w['view_of_self'] === false && $this->user->getLogin() == $this->options['user']->getLogin())
					{
						ob_end_clean();
						continue;
					}
					else if($w['view_of_self'] === true && $this->user->getLogin() != $this->options['user']->getLogin())
					{
						ob_end_clean();
						continue;
					}
				}
				
				if(class_exists("ComWid_{$w[0]}")) // New style of Complex Widget
				{
					$className = "ComWid_{$w[0]}";
					$widget = new $className();
					$widget->invoke($w['parameter'], $performerResults);
					
					if(!$widget->haveDisplayed() and isset($w['clear_on_false']) and $w['clear_on_false'])
					{
						ob_clean();
						continue;
					}
				}
				
				elseif((@include("template/widget_{$w[0]}.php")) !== false) // trivial widgets
				{
					// do nothing
				}
				elseif((@include("template/cw_{$w[0]}.php")) !== false)
				{
					echo '<!-- old style complex widget -->';
					if(!is_callable($w[0]))
					{
						ob_end_clean();
						echo '<!-- function does not exist in file -->';
						continue;
					}
					$rf = new ReflectionFunction($w[0]);
					
					if($rf->getNumberOfRequiredParameters() == 0)
					{
						$res = $rf->invoke();
					}
					else if($rf->getNumberOfRequiredParameters() == 1)
					{
						if(isset($w['parameter']))
						{
							$res = $rf->invoke($w['parameter']);
						}
						else
						{
							continue;
						}
					}
					else
					{
						continue;
					}
					$haveDisplayed = (isset($res) and is_bool($res) and $res);
					
					if(!$haveDisplayed and isset($w['clear_on_false']) and $w['clear_on_false'])
					{
						ob_clean();
						continue;
					}
				}
				elseif(is_callable([$this->complexWidgets, $w[0]]))
				{
					echo '<!-- depricated way to include complex widgets -->';
					$rf = new ReflectionMethod($this->complexWidgets, $w[0]);
					if($rf->getNumberOfRequiredParameters() == 0)
					{
						$res = $rf->invoke($this->complexWidgets);
					}
					else if($rf->getNumberOfRequiredParameters() == 1)
					{
						if(isset($w['parameter']))
						{
							$res = $rf->invoke($this->complexWidgets, $w['parameter']);
						}
						else
						{
							continue;
						}
					}
					else
					{
						continue;
					}
					$haveDisplayed = (isset($res) and is_bool($res) and $res);
					
					if(!$haveDisplayed and isset($w['clear_on_false']) and $w['clear_on_false'])
					{
						ob_clean();
						continue;
					}
				}
				else
				{
					$haveDisplayed = true;
				}
				$content = ob_get_clean();
				if($content === false)
				{
					continue;
				}
				else if($w['make_section'] === true)
				{
					echo '<section>';
					if(isset($w['section_header']))
					{
						?><h1><?= $w['section_header'] ?></h1><?php
					}
					echo $content;
					echo '</section>';
				}
				else
				{
					echo $content;
				}
			} // foreach($widgetQueue as $w)
		} //foreach($widgets as $widgetQueue)
	}
	
	public function toString() : string
	{
		$this->complexWidgets = new ComplexWidgets($this, $this->options);
		ob_start();
		@include('template/'.$this->template.'.php');
		$content = ob_get_contents();
		ob_end_clean();
		if($content === false)
		{
			return 'TEMPLATE COULD NOT GET CONTENT';
		}
		return $content;
	}
}