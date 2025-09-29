<?php

class ComWid_admin_section extends SimpWid
{
	public function invoke() : void
	{
		?><section class='important'><?php
			?><h1>ADMIN</h1><?php
			?>You are now in <a href='/admin'>the admin section</a>.<?
		?></section><?php
	}
}