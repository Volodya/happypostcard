<section>
	<header><?= $this->options['user']->getLogin() ?></header>
	<?php $this->complexWidgets->user_statistics($this->options['user']); ?>
</section>