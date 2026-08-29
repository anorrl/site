<?php

	namespace anorrl;

	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;

	class Page {

		private array $scripts = [];
		private array $stylesheets = [];
		private array $metas = [];
		private array $data_values = [];

		private string $icon = "/favicon.ico";
		private string $title;
		private string $internal_name;
		private int $lucky_number;
		private bool $bad_apple = false;
		private UserSettings $settings;

		private bool $ignore_anorrl = false;

		function __construct(string $title, string|null $internal_name = null, bool $ignore_anorrl = true) {
//			if(ClientDetector::IsAClient() && $title != "Login" && !str_starts_with($internal_name, "ide")) // assume studio
//				redirect("/develop/projects");

			$this->title = $title;
			if(!$internal_name)
				$this->internal_name = $title;
			else
				$this->internal_name = $internal_name;

			$this->ignore_anorrl = $ignore_anorrl;

			$this->lucky_number = rand(0, 100000);
			$this->bad_apple = $this->lucky_number > 6500 && $this->lucky_number < 6515;

			$this->addStylesheet("/css/base.css");
			$this->addScript("/js/core/jquery.js");
			$this->addScript("/js/core/jquery-modal.js");
			$this->addScript("/js/messagebox.js");
			$this->addStylesheet("https://unpkg.com/7.css/dist/7.scoped.css", false);

			if(SESSION) {
				$this->settings = SESSION->settings;
			}
			else {
				$this->settings = UserSettings::Get();
			}

			/*if(SESSION && SESSION->user && $_SERVER['SCRIPT_NAME'] != "/users/profile.php") {
				$user_id = SESSION->user->id;
				$time = time();

				$this->addStylesheet(SESSION->user->getTypedURL("css?t=$time"), false);
			}*/
		}

		function setTitle(string $title) {
			$this->title = $title;
		}

		function setInternalName(string $name) {
			$this->internal_name = $name;
		}

		function setIgnoreANORRL(bool $value) {
			$this->ignore_anorrl = $value;
		}

		function load3DScripts() {
			$this->addStylesheet("/css/thumbnail.css");
			$this->addScript("/js/3D/ThreeDeeThumbnails.js");
			$this->addScript("/js/3D/three.min.js");
			$this->addScript("/js/3D/MTLLoader.js");
			$this->addScript("/js/3D/OBJMTLLoader.js");
			$this->addScript("/js/3D/tween.js");
			$this->addScript("/js/3D/PolygonOrbitControls.js");
			$this->addScript("/js/thumbnails.js");
		}

		function clearAll() {
			$this->clearStylesheets();
			$this->clearScripts();
			$this->clearMetas();
		}

		function clearMetas() {
			$this->metas = [];
		}
		
		function clearScripts() {
			$this->scripts = [];
		}

		function clearStylesheets() {
			$this->stylesheets = [];
		}

		function addStylesheet(string $path, bool $public = true) {
			$this->addResource('stylesheet', $path, $public);
		}

		function addScript(string $path, bool $public = true) {
			$this->addResource('script', $path, $public);
		}

		function addMeta(string $type, string $path) {
			$this->metas[] = [
				"type" => "$type",
				"contents" => $path
			];
		}

		function addResource(string $type, string $path, bool $public = true) {
			$add_path = ($public ? "/public":"").$path;
			if(str_starts_with($add_path, "/") && !file_exists($_SERVER['DOCUMENT_ROOT'].$add_path) && $public) {
				error_log("Page of {$this->title} failed to load {$add_path}");
				return;
			}

			// if item is a resource on server, calculate hash and allow client to cache it
			// any new changes can immediately be pushed out without having to risk getting the same resource everytime
			if(str_starts_with($add_path, "/") && (str_ends_with($add_path, ".css") || str_ends_with($add_path, ".js")))
				$add_path .= "?hash=".md5(file_get_contents($_SERVER['DOCUMENT_ROOT'].$add_path));

			if($type == "script") {
				$this->scripts[] = $add_path;
			}
			if($type == "stylesheet") {
				$this->stylesheets[] = $add_path;
			}
		}

		function addValue(string $name, $value) {
			$this->data_values[] = [
				"name" => $name,
				"value" => $value
			];
		}

		function loadTemplate(string $template) {
			include $_SERVER['DOCUMENT_ROOT'] . "/private/templates/{$template}.php";
		}

		function loadHeader() {
			$this->loadTemplate("layouts/header");
		}

		function loadFooter() {
			$this->loadTemplate("layouts/footer");
		}

		function setIcon(string $icon) {
			$this->icon = $icon;
		}
	}
?>
