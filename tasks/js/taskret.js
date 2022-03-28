//default visibility
document.getElementById('recurrence_container').style.display = 'none';
//set the event listener for the checkboxy for this subform
document.getElementById('recurrence_checkbox').onchange = function () {
	//to adjust height of sliding container
	var formContainer = document.getElementById('createTaskFormContainer');
		
	//depending on the state of the checkboxy, show the subform
	if (this.checked == true) {
		document.getElementById('recurrence_container').style.display = 'block';
		formContainer.style.height = formContainer.scrollHeight + 'px';
	} else {
		document.getElementById('recurrence_container').style.display = 'none';
		formContainer.style.height = formContainer.scrollHeight + 'px';
<<<<<<< HEAD
	}
}

// prevent the default form submit behavior
document.forms['taskCreateForm'].addEventListener('submit', function(e) {
	e.preventDefault();
	createTask();
});

//event handler for the create task button
document.getElementById('createTaskButton').onclick = function() {
	//get the form container
	var formContainer = document.getElementById('createTaskFormContainer');
	
	//get the button for animations
	var arrowSVG = document.getElementById('arrowSVG');

	//whipe it up or down depending upon its current height
	if (formContainer.clientHeight) {
		formContainer.style.height = 0;
		//this.classList.remove('active'); //nick

		// Rotate arrow svg
		$(arrowSVG).css({
	        'transform': 'rotate(0deg)'
	    });
	} else {
		formContainer.style.height = formContainer.scrollHeight + 'px';
		//this.classList.add('active'); //nick

		// Rotate arrow svg
		$(arrowSVG).css({
	        'transform': 'rotate(90deg)'
	    });
	}
}

//event handlers for hovering over the create task button
document.getElementById('createTaskButton').onmouseover = function() {
	//get the button for animations
	var arrowSVG = document.getElementById('arrowSVG');

	// Rotate arrow svg
	$(arrowSVG).css({
        'filter': 'grayscale(0%) opacity(1)'
    });
}
document.getElementById('createTaskButton').onmouseout = function() {
	//get the button for animations
	var arrowSVG = document.getElementById('arrowSVG');

	// Rotate arrow svg
	$(arrowSVG).css({
        'filter': 'grayscale(100%) opacity(0.7)'
    });
}

=======
	}
}

// prevent the default form submit behavior
document.forms['taskCreateForm'].addEventListener('submit', function(e) {
	e.preventDefault();
	createTask();
});

//event handler for the create task button
document.getElementById('createTaskButton').onclick = function() {
	//get the form container
	var formContainer = document.getElementById('createTaskFormContainer');
	
	//get the button for animations
	var arrowSVG = document.getElementById('createTaskButton');

	//whipe it up or down depending upon its current height
	if (formContainer.clientHeight) {
		formContainer.style.height = 0;
		//this.classList.remove('active'); //nick
	} else {
		formContainer.style.height = formContainer.scrollHeight + 'px';
		//this.classList.add('active'); //nick
	}
}

//global variable to check rotation of svg
var rotated = false;

//arrow animation
$('#arrowSVG').click(function() {
    if (rotated == false) {
	    $(this).css({
	        'transform': 'rotate(90deg)'
	    });
	    rotated = true;
    } else if (rotated == true) {
    	$(this).css({
	        'transform': 'rotate(0deg)'
	    });
	    rotated = false;
    }
});

>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
//global for the tasks collection
var tasks;

function getTasks(id) {
	//open up a HTTP request and tell it what to replace
	var request = new XMLHttpRequest();
	
	request.onload = function() {
		
		//the JSON for the tasks from the server
		tasks = JSON.parse(this.responseText);
		for (var t in tasks)
			TaskDataCorrector(tasks[t]);
		
		updateTaskUI();
	}
	
	//post the URL along with the user's ID
	request.open("POST", "../tasks/CRUD/AJAX/getTasks.php?user_id="+id);
	request.send();
}

function createTask() {
	var request = new XMLHttpRequest();
	request.onload = function() {
		//get the task from the return value
		newTask = JSON.parse(this.responseText);
		
<<<<<<< HEAD
=======
		
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
		TaskDataCorrector(newTask);
		
		//add it to the frontend task queue if it's for today
		dateParts = newTask.start_date.split('-');
		var taskDate = new Date(dateParts[0], dateParts[1]-1, dateParts[2]); //create the date for the task
		
		if (taskDate - new Date(new Date().toDateString()) == 0) //date - today = 0 if date == today
			tasks.push(newTask);
		
		//sort and place tasks in the UI container
		updateTaskUI();
	}
	
	//create post script
	const data = new FormData(document.forms['taskCreateForm']);
	
	request.open("POST", "../tasks/CRUD/create_task.php?input="+JSON.stringify(Object.fromEntries(data.entries())));
	request.send();
	
	//clear the form elements when done
	document.forms['taskCreateForm'].reset();
	document.getElementById('recurrence_container').style.display = 'none';
}

