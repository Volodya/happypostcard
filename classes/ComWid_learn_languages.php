<?php

class ComWid_learn_languages extends SimpWid
{
	public function invoke() : void
	{
		$db = Database::getInstance();
		$stmt = $db->prepare('
			SELECT `language`, `language_code`, `phrase` FROM `learnlanguages_thanks` ORDER BY Random() LIMIT 1
		');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		?>In <?= $result['language']; ?> thank a postal worker with:<br/><?php
		?><span lang='<?= $result['language_code'] ?>'><?= $result['phrase'] ?></span>.<?php
	}
}