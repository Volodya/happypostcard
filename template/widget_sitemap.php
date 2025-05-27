<footer>
	<nav>
<ul class='bottom-sitemap'>
	<li><a href='/home'>Home</a></li>
	<li><a href='/news'>News</a></li>
<?php if($this->isLoggedIn()) { ?>
	<li><a href='/travelling'>Travelling</a></li>
	<li><a href='/send'>Send</a>
		<ul>
			<li><a href='/birthday'>Birthday</a></li>
		</ul>
	</li>
	<li><a href='/receive'>Register</a></li>
	<li><a href='/statistics'>Statistics</a>
		<ul class='bottom-sitemap'>
			<li><a href='/users'>User list</a></li>
			<li><a href='/wpd_cards'>WPD&nbsp;gallery</a></li>
		</ul>
	</li>
	<!--<li><a href='/discuss'>Discuss</a></li>-->
	<li><a href='/user/<?= $this->user->getLogin() ?>'>Profile</a>
		<ul>
			<li><a href='/useredit/<?= $this->user->getLogin() ?>'>Edit</a></li>
		</ul>
	</li>
	<li><a href='/account'>Account</a>
		<ul>
			<li><a href='/performlogout'>Logout</a></li>
		</ul>
	</li>
<?php } else { ?>
	<li><a href='/login'>Login</a></li>
<?php } ?>
<?php if($this->isLoggedIn()) { ?>
	<li><a href='/help'>Help</a>
		<ul>
			<li><a href='/faq'>FAQ</a></li>
		</ul>
	</li>
<?php } ?>
</ul></nav>
</footer>