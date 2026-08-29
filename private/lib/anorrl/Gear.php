<?php

	namespace anorrl;

	use anorrl\Asset;
	use anorrl\Database;
	use anorrl\enums\GearType;

	class Gear extends Asset {

		public GearType $type;
		
		public static function FromID(?int $id): Badge|null {
			if(!is_int($id))
				return null;
			
			$row = Database::singleton()->run(
				"SELECT * FROM `gears` WHERE `id` = :id LIMIT 1",
				[ ":id" => $id ]
			)->fetchObject();

			return $row ? new self($row) : null;
		}

		function __construct(int|object $rowdata) {
			parent::__construct($rowdata->id);
			$this->type = GearType::index($rowdata->type);
		}
	}

?>