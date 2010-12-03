<?

require_once(realpath(dirname(__FILE__) . '/..') . '/framework-php/include/controller.php');
require_once(realpath(dirname(__FILE__)) . '/init.php');

class Controller extends NovoController
{
	protected $local_path;

	function __construct($parent = null)
	{
		parent::__construct($parent);

		$this->local_path = dirname(__FILE__);
		$this->title = '';

		$this->add_style('default');
		$this->add_script('jquery');
	}

	function view_path($view)
	{
		$ppath = parent::view_path($view);

		if (!is_array($ppath))
			$ppath = array($ppath);

		return array_merge(array($this->local_path . '/../views/' . $view), $ppath);
	}
}

?>
