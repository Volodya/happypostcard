<?php

class PerformerListAllTravellingPostcards extends Performer_Abstract
{
	private array $result;
	public function __construct()
	{
		$this->result = ['postcards'=>[]];
	}
	public function addAdditionalParameters(array $params) : void
	{}
	public function perform(Request $request, Response $response, Config $config) : Response
	{
		$db = Database::getInstance();
		
		$stmt = $db->prepare('
			SELECT
				`postcard`.`code` AS `postcard_code`,
				`sender`.`login` AS `sender_login`, `sender`.`polite_name` AS `sender_polite_name`,
				`send_location_code`.`code` AS `send_loc_code`, `send_location_code`.`name` AS `send_loc_name`,
				`sent_at`, CAST(JulianDay("now") - JulianDay(`sent_at`) AS INTEGER) AS `days_travelling`,
				`receiver`.`login` AS `receiver_login`, `receiver`.`polite_name` AS `receiver_polite_name`,
				`receive_location_code`.`code` AS `receive_loc_code`, `receive_location_code`.`name` AS `receive_loc_name`,
				`postcard_first_image`.`image_hash` as `first_image_hash`
			FROM `postcard`
			INNER JOIN `user` AS `sender` ON `postcard`.`sender_id` = `sender`.`id`
			INNER JOIN `user` AS `receiver` ON `postcard`.`receiver_id` = `receiver`.`id`
			INNER JOIN `location_code` AS `send_location_code` ON `send_location_code`.`id` = `postcard`.`send_location_id`
			INNER JOIN `location_code` AS `receive_location_code` ON `receive_location_code`.`id` = `postcard`.`receive_location_id`
			LEFT JOIN (
				SELECT `postcard_id`, `hash` AS `image_hash`, `extension` AS `image_extension`
				FROM `postcard_image`
				GROUP BY `postcard_id`
				HAVING `id` = MIN(`id`)
			) AS `postcard_first_image` ON `postcard_first_image`.`postcard_id` = `postcard`.`id`
			WHERE `received_at` IS NULL
			ORDER BY `sent_at` DESC
		');
		$res = $stmt->execute();
		
		$this->result = ['postcards'=> $stmt->fetchAll(PDO::FETCH_ASSOC)];
		
		return $response;
	}
	public function getResult() : array
	{
		return $this->result;
	}
}