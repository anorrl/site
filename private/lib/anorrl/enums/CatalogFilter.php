<?php
	namespace anorrl\enums;

	enum CatalogFilter {
		case RecentlyUploaded;
		case RecentlyUpdated;
		case OldestUploaded;
		case OldestUpdated;
		case MostSold;
		case MostFavourited;

		/* Games Stuff Only */
		case MostPopular;
		case MostVisited;
		case Random;

		public function ordinal(): int {
			return match($this) {
				CatalogFilter::RecentlyUploaded => 1,
				CatalogFilter::RecentlyUpdated => 2,
				CatalogFilter::OldestUploaded => 3,
				CatalogFilter::OldestUpdated => 4,
				CatalogFilter::MostSold => 5,
				CatalogFilter::MostFavourited => 6,
				CatalogFilter::MostPopular => 7,
				CatalogFilter::MostVisited => 8,
				CatalogFilter::Random => 9,
			};
		}

		public static function index(int $index): CatalogFilter {
			return match($index) {
				1 => CatalogFilter::RecentlyUploaded,
				2 => CatalogFilter::RecentlyUpdated,
				3 => CatalogFilter::OldestUploaded,
				4 => CatalogFilter::OldestUpdated,
				5 => CatalogFilter::MostSold,
				6 => CatalogFilter::MostFavourited,
				7 => CatalogFilter::MostPopular,
				8 => CatalogFilter::MostVisited,
				9 => CatalogFilter::Random,
			};
		}

		public function getSQL(): string {
			return match($this) {
				CatalogFilter::RecentlyUploaded => "ORDER BY `created` DESC",
				CatalogFilter::RecentlyUpdated  => "ORDER BY `lastedited` DESC",
				CatalogFilter::OldestUploaded   => "ORDER BY `created` ASC",
				CatalogFilter::OldestUpdated    => "ORDER BY `lastedited` ASC",
				CatalogFilter::MostSold         => "ORDER BY `sales_count` DESC, `lastedited` DESC",
				CatalogFilter::MostFavourited   => "ORDER BY `favourites_count` DESC, `lastedited` DESC",
				CatalogFilter::MostPopular      => "ORDER BY `currently_playing_count` DESC, `visit_count` DESC, `lastedited` DESC",
				CatalogFilter::MostVisited      => "ORDER BY `visit_count` DESC",
				CatalogFilter::Random           => "ORDER BY `currently_playing_count` DESC, rand()",
			};
		}
	}

?>