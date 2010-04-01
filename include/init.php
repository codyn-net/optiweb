<?
	set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/..') . PATH_SEPARATOR . dirname(__FILE__));
	
	ini_set('error_reporting', E_ALL & ~E_NOTICE);
	ini_set('display_errors', 'stdout');

	define('LOG_PATH', '/data/jesse/log/optimaster.db');
	define('COMMAND_URI', 'localhost');

	require_once('data/model.php');
	require_once('data/sqlite.php');

	function english_duration($diff, $always = true)
	{
		$parts = array(
			array(10, 1, 1, 'second', 'seconds'),
			array(60, 1, 5, 'second', 'seconds'),
			array(60 * 10, 60, 1, 'minute', 'minutes'),
			array(60 * 60, 60, 5, 'minute', 'minutes'),
			array(60 * 60 * 20, 60 * 60, 0.5, 'hour', 'hours')
		);

		for ($i = 0; $i < count($parts); $i++)
		{
			$part = $parts[$i];

			if ($diff < $part[0] || ($always && $i == count($parts) - 1))
			{
				$num = intval(floatval($diff) / ($part[1] * $part[2])) * $part[2];
				
				return $num . ' ' . ($num == 1 ? $part[3] : $part[4]);
			}
		}

		return null;
	}

	function english_date($date)
	{
		$diff = time() - $date;
		$duration = english_duration($diff, false);

		if ($duration !== null)
		{
			return $duration . ' ago';
		}

		return format_date($date);
	}
	
	function format_date($date)
	{
		return date('d M, H:i', $date);
	}

	function create_db()
	{
		static $db = null;

		if (!$db)
		{
			$db = new SQLiteDB(LOG_PATH);
		}

		return $db;
	}

	Model::set_db_creator(create_db);

?>
