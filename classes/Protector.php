<?php

interface Protector
{
	public function protect(Request $request, Response $response) : array; // list(Request, Response)
}