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
			
			$query_filter = "AND `asset_public` = 1 AND `asset_nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `asset_nevershow` = 0";
			}
			
			$stmt_query = "%$query%";
			$stmt_type = $type->ordinal();

			if($page == -1 || $count == -1) {
				$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `asset_name` LIKE ? AND `asset_type` = ? $query_filter");
				$stmt_getuser->bind_param('si', $stmt_query, $stmt_type);
				$stmt_getuser->execute();
				// show all
			} else {
				$stmt_page = (($page-1)*$count);
				
				$stmt_getuser = $con->prepare("SELECT * FROM `assets` WHERE `asset_name` LIKE ? AND `asset_type` = ? $query_filter LIMIT ?, ?");
				$stmt_getuser->bind_param('siii', $stmt_query, $stmt_type, $stmt_page, $count);
				$stmt_getuser->execute();
				// pagify
			}

			$result = $stmt_getuser->get_result();

			$result_array = [];

			if($result->num_rows != 0) {
				while($row = $result->fetch_assoc()) {
					if($row['asset_type'] == AssetType::PLACE->ordinal()) {
						$asset = Place::FromID($row['asset_id']);
					} else {
						$asset = new Asset($row);
					}

					if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {
						array_push($result_array, $asset);
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
			
			$query_filter = "AND `asset_public` = 1 AND `asset_nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `asset_nevershow` = 0";
			}

			$base_sql_query = "SELECT * FROM `assets` WHERE `asset_name` LIKE ? AND `asset_type` = ? $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT asset_places.* FROM `asset_places`, `assets` WHERE assets.asset_id = asset_places.place_id AND `asset_name` LIKE ? AND `asset_type` = ? $query_filter ".($_SESSION['ANORRL$Games$OriginalOnly'] ? " AND `place_original` = 1 " : "");
			}
			
			$filter = match($filter) {
				CatalogFilter::RecentlyUploaded => "ORDER BY `asset_created` DESC",
				CatalogFilter::RecentlyUpdated  => "ORDER BY `asset_lastedited` DESC",
				CatalogFilter::OldestUploaded   => "ORDER BY `asset_created` ASC",
				CatalogFilter::OldestUpdated    => "ORDER BY `asset_lastedited` ASC",
				CatalogFilter::MostSold         => "ORDER BY `asset_sales_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostFavourited   => "ORDER BY `asset_favourites_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostPopular      => "ORDER BY `place_currently_playing` DESC, `place_visit_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostVisited      => "ORDER BY `place_visit_count` DESC"
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
						$asset = Place::FromID($row['place_id']);
					} else {
						$asset = new Asset($row);
					}

					if($user->isAdmin() || !$asset->notcatalogueable && $asset->public) {
						array_push($result_array, $asset);
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
				return [];
			
			$query_filter = "AND `asset_public` = 1 AND `asset_nevershow` = 0";
			if($user->isAdmin()) {
				$query_filter = "AND `asset_nevershow` = 0";
			}

			$base_sql_query = "SELECT COUNT(`asset_id`) FROM `assets` WHERE `asset_name` LIKE ? AND `asset_type` = ? $query_filter";
			if($type == AssetType::PLACE) {
				$base_sql_query = "SELECT COUNT(`place_id`) FROM `asset_places`, `assets` WHERE assets.asset_id = asset_places.place_id AND `asset_name` LIKE ? AND `asset_type` = ? $query_filter ".($_SESSION['ANORRL$Games$OriginalOnly'] ? " AND `place_original` = 1 " : "");
			}
			
			$filter = match($filter) {
				CatalogFilter::RecentlyUploaded => "ORDER BY `asset_created` DESC",
				CatalogFilter::RecentlyUpdated  => "ORDER BY `asset_lastedited` DESC",
				CatalogFilter::OldestUploaded   => "ORDER BY `asset_created` ASC",
				CatalogFilter::OldestUpdated    => "ORDER BY `asset_lastedited` ASC",
				CatalogFilter::MostSold         => "ORDER BY `asset_sales_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostFavourited   => "ORDER BY `asset_favourites_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostPopular      => "ORDER BY `place_currently_playing` DESC, `place_visit_count` DESC, `asset_lastedited` DESC",
				CatalogFilter::MostVisited      => "ORDER BY `place_visit_count` DESC"
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
				return $row['COUNT(`place_id`)'];
			}
			
			return $row['COUNT(`asset_id`)'];
		}
		/*
		SELECT COUNT(column_name)
FROM table_name
WHERE condition; */

	}
?>