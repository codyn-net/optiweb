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

	class JobProgress
	{
		public $best = array();
		public $mean = array();
		public $header = array();

		function __construct()
		{
		}
	}

	class Command
	{
		static $jobs = null;

		static function cmd($cmd, &$output)
		{
			return exec("/usr/bin/opticommand -m " . COMMAND_URI . " --raw --send " . escapeshellarg($cmd), $output);
		}

		static function progress($job)
		{
			self::cmd('progress ' . intval($job), $lines);

			$header = array_shift($lines);
			$ret = new JobProgress();
			$parts = explode("\t", $header);

			$names = array();

			for ($i = 0; $i < count($parts); $i += 2)
			{
				$ret->header[$parts[$i]] = intval($parts[$i + 1]);
				$ret->best[$parts[$i]] = array();
				$ret->mean[$parts[$i]] = array();

				$names[] = $parts[$i];
			}

			foreach ($lines as $line)
			{
				$parts = explode("\t", $line);

				// First value is the iteration
				$iteration = intval(array_shift($parts));

				for ($i = 0; $i < count($parts); $i += 2)
				{
					$idx = intval($i / 2);
					$head = $names[$idx];

					$ret->best[$head][] = floatval($parts[$i]);
					$ret->mean[$head][] = floatval($parts[$i + 1]);
				}
			}

			return $ret;
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
