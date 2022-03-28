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
	GetNextTaskOccurrence(t)
		t: the task to operate on
		returns: a date object describing the date of the next
			time the task will occur. Null if task doesn't recur.
 */
function GetNextTaskOccurrence(t) {
	if (t.recurrence_interval == 0)
		return null;
	else {
		//get the current date
		var next = new Date();
		//we add to the current date depending upon the interval unit
		switch (t.interval_unit) {
			case 0: //days
				next.setDate(next.getDate() + t.recurrence_interval);
				break;
			case 1: //weeks
				next.setDate(next.getDate() + (t.recurrence_interval * 7));
				break;
			case 2: //months
				next.setMonth(next.getMonth() + t.recurrence_interval);
				break;
			case 3: //years
				next.setFullYear(next.getFullYear() + t.recurrence_interval);
				break;
			default:
				return null;
		}
		//return the date
		return next;
	}
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

/* 
	TaskClusterByRollover(a, b)
		a, b: the tasks to be sorted
		Description:
			Sorts the tasks by completion first, then groups them such that
		the tasks that roll over are on the top. Finally, the two clusters
		(rollover and not rollover) are sorted by priority.

 */
function TaskClusterByRollover(a, b) {
	if (!a.is_complete && b.is_complete)
		return -1; //a before b
	else if (!b.is_complete && a.is_complete)
		return 1; //b before a
	else { //both are either incomplete or complete
		if (a.rolls_over && !b.rolls_over)
			return -1;
		else if (b.rolls_over && !a.rolls_over)
			return 1;
		else { //both roll over
			if (a.priority > b.priority)
				return 1;
			else if (b.priority > a.priority)
				return -1;
			else
				return 0;
		}
	}
}

/* 
	TaskClusterByRepetitionInterval(a, b)
		a, b: tasks to be sorted
		Description:
			Sorts the task by completion then by repetition interval.
			(ie. 1 week < 3 weeks < 2 months < 9 weeks < 1 year)
 */
function TaskClusterByRepetitionInterval(a, b) {
	if (!a.is_complete && b.is_complete)
		return -1; //a before b
	else if (!b.is_complete && a.is_complete)
		return 1; //b before a
	else { //both are either incomplete or complete
		if (a.recurrence_interval == 0 && b.recurrence_interval == 0)
			return 0;
		else if (a.recurrence_interval == 0 && b.recurrence_interval != 0)
			return 1;
		else if (b.recurrence_interval == 0 && a.recurrence_interval != 0)
			return -1;
		else { // they both have a recurrence interval
			//get the dates of their next occurnce
			var aDate = GetNextTaskOccurrence(a);
			var bDate = GetNextTaskOccurrence(b);
			
			//chech nullness
			if (aDate == null || bDate == null) {
				//error handling here
				return 0;
			}
			
			//subtract them and return
			//if equal it's 0, if a > b then positive, if a < b then negative
			return aDate - bDate;
		}
	}
}























