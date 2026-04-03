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
			};
		}
	}

?>