<?php

	namespace anorrl;

	class Session {
		public User $user;
		public UserSettings $settings;

		function __construct(User|int $user) {
			if(is_int($user)) {
				$this->user = User::FromID($user);
			}
			else {
				$this->user = $user;
			}

			$this->settings = UserSettings::Get($this->user);
		}
	}

?>