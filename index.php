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
		$this->title = 'Optimization Framework Log';
	}

	function action_index()
	{
		$this->jobs = Command::jobs();
		$form = new Form($this, 'get');

		$toggled = explode(',', $form->toggled);

		foreach ($this->jobs as $job)
		{
			if (in_array($job->id, $toggled))
			{
				$job->toggled = true;
			}
		}

		$this->view();
	}
}

$controller = new Home();
$controller->run();

?>
