<?php

	namespace anorrl;

	use anorrl\UserSettings;

	class Page {

		private array $scripts = [];
		private array $stylesheets = [];
		private array $metas = [];

		private string $title;
		private string $internal_name;
		private int $lucky_number;
		private bool $bad_apple = false;
		private UserSettings $settings;

		function __construct(string $title, string|null $internal_name = null) {
			$this->title = $title;
			if(!$internal_name)
				$this->internal_name = $title;
			else
				$this->internal_name = $internal_name;

			$this->lucky_number = rand(0, 100000);
			$this->bad_apple = $this->lucky_number > 6500 && $this->lucky_number < 6515;

			$this->addScript("/js/core/jquery.js");
			$this->addScript("/js/main.js?t=1776250887");
			$this->addStylesheet("/css/new/main.css?v=3");

			if(SESSION) {
				$this->settings = SESSION->settings;
			}
			else {
				$this->settings = UserSettings::Get();
			}

			if($this->settings->teto_enabled) {
				$this->addStylesheet("/css/new/teto.css?v=1");
			}

			if(SESSION && SESSION->user && $_SERVER['SCRIPT_NAME'] != "/users/profile.php") {
				$user_id = SESSION->user->id;
				$time = time();

				$this->addStylesheet("/users/$user_id/css?t=$time", false);
			}
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
	}
?>
