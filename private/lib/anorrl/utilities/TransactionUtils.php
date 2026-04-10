<?php

	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\User;
	use anorrl\enums\TransactionType;


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

		/*public static function BuyItem(int|string $asset_id): string {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			
			$get_user = SESSION ? SESSION->user : null;
			
			if($get_user != null && !$get_user->IsBanned()) {
				$asset = Asset::FromID($asset_id);
				if($asset != null && $asset->isUsable()) {
					if(!$get_user->Owns($asset) && $asset->onsale) {
						$ta_id = self::GenerateID();
						$ta_userid = $get_user->id;
						$ta_asset = $asset->id;
						$stmt_processtransaction = $con->prepare("INSERT INTO `transactions`(`id`, `userid`, `asset`, `assetcreator`) VALUES (?, ?, ?, ?)");
						$stmt_processtransaction->bind_param('siii', $ta_id, $ta_userid, $ta_asset, $asset->creator->id);
						if($stmt_processtransaction->execute()) {
							$stmt_get_sale_count = $con->prepare("SELECT * FROM `transactions` WHERE `asset` = ? AND `userid`!= ?");
							$stmt_get_sale_count->bind_param('ii', $asset_id, $asset->creator->id);
							$stmt_get_sale_count->execute();
							$sale_count = $stmt_get_sale_count->get_result()->num_rows;
		
							$stmt_update_sale_stat = $con->prepare("UPDATE `assets` SET `asset_sales_count` = ? WHERE `asset_id` = ?");
							$stmt_update_sale_stat->bind_param('ii', $sale_count, $asset_id);
							$stmt_update_sale_stat->execute();
							return "yay";
						} else {
							return "Something went wrong at our end!";
						}
					} else {
						if($get_user->Owns($asset)) {
							return "You already own this asset.";
						} else if(!$asset->onsale) {
							return "Item is off-sale sorry not sorry...";
						} else {
							return "Item is off-sale and beside you already own this?";
						}
					}
					
				} else {
					if($asset == null) {
						return "That asset doesn't exist!";
					} else {
						return "That asset is unusable at this time!";
					}
				}
			} else {
				return "User is not authorised to perform this action!";
			}
		}*/



		public static function CommitTransaction(TransactionType $type, User $user, int $cost = 0, Asset|null $asset = null) {
			$ta_id = self::GenerateID();

			if($asset) {
				Database::singleton()->run(
					"INSERT INTO `transactions`(`id`, `userid`, `assetcreator`, `asset`, `method`, `cost`) VALUES (:id, :uid, :auid, :aid, :method, :cost)",
					[
						":id"     => $ta_id,
						":uid"    => $user->id,
						":auid"   => $asset->creator->id,
						":aid"    => $asset->id,
						":method" => $type->ordinal(),
						":cost"   => $cost
					]
				);
			} else {
				Database::singleton()->run(
					"INSERT INTO `transactions`(`id`, `userid`, `method`, `cost`) VALUES (?, ?, ?, ?)",
					[
						":id"     => $ta_id,
						":uid"    => $user->id,
						":method" => $type->ordinal(),
						":cost"   => $cost
					]
				);
			}
		}

		public static function CommitAssetTransaction(TransactionType $type, Asset $asset, User $user) {
			$cost = 0;
			switch($type) {
				case TransactionType::CONES:
					$cost = $asset->cones;
					break;
				case TransactionType::LIGHTS:
					$cost = $asset->lights;
					break;
			}
			self::CommitTransaction($type, $user, $cost, $asset);
		}
	}
?>