<?
require_once('include/controller.php');
require_once('include/command.php');
require_once('form.php');

class Home extends Controller
{
	function __construct()
	{
		parent::__construct();

		$this->_dbpath = LOG_PATH;
		$this->title = 'Optimization Inspector';

		$this->add_script('flot/jquery.flot');
	}

	function action_index()
	{
		$this->all_jobs = Command::jobs();
		$this->jobs = array();
		$form = new Form($this, 'get');
		$this->users = array();
		$this->user_selected = $form->user_filter;

		$toggled = explode(',', $form->toggled);
		$toggleitems = array();

		foreach ($toggled as $t)
		{
			$parts = explode(":", $t);

			$toggleitems[intval($parts[0])] = array_slice($parts, 1);
		}

		foreach ($this->all_jobs as $job)
		{
			if (!in_array($job->user, $this->users))
			{
				$this->users[] = $job->user;
			}

			if ($form->user_filter && $form->user_filter != $job->user)
			{
				continue;
			}

			if (array_key_exists($job->id, $toggleitems))
			{
				$job->toggled = $toggleitems[$job->id];
			}

			$this->jobs[] = $job;
		}

		sort(&$this->users);

		$this->view();
	}
}

$controller = new Home();
$controller->run();

?>
