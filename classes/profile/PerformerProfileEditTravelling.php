<?php

class PerformerProfileEditTravelling extends Performer_Abstract
{
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		if(!$request->allSetPOST(['travelling_location']))
		{
			return $this->abandon($response, 'all fields must exist');
		}
		$user = $request->getLoggedInUser();
		
		$post = $request->getPOST([
			'travelling_location' =>['filter'=>FILTER_UNSAFE_RAW],
			]);
		
		if($post['travelling_location'] == 'off')
		{
			$user->removeTravellingLocation();
		}
		else
		{
			$locationId = Location::getIdByCode($post['travelling_location']);
			$user->updateTravellingLocation($locationId);
		}
		
		$page = (new PageRedirector())->withRedirectTo('/user/'.urlencode($user->getLogin()));
		$response = $response->withPage($page);
		$response = $response->withNoticeMessage('Your pofile has been updated.');
		
		return $response;
	}
}