<?php
	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\Place;
	use anorrl\Universe;
	use anorrl\enums\AssetType;
	use anorrl\enums\CatalogFilter;
	use anorrl\utilities\UserUtils;
	
	class AssetUtils {
		
		public static function Get(AssetType $type, string $query = "", int $page = -1, int $count = -1): array {
			//if(!\SESSION) 
			//	return [];

			//$user = \SESSION->user;
			$db = Database::singleton();

			/*if($user == null) 
				return [];*/
			
			$query_filter = "AND `public` = 1 AND `nevershow` = 0";
			/*if($user->isAdmin()) {
				$query_filter = "AND `nevershow` = 0";
			}*/
			
			$stmt_query = "%$query%";
			$stmt_type = $type->ordinal();
			
			$rows = [];

			if($page == -1 || $count == -1) {
				$rows = $db->run(
					"SELECT `id`,`type` FROM `assets` WHERE `name` LIKE :search AND `type` = :type $query_filter",
					[
						":search" => $stmt_query,
						":type" => $stmt_type
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			} else {
				$rows = $db->run(
					"SELECT `id`,`type` FROM `assets` WHERE `name` LIKE :search AND `type` = :type $query_filter LIMIT :page, :size",
					[
						":search" => $stmt_query,
						":type" => $stmt_type,
						":page" => (($page-1)*$count),
						":size" => $count
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			}

			$result_array = [];
			
			foreach($rows as $row) {
				if($type == AssetType::PLACE->ordinal()) {
					$asset = Place::FromID($row->id);
				} else {
					$asset = Asset::FromID($row->id);
				}

				//if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {}
				$result_array[] = $asset;
			}

			return $result_array;
		}
		
		public static function GetFiltered(CatalogFilter $filter, AssetType $type, string $query, int $page = -1, int $count = -1) {

			if($type != AssetType::PLACE && 
				($filter == CatalogFilter::MostPopular || $filter == CatalogFilter::MostVisited || $filter == CatalogFilter::Random)) {
				$filter = CatalogFilter::RecentlyUploaded;
			}

			$is_bodyparts = $type == AssetType::BODYPARTS;

			/*if(!\SESSION) 
				return [];

			$user = \SESSION->user;
			if($user == null) 
				return [];*/
			
			$query_filter = "AND `assets`.`public` = 1 AND `nevershow` = 0";
			// if($user->isAdmin()) { $query_filter = "AND `nevershow` = 0"; }

			$sql_types = "`type` = :type";

			if($is_bodyparts) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg OR `type` = :type)";
			}

			$base_sql_query = "SELECT `id` FROM `assets` WHERE `name` LIKE :query AND $sql_types $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT places.id FROM `universes`, `places`, `assets` WHERE assets.id = places.id AND universes.starting_place = assets.id AND `name` LIKE :query AND `type` = :type $query_filter ".($_SESSION['ANORRL$Games$OriginalOnly'] ? " AND `original` = 1 " : "");
			}
			
			$sql_filter = $filter->getSQL();

			$db = Database::singleton();

			if($page == -1 || $count == -1) {
				$rows = $db->run(
					"$base_sql_query $sql_filter",
					[
						":query" => "%$query%",
						":type" => $type->ordinal()
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			} else {
				$rows = $db->run(
					"$base_sql_query $sql_filter LIMIT :page, :count", 
					[
						":query" => "%$query%",
						":type" => $type->ordinal(),
						":page" => (($page-1)*$count),
						":count" => $count
					]
				)->fetchAll(\PDO::FETCH_OBJ);
			}

			$result_array = [];

			foreach($rows as $row) {
				if($type == AssetType::PLACE) {
					$asset = Place::FromID($row->id);
				} else {
					$asset = Asset::FromID($row->id);
				}
				//if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {}
				$result_array[] = $asset;
			}
			return $result_array;
		}

		public static function GetFilteredCount(CatalogFilter $filter, AssetType $type, string $query, int $page = -1, int $count = -1) {

			if($type != AssetType::PLACE && 
				($filter == CatalogFilter::MostPopular || $filter == CatalogFilter::MostVisited || $filter == CatalogFilter::Random)) {
				$filter = CatalogFilter::RecentlyUploaded;
			}

			$is_bodyparts = $type == AssetType::BODYPARTS;

			/*if(!\SESSION)
				return 0;*/
			
			$query_filter = "AND `public` = 1 AND `nevershow` = 0";
			// if($user->isAdmin()) { $query_filter = "AND `nevershow` = 0"; }

			$sql_types = "`type` = :type";

			if($is_bodyparts) {
				$type_head = AssetType::HEAD->ordinal();
				$type_torso = AssetType::TORSO->ordinal();
				$type_leftarm = AssetType::LEFTARM->ordinal();
				$type_rightarm = AssetType::RIGHTARM->ordinal();
				$type_leftleg = AssetType::LEFTLEG->ordinal();
				$type_rightleg = AssetType::RIGHTLEG->ordinal();

				$sql_types = "(`type` = $type_head OR `type` = $type_torso OR `type` = $type_leftarm OR `type` = $type_rightarm OR `type` = $type_leftleg OR `type` = $type_rightleg OR `type` = :type)";
			}

			$base_sql_query = "SELECT COUNT(`id`) FROM `assets` WHERE `name` LIKE :query AND $sql_types $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT COUNT(`places`.`id`) FROM `places`, `assets` WHERE assets.id = places.id AND `name` LIKE :query AND `type` = :type $query_filter ";
			}
			
			$sql_filter = $filter->getSQL();

			$db = Database::singleton();

			if($page == -1 || $count == -1) {
				$row = $db->run(
					"$base_sql_query $sql_filter",
					[
						":query" => "%$query%",
						":type" => $type->ordinal()
					] 
				)->fetch(\PDO::FETCH_ASSOC);
			} else {
				$row = $db->run(
					"$base_sql_query $sql_filter LIMIT :page, :count",
					[
						":query" => "%$query%",
						":type" => $type->ordinal(),
						":page" => (($page-1)*$count),
						":count" => $count
					]
				)->fetch(\PDO::FETCH_ASSOC);
			}

			if(!$row) {
				return -1;
			}
			
			if($type == AssetType::PLACE) {
				return $row['COUNT(`places`.`id`)'];
			}
			
			return $row['COUNT(`id`)'];
		}
	}
?>
