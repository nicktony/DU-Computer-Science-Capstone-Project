/*
	TaskDataCorrector(t)
		t: the task to parse
		Description:
			Data sent through HTTP is always parsed as a string. This method
		is specifically for converting data types in the task class back to the
		types that they are supposed to be. This makes the processing easier in
		other parts of the application.
		
			Data is passed by reference so there is no need for a return value.
*/
function TaskDataCorrector(t) {
	
	//don't attempt anything if t is null
	if (t == null)
		return;
	
	//parse and replace values
	t.id = parseInt(t.id);
	t.user_id = parseInt(t.user_id, 10);
	t.interval_unit = parseInt(t.interval_unit, 10);
	t.recurrence_interval = parseInt(t.recurrence_interval, 10);
	t.priority = parseInt(t.priority, 10);
	t.is_complete = (t.is_complete == '1') ? true : false;
	t.rolls_over = (t.rolls_over == '1') ? true : false;
}

/* 
	TaskStandardSort(a, b)
		a, b: the tasks to be sorted
		Description:
			Sorts tasks by completion and then by priority for incompletion only.
		The sort function in javascript already checks for null values, so no need
		to do that here.
 */
function TaskStandardSort(a, b) {
	if (!a.is_complete && !b.is_complete)
	{
		if (a.priority == b.priority)
			return 0; //doesn't matter
		else if (a.priority > b.priority)
			return 1; //a is after b
		else
			return -1; //b is after a
	} else if (!a.is_complete) { //a is before b
		return -1;
	} else if (!b.is_complete) { //b is after a
		return 1;
	} else { //both are complete
		return 0; //order doesn't matter
	}
}