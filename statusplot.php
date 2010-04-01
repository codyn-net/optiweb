<?php
	require_once('include/controller.php');
	require_once('data/sqlite.php');
	require_once('models/log.php');
	require_once('colors.php');

	class StatusPlot extends Controller
	{
		function __construct()
		{
			parent::__construct($parent);

			$this->set_day(intval(date('j')), intval(date('Y')));

			$this->add_filter('selection');
			$this->add_style('statusplot');
		}

		function _filter_selection()
		{
			if (isset($_GET['year']))
			{
				$year = intval($_GET['year']);
			}
			else
			{
				$year = intval(date('Y'));
			}

			if (isset($_GET['day']))
			{
				$day = intval($_GET['day']);

				if (isset($_GET['month']))
				{
					$time = mktime(0, 0, 0, intval($_GET['month']) + 1, $day + 1, $year);
					$day = date('z', $time);
				}

				$this->set_day($day, $year);
			}
			else if (isset($_GET['week']))
			{
				$this->set_week(intval($_GET['week']), $year);
			}
			else if (isset($_GET['month']))
			{
				$this->set_month(intval($_GET['month']), $year);
			}
			else
			{
				//$this->set_day(date('j'), $year);
				$this->set_day(date('z') - 4, $year);
			}

			return false;
		}

		function set_day($day_of_year, $year)
		{
			$this->start = strtotime('+' . $day_of_year . ' days', mktime(0, 0, 0, 1, 1, $year));
			$this->end = strtotime('+1 day', $this->start);

			$this->interval = 3600;
			$this->labels = array();

			for ($i = 0; $i < 24; $i++)
			{
				$this->labels[] = sprintf('%02d', $i);
			}
		}

		function set_week($week_of_year, $year)
		{
			$this->start = strtotime('+' . $week_of_year . ' weeks', mktime(0, 0, 0, 1, 1, $year));
			$this->end = strtotime('+1 week', $this->start);

			$this->interval = 6 * 3600;
			$this->labels = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		}

		function set_month($month_of_year, $year)
		{
			$this->start = strtotime('+' . $month_of_year . ' months', mktime(0, 0, 0, 1, 1, $year));
			$this->end = strtotime('+1 month', $this->start);

			$this->interval = 24 * 3600;
			$start = $this->start;
			$i = 1;

			$this->labels = array();

			while ($start < $this->end)
			{
				$this->labels[] = sprintf('%02d', $i++);
				$start = strtotime('+1 day', $start);
			}
		}

		function filter_interval()
		{
			$sql = 'SELECT ((`date` - ' . $this->start . ') / ' . $this->interval . ') AS idx, user, SUM(message) AS `time` FROM log WHERE (title = "user-status" OR title = "idle-time") AND idx >= 0 AND (`date` - ' . $this->start . ') >= 0 AND `date` < ' . $this->end . ' GROUP BY title, idx, user';

			$model = new LogModel();
			$items = $model->query($sql);

			$this->items = array();
			$users = array();
			$this->ymax = array();

			foreach ($items as $item)
			{
				$id = intval($item->idx);

				if (!isset($this->items[$id]))
				{
					$this->items[$id] = array();
				}

				$user = $item->user ? $item->user : 'idle';

				if (!in_array($user, $users))
				{
					$users[] = $user;
				}

				$f = floatval($item->time);

				$this->items[$id][$user] = $f;
				$this->ymax[$id] += $f;
			}

			if (count($this->ymax))
			{
				$this->ymax = max($this->ymax);
			}
			else
			{
				$this->ymax = 0;
			}

			ksort($this->items);

			foreach ($this->items as &$item)
			{
				foreach ($users as $user)
				{
					if (!array_key_exists($user, $item))
					{
						$item[$user] = 0;
					}
				}

				ksort($item);
			}

			$allusers = $model->query('SELECT DISTINCT(user) AS user FROM log');
			$all = array();

			foreach ($allusers as $user)
			{
				if ($user->user != '')
				{
					$all[] = $user->user;
				}
			}

			sort($all);
			$all[] = 'idle';
			$this->users = array();

			foreach ($users as $user)
			{
				$idx = array_search($user, $all);

				if ($user == 'idle')
				{
					$color = '#222';
				}
				else
				{
					$color = Colors::indexed($idx);
				}

				$this->users[$idx] = array('user' => $user, 'color' => $color);
			}

			if ($this->users)
			{
				ksort($this->users);
			}

			$zeros = array();

			foreach ($this->users as $user)
			{
				$zeros[$user['user']] = 0;
			}

			// Fill up data...
			for ($i = 0; $i < count($this->labels); $i++)
			{
				if (!$this->items[$i])
				{
					$this->items[$i] = $zeros;
				}
			}
		}

		function site($contents)
		{
			echo $contents;
		}

		function action_index()
		{
			$this->filter_interval();

			header('Content-type: application/json');
			$this->view();
		}
	}

	$controller = new StatusPlot();
	$controller->run();
?>
