<?php 
spl_autoload_register();
error_reporting(0);

use App\Task;

$task = new Task;
if (!empty($_GET) && isset($_GET["API"])) {
	if (isset($_GET['action'])) {
		if ($_GET['action'] == "create") {
			if (isset($_GET['sip']) && isset($_GET['account']) && isset($_GET['balance'])) {
				echo $task->insert();
			}
			else {
				echo "Enter sip, account and balance";
			}
		}

		if ($_GET['action'] == "delete") {
			if (isset($_GET['id'])) {
				echo $task->del($_GET['id']);
			}
			else {
				echo "What do you want delete?";
			}
		}

		if ($_GET['action'] == "change") {
			if (isset($_GET['id'])) {
				echo $task->update_db($_GET['id']);
			}
			else {
				echo "Enter id";
			}
		}
		if ($_GET['action'] == "sample") {
			if (isset($_GET['balance']) && ($_GET['balance'] == "positiv" || $_GET['balance'] == "negativ")) {
				echo $task->read_for_balance();
			}
			else {
				echo "Enter state balance: negativ or positiv";
			}
		}
	}
	else {
		echo $task->read_to_db();
	}
}
else {
	echo "{Error: Enter API }";
}
		
?>
