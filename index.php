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
		$this->jobs = Command::jobs();
		$form = new Form($this, 'get');

		$toggled = explode(',', $form->toggled);
		$toggleitems = array();

		foreach ($toggled as $t)
		{
			$parts = explode(":", $t);

			$toggleitems[intval($parts[0])] = array_slice($parts, 1);
		}

		foreach ($this->jobs as $job)
		{
			if (array_key_exists($job->id, $toggleitems))
			{
				$job->toggled = $toggleitems[$job->id];
			}
		}

		$this->view();
	}
}

$controller = new Home();
$controller->run();

?>
