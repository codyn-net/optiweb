<?php
	require_once('include/controller.php');
	require_once('include/command.php');

	class Progress extends Controller
	{
		function __construct()
		{
			parent::__construct($parent);
		}

		function site($contents)
		{
			echo $contents;
		}

		function action_index()
		{
			header('Content-type: application/json');

			$id = intval($_GET['id']);

			$this->progress = Command::progress($id);
			$this->view();
		}
	}

	$controller = new Progress();
	$controller->run();
?>
