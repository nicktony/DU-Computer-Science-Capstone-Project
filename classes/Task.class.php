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
	
	
	
	//HTML output for this task
	function getTaskHTML() {
		$html = <<<EOD
		
<div class="task-container" id="task{$this->id}">
	<img class="ic-hidden" src="img/taskcheck.png" />
	<h3 class="task-title">{$this->title}</h3>
	<p class="task-description">{$this->description}</p>
</div>
<!--
<b>task id</b> : {$this->id}<br>
<b>user id</b> : {$this->user_id}<br>
<b>title</b> : {$this->title}<br>
<b>descritpion</b> : {$this->description}<br>
<b>start date</b> : {$this->start_date}<br>
<b>recurrence interval</b> : {$this->recurrence_interval}<br>
<b>interval unit</b> : {$this->interval_unit}<br>
<b>priority</b> : {$this->priority}<br>
<b>is complete</b> : {$this->is_complete}<br>
<b>rolls over</b> : {$this->rolls_over}<br>
//-->
EOD;
		return $html;
	}
}
?>