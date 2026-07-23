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

		public static function UndoTransaction(User $user, Asset $asset) {
			if($asset->isOwner($user, true))
				return;
			$db = Database::singleton();
			
			$db->run(
				"DELETE FROM `transactions` WHERE `userid` = :uid AND `assetcreator` = :auid AND `asset` = :aid",
				[
					":uid"    => $user->id,
					":auid"   => $asset->creator->id,
					":aid"    => $asset->id,
				]
			);

			$db->run(
				"DELETE FROM `inventory` WHERE `assetid` = :id AND `userid` = :user",
				[
					":id"   => $asset->id,
					":user" => $user->id
				]
			);

			$user->updateOutfitHash();
			$asset->updateSalesCount();
		}
	}
?>