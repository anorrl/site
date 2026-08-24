<?php

	namespace anorrl;

	use anorrl\Place;
	use anorrl\Database;
	use anorrl\User;

	class Rating {

		public static function Rate(Place $place, bool $positive) {
			if(!\ARLAUTH)
				return ["success" => false, "reason" => "User is not authorized to perform this action."];

			// maybe check if universe or not

			$user = SESSION->user;

			if($user->isBanned())
				return ["success" => false, "reason" => "User is not authorized to perform this action."];

			$row = Database::singleton()->run(
				"SELECT `rating` FROM `ratings` WHERE `userid` = :user AND `placeid` = :place",
				[
					":user" => $user->id,
					":place" => $place->id,
				]
			)->fetch(\PDO::FETCH_OBJ);

			if(!$row) {
				Database::singleton()->run(
					"INSERT INTO `ratings`(`placeid`, `userid`, `rating`) VALUES (:place, :user, :rating)",
					[
						":user" => $user->id,
						":place" => $place->id,
						":rating" => $positive ? 1 : 0,
					]
				);
			}
			else {
				$prev_rating = boolval($row->rating);

				if($prev_rating == $positive) {
					Database::singleton()->run(
						"DELETE FROM `ratings` WHERE `placeid` = :place AND `userid` = :user",
						[
							":user" => $user->id,
							":place" => $place->id,
						]
					);
				}
				else {
					Database::singleton()->run(
						"UPDATE `ratings` SET `rating` = :rating, `date` = now() WHERE `placeid` = :place AND `userid` = :user",
						[
							":user" => $user->id,
							":place" => $place->id,
							":rating" => $positive ? 1 : 0,
						]
					);
				}
			}

			$data_ratings = self::GetRatingsOn($place);
			$ratings = $data_ratings['positives'] - $data_ratings['negatives'];

			Database::singleton()->run(
				"UPDATE `places` SET `ratings` = :rating WHERE `id` = :place",
				[
					":place" => $place->id,
					":rating" => $ratings,
				]
			);

			return ["success" => true];
		}

		public static function HasUserRatedOn(Place $place) {
			return \ARLAUTH ? Database::singleton()->run(
				"SELECT `rating` FROM `ratings` WHERE `userid` = :user AND `placeid` = :place",
				[
					":user" => SESSION->user->id,
					":place" => $place->id,
				]
			)->rowCount() != 0 : false;
		}

		public static function GetRatingsOn(Place $place) {
			$positives = Database::singleton()->run(
				"SELECT `id` FROM `ratings` WHERE `placeid` = :place AND `rating` = 1",
				[
					":place" => $place->id,
				]
			)->rowCount();

			$negatives = Database::singleton()->run(
				"SELECT `id` FROM `ratings` WHERE `placeid` = :place AND `rating` = 0",
				[
					":place" => $place->id,
				]
			)->rowCount();

			return [
				"positives" => $positives,
				"negatives" => $negatives,
				"can_vote" => \ARLAUTH,
				"has_voted" => self::HasUserRatedOn($place)
			];
		}
	}

?>
