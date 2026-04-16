<?php
	namespace anorrl\utilities;

	use anorrl\GSMJob;
	use anorrl\Place;

	class Arbiter {

		private string $location;
		private int $port;
		private string $token;
		private string $api_prefix = "/api/v1/";
		private int $timeout = 60;

		private static self|null $instance = null;

		public static function singleton(): self {
			if (!self::$instance)
				self::$instance = new Arbiter();

			return self::$instance;
		}

		private function __construct() {
			$config_location = explode(":", \CONFIG->arbiter->location->private);

			$this->location = $config_location[0];
			$this->port = intval($config_location[1]);
			$this->token = strtoupper(hash("sha256", \CONFIG->arbiter->token));
		}

		public function request(string $endpoint, array $data = []): Object|null {
			if(str_starts_with($endpoint, "/"))
				$endpoint = substr($endpoint, 1);
				
			$ch = curl_init("http://{$this->location}:{$this->port}{$this->api_prefix}$endpoint");

			error_log("http://{$this->location}:{$this->port}{$this->api_prefix}$endpoint");
			
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_HTTPHEADER => [
					"Authorization: Bearer {$this->token}",
					"Content-Type: application/json",
					"User-Agent: ANORRL/1.0"
				],
				CURLOPT_TIMEOUT => $this->timeout
			]);

			$response = curl_exec($ch);

			if ($response === false)
				return null;

			$json = json_decode($response);

			if (!$json)
				return null;

			return $json;
		}

		public function getAllJobs(int $size = 50): array {
			$jobs = $this->request("getalljobs?limit=$size");

			if(!$jobs)
				return [];

			return [];
		}

		public function getGSMJob(string $jobid): GSMJob|null {

			$job = $this->request("job/$jobid");

			if(!$job)
				return null;

			$place = Place::FromID(intval($job->PlaceId));

			if(!$place || ($place && $place->creator->isBanned()))
				return null;
			
			// cba!
			return new GSMJob(
				$job->JobId,
				$job->Port,
				$job->PlaceId,
				$job->Pid,
				new \DateTime(),
				new \DateTime(),
				$job->Alive
			);
		}

	}
?>