//updates the UI according to the state of the tasks by sorting them
//and then replacing them
<<<<<<< HEAD
function updateTaskUI(sortingMethod = TaskStandardSort) {
	
	//sort based on the provided method
	tasks.sort(sortingMethod);
=======
function updateTaskUI() {
	
	//currently just the one sort
	//eventually a user specified sort will be used here
	//switch statement?
	tasks.sort(TaskStandardSort);
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
	
	//remove elements from the task body
	var taskBody = document.getElementById("task-body");
	
	while (taskBody.firstChild)
			taskBody.removeChild(taskBody.lastChild);
		
	//replace all elements
	appendTasksToElement(taskBody);
}

<<<<<<< HEAD
//this function has to be here to fix a bug in the transitionend event handler
function updateTaskUIEvent() {
	updateTaskUI();
}

=======
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
function appendTasksToElement(ElementToAppendTo) {
	//foreach of the tasks
	for (var t in tasks) {
		//create a container for the individual task
		var taskContainer = document.createElement('div');
		taskContainer.setAttribute('class', 'task-container');
		taskContainer.setAttribute('id', 'task_'+tasks[t].id);
<<<<<<< HEAD
		taskContainer.addEventListener('click', markTask);
		//taskContainer.addEventListener('click', function() { markTask(this.id); console.log(this); });
		taskContainer.setAttribute('data-task','');
=======
		taskContainer.addEventListener('click', function() { markTask(this.id); });
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
		
		//create the hidden checkmark (for incomplete ones)
		var taskImg = document.createElement('img');
		taskImg.setAttribute('class', 'ic-hidden');
<<<<<<< HEAD
		taskImg.setAttribute('id', 'task_' + tasks[t].id + 'checkImg');
		taskImg.setAttribute('src', 'img/taskcheck.png');
		//when the checkmark finishes its transition specified by the
		//css, then the UI should be updated/resorted
		taskImg.addEventListener('transitionend', updateTaskUIEvent);

=======
		taskImg.setAttribute('id', 'task_'+tasks[t].id+'checkImg');
		taskImg.setAttribute('src', 'img/taskcheck.png');
		//when the checkmark finishes its transition specified by the
		//css, then the UI should be updated/resorted
		taskImg.addEventListener('transitionend', updateTaskUI);
		
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
		//get rollover attributes
		if (tasks[t].rolls_over == true) {
			//create elements to contain the icon and tooltip
			var toolTipContainer = document.createElement('div');
			toolTipContainer.setAttribute('class', 'tooltip-container');
			
			var toolTipText = document.createElement('span');
<<<<<<< HEAD
			/*toolTipText.appendChild(document.createTextNode('Next day roll over if incomplete'));*/ // nick
=======
			//toolTipText.appendChild(document.createTextNode('This task rolls over to the next day if incomplete')); // nick
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
			
			var taskRolloverImg = document.createElement('img');
			taskRolloverImg.setAttribute('class', 'ic-taskinfo');
			taskRolloverImg.setAttribute('src', 'img/rollover.png');
			
			//add elements in the appropriate order
			toolTipContainer.appendChild(taskRolloverImg);
<<<<<<< HEAD
			/*toolTipContainer.appendChild(toolTipText);*/ //nick
=======
			toolTipContainer.appendChild(toolTipText);
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
			taskContainer.appendChild(toolTipContainer);
		}
		
		//get recurrence attributes of the task and get ic images
		if (tasks[t].recurrence_interval > 0) {
			//create the container
			var toolTipContainer = document.createElement('div');
			toolTipContainer.setAttribute('class', 'tooltip-container');
			
			//create the tooltip element
			var toolTipText = document.createElement('span');
			
			var taskRecurrenceType = document.createElement('img');
			taskRecurrenceType.setAttribute('class', 'ic-taskinfo');
			
			//for proper grammar
			var isSingular = (tasks[t].recurrence_interval == 1) ? true : false;
			
			switch (tasks[t].interval_unit) {
				case 0: //Daily
					//set the appropriate image
					taskRecurrenceType.setAttribute('src', 'img/daily.png');
					//set the tooltip text
					if (isSingular)
<<<<<<< HEAD
						toolTipText.appendChild(document.createTextNode('1'));
					else
						toolTipText.appendChild(document.createTextNode(tasks[t].recurrence_interval));
=======
						toolTipText.appendChild(document.createTextNode('This task repeats every day'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' days'));
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
				break;
				case 1: //Weekly
					taskRecurrenceType.setAttribute('src', 'img/weekly.png');
					if (isSingular)
<<<<<<< HEAD
						toolTipText.appendChild(document.createTextNode('1'));
					else
						toolTipText.appendChild(document.createTextNode(tasks[t].recurrence_interval));
=======
						toolTipText.appendChild(document.createTextNode('This task repeats every week'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' weeks'));
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
				break;
				case 2: //Monthly
					taskRecurrenceType.setAttribute('src', 'img/monthly.png');
					if (isSingular)
<<<<<<< HEAD
						toolTipText.appendChild(document.createTextNode('1'));
					else
						toolTipText.appendChild(document.createTextNode(tasks[t].recurrence_interval));
=======
						toolTipText.appendChild(document.createTextNode('This task repeats every month'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' months'));
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
				break;
				case 3: //Yearly
					taskRecurrenceType.setAttribute('src', 'img/yearly.png');
					if (isSingular)
<<<<<<< HEAD
						toolTipText.appendChild(document.createTextNode('1'));
					else
						toolTipText.appendChild(document.createTextNode(tasks[t].recurrence_interval));
=======
						toolTipText.appendChild(document.createTextNode('This task repeats every year'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' years'));
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
				break;
			}
			
			toolTipContainer.appendChild(taskRecurrenceType);
			toolTipContainer.appendChild(toolTipText);
<<<<<<< HEAD
=======
			
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
			taskContainer.appendChild(toolTipContainer);
		}
		
		
		
		//create the title tag
		var taskTitle = document.createElement('h3');
		taskTitle.setAttribute('class', 'task-title');
		taskTitle.appendChild(document.createTextNode(tasks[t].title));
		
		//if the task is completed, it appears differently
		if (tasks[t].is_complete) {
			taskImg.classList.add('ic-visible');
			taskContainer.classList.add('completed');
		}
		
		//append them both to the task container
		
		taskContainer.appendChild(taskImg);
		taskContainer.appendChild(taskTitle);
		
		//if this task has a description, then get it and append it to the task container too
		if (tasks[t].description != "") {
			var taskDescription = document.createElement('p');
			taskDescription.setAttribute('class', 'embedded-task-description');
			taskDescription.appendChild(document.createTextNode(tasks[t].description));
			
			taskContainer.appendChild(taskDescription);
		}
		
		//priority information
		var priorityContainer = document.createElement('div');
		priorityContainer.setAttribute('class', 'task-priority');
		priorityContainer.appendChild(document.createTextNode('Priority: ' + tasks[t].priority));
		taskContainer.appendChild(priorityContainer);
		
		//append the task container to the main task body
		ElementToAppendTo.appendChild(taskContainer);
		//move on to the next task
	}
}

function markTask(e) {
	var id = this.id;
	
	//get this DOM element
	var thisTask = document.getElementById(id);
	var completed = thisTask.classList.contains('completed');
	
	//make i refer to the task in the collection doing a linear search
	for (var i = 0, length = tasks.length; i < length; i++) {
<<<<<<< HEAD
		if (tasks[i].id == this.id.slice(5))
=======
		if (tasks[i].id == id.slice(5))
>>>>>>> 84968e3336806504266a8c307577a7de85c83f69
			break;
	}
	
	var request = new XMLHttpRequest();
	
	request.onload = function () {
		const response = this.responseText;
		
		if (response == 1) { //state flipped OK
			//flip the UI stuff
			var imgTag = document.getElementById(id+'checkImg');
			if (completed) {
				thisTask.classList.remove('completed');
				imgTag.classList.remove('ic-visible');
			} else {
				thisTask.classList.add('completed');
				imgTag.classList.add('ic-visible');
			}
			
			//flip the state of the task in the collection
			tasks[i].is_complete = !tasks[i].is_complete;
		} else { //state did not flip
			//error message?
		}
	}
	
	request.open("POST", "../tasks/CRUD/AJAX/mark_task.php?task_id="+id.slice(5));
	request.send();
}