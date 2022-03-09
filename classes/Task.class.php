<?php
/*
	Task
	A container for the properties and information of tasks in Taskless.
	Use TaskFactory to instantiate Tasks with various configurations
*/
class Task {
	
	//properties directly from the database
	//these shouldn't be edited directly unless in the factory class.
	//use methods below to set these values to maintain a consistent database
	public $id;
	public $user_id;
	public $title;
	public $description;
	public $start_date;
	public $recurrence_interval;
	public $interval_unit;
	public $priority;
	public $is_complete;
	public $rolls_over;
	
	

}
?>