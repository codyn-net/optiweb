<?php
	class Job
	{
		public $properties = array();

		function __construct()
		{
		}

		function __get($name)
		{
			return $this->properties[$name];
		}

		function __set($name, $value)
		{
			$this->properties[$name] = $value;
		}

		function __isset($name)
		{
			return isset($this->properties[$name]);
		}

		function __unset($name)
		{
			unset($this->properties[$name]);
		}
	}

	class Command
	{
		static $jobs = null;

		static function cmd($cmd, &$output)
		{
			return exec("/usr/bin/opticommand -c " . COMMAND_URI . " --raw --send " . escapeshellarg($cmd), $output);
		}

		static function jobs()
		{
			if (self::$jobs !== NULL)
			{
				return self::$jobs;
			}

			self::cmd('list', $lines);

			self::$jobs = array();

			foreach ($lines as $line)
			{
				$parts = explode("\t", $line);
				$properties = array();

				self::cmd('info ' . $parts[0], $properties);

				$job = new Job();
				$header = array_shift($properties);

				$parts = explode("\t", $header);

				$job->id = $parts[0];
				$job->name = $parts[1];
				$job->user = $parts[2];

				foreach ($properties as $property)
				{
					$parts = explode("\t", $property);
					$job->{$parts[0]} = $parts[1];
				}

				self::$jobs[] = $job;
			}

			return self::$jobs;
		}
	}
?>
