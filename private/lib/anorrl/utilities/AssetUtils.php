<?php
	namespace anorrl\utilities;

	use anorrl\Asset;
	use anorrl\Place;
	use anorrl\enums\AssetType;
	use anorrl\enums\CatalogFilter;
	use anorrl\utilities\UserUtils;
	
	class AssetUtils {
		
		public static function Get(AssetType $type, string $query = "", int $page = -1, int $count = -1): array {
			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";
			$user = UserUtils::RetrieveUser();
			if($user == null) 
				return [];
			
			$query_filter = "AND `public` = 1 AND `nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `nevershow` = 0";
			}
			
			$stmt_query = "%$query%";
			$stmt_type = $type->ordinal();

			if($page == -1 || $count == -1) {
				$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `name` LIKE ? AND `type` = ? $query_filter");
				$stmt_getuser->bind_param('si', $stmt_query, $stmt_type);
				$stmt_getuser->execute();
				// show all
			} else {
				$stmt_page = (($page-1)*$count);
				
				$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `name` LIKE ? AND `type` = ? $query_filter LIMIT ?, ?");
				$stmt_getuser->bind_param('siii', $stmt_query, $stmt_type, $stmt_page, $count);
				$stmt_getuser->execute();
				// pagify
			}

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					if($row['type'] == AssetType::PLACE->ordinal()) {
						$asset = Place::FromID($row['id']);
					} else {
						$asset = new Asset($row);
					}

					if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {
						$result_array[] = $asset;
					}
				}
				return $result_array;
			}

			return [];

		}
		
		public static function GetFiltered(CatalogFilter $filter, AssetType $type, string $query, int $page = -1, int $count = -1) {

			if($type != AssetType::PLACE && 
				($filter == CatalogFilter::MostPopular || $filter == CatalogFilter::MostVisited)) {
				$filter = CatalogFilter::RecentlyUploaded;
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			$user = UserUtils::RetrieveUser();
			if($user == null) 
				return [];
			
			$query_filter = "AND `public` = 1 AND `nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `nevershow` = 0";
			}

			$base_sql_query = "SELECT * FROM `assets` WHERE `name` LIKE ? AND `type` = ? $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT places.* FROM `places`, `assets` WHERE assets.id = places.id AND `name` LIKE ? AND `type` = ? $query_filter ".($_SESSION['ANORRL$Games$OriginalOnly'] ? " AND `original` = 1 " : "");
			}
			
			$filter = match($filter) {
				CatalogFilter::RecentlyUploaded => "ORDER BY `created` DESC",
				CatalogFilter::RecentlyUpdated  => "ORDER BY `lastedited` DESC",
				CatalogFilter::OldestUploaded   => "ORDER BY `created` ASC",
				CatalogFilter::OldestUpdated    => "ORDER BY `lastedited` ASC",
				CatalogFilter::MostSold         => "ORDER BY `sales_count` DESC, `lastedited` DESC",
				CatalogFilter::MostFavourited   => "ORDER BY `favourites_count` DESC, `lastedited` DESC",
				CatalogFilter::MostPopular      => "ORDER BY `currently_playing_count` DESC, `visit_count` DESC, `lastedited` DESC",
				CatalogFilter::MostVisited      => "ORDER BY `visit_count` DESC"
			};
			
			$stmt_query = "%$query%";
			$stmt_type = $type->ordinal();

			if($page == -1 || $count == -1) {
				$stmt_getuser = $con->prepare("$base_sql_query $filter");
				$stmt_getuser->bind_param('si', $stmt_query, $stmt_type);
				$stmt_getuser->execute();
			} else {
				$stmt_page = (($page-1)*$count);
				
				$stmt_getuser = $con->prepare("$base_sql_query $filter LIMIT ?, ?");
				$stmt_getuser->bind_param('siii', $stmt_query, $stmt_type, $stmt_page, $count);
				$stmt_getuser->execute();
			}

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					if($type == AssetType::PLACE) {
						$asset = Place::FromID($row['id']);
					} else {
						$asset = new Asset($row);
					}

					if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {
						$result_array[] = $asset;
					}
				}
				return $result_array;
			}

			return [];
		}

		public static function GetFilteredCount(CatalogFilter $filter, AssetType $type, string $query, int $page = -1, int $count = -1) {

			if($type != AssetType::PLACE && 
				($filter == CatalogFilter::MostPopular || $filter == CatalogFilter::MostVisited)) {
				$filter = CatalogFilter::RecentlyUploaded;
			}

			include $_SERVER["DOCUMENT_ROOT"]."/private/connection.php";

			
			$user = UserUtils::RetrieveUser();
			if($user == null) 
				return 0;
			
			$query_filter = "AND `public` = 1 AND `nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `nevershow` = 0";
			}

			$base_sql_query = "SELECT COUNT(`id`) FROM `assets` WHERE `name` LIKE ? AND `type` = ? $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT COUNT(`places`.`id`) FROM `places`, `assets` WHERE assets.id = places.id AND `name` LIKE ? AND `type` = ? $query_filter ".($_SESSION['ANORRL$Games$OriginalOnly'] ? " AND `original` = 1 " : "");
			}
			
			$filter = match($filter) {
				CatalogFilter::RecentlyUploaded => "ORDER BY `created` DESC",
				CatalogFilter::RecentlyUpdated  => "ORDER BY `lastedited` DESC",
				CatalogFilter::OldestUploaded   => "ORDER BY `created` ASC",
				CatalogFilter::OldestUpdated    => "ORDER BY `lastedited` ASC",
				CatalogFilter::MostSold         => "ORDER BY `sales_count` DESC, `lastedited` DESC",
				CatalogFilter::MostFavourited   => "ORDER BY `favourites_count` DESC, `lastedited` DESC",
				CatalogFilter::MostPopular      => "ORDER BY `currently_playing_count` DESC, `visit_count` DESC, `lastedited` DESC",
				CatalogFilter::MostVisited      => "ORDER BY `visit_count` DESC"
			};
			
			$stmt_query = "%$query%";
			$stmt_type = $type->ordinal();

			if($page == -1 || $count == -1) {
				$stmt_getuser = $con->prepare("$base_sql_query $filter");
				$stmt_getuser->bind_param('si', $stmt_query, $stmt_type);
				$stmt_getuser->execute();
			} else {
				$stmt_page = (($page-1)*$count);
				
				$stmt_getuser = $con->prepare("$base_sql_query $filter LIMIT ?, ?");
				$stmt_getuser->bind_param('siii', $stmt_query, $stmt_type, $stmt_page, $count);
				$stmt_getuser->execute();
			}

			$result = $stmt_getuser->get_result();

			$row = $result->fetch_assoc();

			if($row == null) {
				return -1;	
			}
			
			if($type == AssetType::PLACE) {
				return $row['COUNT(`places`.`id`)'];
			}
			
			return $row['COUNT(`id`)'];
		}
		/*
		SELECT COUNT(column_name)
FROM table_name
WHERE condition; */

	}
?>