<?php

class Performer_Permissions
{
	public static function canUploadPostcardImage(UserExisting $actor, Card $card) : bool
	{
		return in_array($actor->getId(), $card->getUserIds()) or $actor->isAdmin();
	}
	public static function canDeletePostcardImage(UserExisting $actor, Card $card, PictureScan $image) : bool
	{
		return 
			($card->isRegistered() and $actor->getId() == $card->getReceiverId()) or
			($actor->getId() == $card->getSenderId())
			or $actor->isAdmin();
	}
	public static function canChangePositionOfPostcardImage(UserExisting $actor, Card $card, PictureScan $imageA, PictureScan $imageB) : bool
	{
		return 
			($card->isRegistered() and $actor->getId() == $card->getReceiverId()) or
			($actor->getId() == $card->getSenderId())
			or $actor->isAdmin();
	}
	public static function canUploadUserImage(UserExisting $actor, UserExisting $user) : bool
	{
		return $actor->getId() == $user->getId() or $actor->isAdmin();
	}
	public static function canDeleteUserImage(UserExisting $actor, UserExisting $user) : bool
	{
		return $actor->getId() == $user->getId() or $actor->isAdmin();
	}
	public static function canChangePositionOfUserImage(UserExisting $actor, UserExisting $user) : bool
	{
		return $actor->getId() == $user->getId() or $actor->isAdmin();
	}
}