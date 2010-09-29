<?
require_once('include/controller.php');
require_once('include/command.php');
require_once('form.php');

class History extends Controller
{
	function __construct()
	{
		parent::__construct();

		$this->_dbpath = LOG_PATH;
		$this->title = 'Optimization Framework Log';
		$this->add_script('statusplot');
	}

	function month($idx)
	{
		static $months = array(
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		);

		return $months[$idx];
	}
}

$controller = new History();
$controller->run();

?>
