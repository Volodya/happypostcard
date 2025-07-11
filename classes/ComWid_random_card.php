<?php

class ComWid_random_card extends SimpWid
{
	public function invoke() : void
	{
		try
		{
			$rCard = Card::getRandomCardImage();
			HtmlSnippets::printPostcardThumb200($rCard['hash'], $rCard['extension'], $rCard['code'], true);
		}
		catch(Exception $e)
		{
			echo 'No card';
		}
	}
}