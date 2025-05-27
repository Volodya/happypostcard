<?php

interface Image
{
	public function getThumb200() : string;
	public function getFileUrl() : string;
	public function getHash() : string;
}
