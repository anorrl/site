<?php

	namespace anorrl;

	use anorrl\UserSettings;
	use anorrl\utilities\ClientDetector;

	class Page {

		private array $scripts = [];
		private array $stylesheets = [];
		private array $metas = [];

		private string $title;
		private string $internal_name;
		private int $lucky_number;
		private bool $bad_apple = false;
		private UserSettings $settings;
		/**
		 * temporary
		 * @var bool
		 */
		private bool $cleared = false;

		/* wimpy stuff */
		private string $url;
		private string $name;
		private string $cover_art;
		private string $link = "";

		function __construct(string $title, string|null $internal_name = null) {
			if(ClientDetector::IsAClient() && $title != "Login" && !str_starts_with($internal_name, "ide")) // assume studio
				redirect("/ide/projects");

			$this->title = $title;
			if(!$internal_name)
				$this->internal_name = $title;
			else
				$this->internal_name = $internal_name;

			$this->lucky_number = rand(0, 100000);
			$this->bad_apple = $this->lucky_number > 6500 && $this->lucky_number < 6515;

			$this->addScript("/js/core/jquery.js");
			$this->addScript("/js/old/main.js?t=1776250887");
			$this->addScript("/wimpy/wimpy.js");
			$this->addStylesheet("/css/new/main.css?v=5");

			if(SESSION) {
				$this->settings = SESSION->settings;
			}
			else {
				$this->settings = UserSettings::Get();
			}

			if($this->settings->teto) {
				$this->addStylesheet("/css/new/teto.css?v=1");
			}

			if(SESSION && SESSION->user && $_SERVER['SCRIPT_NAME'] != "/users/profile.php") {
				$user_id = SESSION->user->id;
				$time = time();

				$this->addStylesheet("/users/$user_id/css?t=$time", false);
			}
		}

		function load3DScripts() {
			$this->addScript("/js/3D/ThumbnailView.js");
			$this->addScript("/js/3D/three.min.js");
			$this->addScript("/js/3D/MTLLoader.js");
			$this->addScript("/js/3D/OBJMTLLoader.js");
			$this->addScript("/js/3D/tween.js");
			$this->addScript("/js/3D/ThumbnailView.js");
			$this->addScript("/js/3D/PolygonOrbitControls.js");
		}

		function clearAll() {
			$this->cleared = true;
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
			if($type == "script") {
				$this->scripts[] = ($public ? "/public":"").$path;
			}
			if($type == "stylesheet") {
				$this->stylesheets[] = ($public ? "/public":"").$path;
			}
		}

		function loadTemplate(string $template) {
			include $_SERVER['DOCUMENT_ROOT'] . "/private/templates/{$template}.php";
		}

		function loadBasicHeader() {
			$this->loadTemplate("basicheader");
		}

		function loadBasicFooter() {
			$this->loadTemplate("basicfooter");
		}

		function loadHeader() {
			$this->loadTemplate("header");
		}

		function loadFooter() {
			$this->loadTemplate("footer");
		}

		function loadHeader2() {
			if(!$this->cleared)
				$this->clearAll();
			$this->addStylesheet("/css/base.css");
			$this->addScript("/js/core/jquery.js");
			$this->loadTemplate("layouts/header");
		}

		function loadFooter2() {
			$this->loadTemplate("layouts/footer");
		}

		function loadWimpy(string $url, string $name, string $cover_art, string $link = "") {
			$this->url = $url;
			$this->name = $name;
			$this->cover_art = $cover_art;
			$this->link = $link;
			$this->loadTemplate("wimpy");
		}

	}
?>
