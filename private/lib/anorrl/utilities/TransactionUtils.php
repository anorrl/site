<?php

	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\User;

	class TransactionUtils {
		private static function getRandomString($length = 15): string {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < $length; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
	
			return $randomString;
		}

		
		public static function GenerateID() {
			$id = self::getRandomString();

			$instances = Database::singleton()->run(
				"SELECT `id` FROM `transactions` WHERE `id` LIKE :id",
				[ ":id" => $id ]
			)->rowCount();
			
			if($instances != 0) {
				return self::GenerateID();
			} else {
				return $id;
			}
		}


		public static function CommitTransaction(User $user, Asset $asset) {
			$ta_id = self::GenerateID();

			Database::singleton()->run(
				"INSERT INTO `transactions`(`id`, `userid`, `assetcreator`, `asset`) VALUES (:id, :uid, :auid, :aid)",
				[
					":id"     => $ta_id,
					":uid"    => $user->id,
					":auid"   => $asset->creator->id,
					":aid"    => $asset->id,
				]
			);

			$asset->updateSalesCount();
		}
	}
?>