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
	
	//whipe it up or down depending upon its current height
	if (formContainer.clientHeight) {
		formContainer.style.height = 0;
		this.classList.remove('active');
	} else {
		formContainer.style.height = formContainer.scrollHeight + 'px';
		this.classList.add('active');
	}
}


//global for the tasks collection
var tasks;

function getTasks(id) {
	//open up a HTTP request and tell it what to replace
	var request = new XMLHttpRequest();
	
	request.onload = function() {
		
		//the JSON for the tasks from the server
		tasks = JSON.parse(this.responseText);
		
		//get the elemend we append all the tasks into
		var taskBody = document.getElementById("task-body");
		
		appendTasksToElement(taskBody);
	}
	
	//post the URL along with the user's ID
	request.open("POST", "./CRUD/AJAX/getTasks.php?user_id="+id);
	request.send();
}

function createTask() {
	var request = new XMLHttpRequest();
	request.onload = function() {
		//get the task from the return value
		newTask = JSON.parse(this.responseText);
		
		//add it to the current list of tasks
		tasks.push(newTask);
		
		//get the task body element
		var taskBody = document.getElementById("task-body");
		
		//clear the task body of all elements before reloading it
		while (taskBody.firstChild)
			taskBody.removeChild(taskBody.lastChild);
		
		
		//taskBody.appendChild(document.createTextNode(this.responseText));
		//reload the element
		appendTasksToElement(taskBody);
	}
	
	//create post script
	const data = new FormData(document.forms['taskCreateForm']);
	
	request.open("POST", "./CRUD/create_task.php?input="+JSON.stringify(Object.fromEntries(data.entries())));
	request.send();
	
	//clear the form elements when done
	document.forms['taskCreateForm'].reset();
	document.getElementById('recurrence_container').style.display = 'none';
}

function appendTasksToElement(ElementToAppendTo) {
	//foreach of the tasks
	for (var t in tasks) {
		//create a container for the individual task
		var taskContainer = document.createElement('div');
		taskContainer.setAttribute('class', 'task-container');
		taskContainer.setAttribute('id', 'task_'+tasks[t].id);
		taskContainer.addEventListener('click', function() { markTask(this.id); });
		
		//create the hidden checkmark (for incomplete ones)
		var taskImg = document.createElement('img');
		taskImg.setAttribute('class', 'ic-hidden');
		taskImg.setAttribute('id', 'task_'+tasks[t].id+'checkImg');
		taskImg.setAttribute('src', 'img/taskcheck.png');
		
		//get rollover attributes
		if (tasks[t].rolls_over == true) {
			//create elements to contain the icon and tooltip
			var toolTipContainer = document.createElement('div');
			toolTipContainer.setAttribute('class', 'tooltip-container');
			
			var toolTipText = document.createElement('span');
			toolTipText.appendChild(document.createTextNode('This task rolls over to the next day if incomplete'));
			
			var taskRolloverImg = document.createElement('img');
			taskRolloverImg.setAttribute('class', 'ic-taskinfo');
			taskRolloverImg.setAttribute('src', 'img/rollover.png');
			
			//add elements in the appropriate order
			toolTipContainer.appendChild(taskRolloverImg);
			toolTipContainer.appendChild(toolTipText);
			taskContainer.appendChild(toolTipContainer);
		}
		
		//get recurrence attributes of the task and get ic images
		var RIvalue = parseInt(tasks[t].recurrence_interval, 10);
		if (RIvalue > 0) {
			//create the container
			var toolTipContainer = document.createElement('div');
			toolTipContainer.setAttribute('class', 'tooltip-container');
			
			//create the tooltip element
			var toolTipText = document.createElement('span');
			
			var taskRecurrenceType = document.createElement('img');
			taskRecurrenceType.setAttribute('class', 'ic-taskinfo');
			
			//for proper grammar
			var isSingular = (RIvalue == 1) ? true : false;
			
			switch (tasks[t].interval_unit) {
				case '0': //Daily
					//set the appropriate image
					taskRecurrenceType.setAttribute('src', 'img/daily.png');
					//set the tooltip text
					if (isSingular)
						toolTipText.appendChild(document.createTextNode('This task repeats every day'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' days'));
				break;
				case '1': //Weekly
					taskRecurrenceType.setAttribute('src', 'img/weekly.png');
					if (isSingular)
						toolTipText.appendChild(document.createTextNode('This task repeats every week'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' weeks'));
				break;
				case '2': //Monthly
					taskRecurrenceType.setAttribute('src', 'img/monthly.png');
					if (isSingular)
						toolTipText.appendChild(document.createTextNode('This task repeats every month'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' months'));
				break;
				case '3': //Yearly
					taskRecurrenceType.setAttribute('src', 'img/yearly.png');
					if (isSingular)
						toolTipText.appendChild(document.createTextNode('This task repeats every year'));
					else
						toolTipText.appendChild(document.createTextNode('This task repeats every ' + tasks[t].recurrence_interval + ' years'));
				break;
			}
			
			toolTipContainer.appendChild(taskRecurrenceType);
			toolTipContainer.appendChild(toolTipText);
			
			taskContainer.appendChild(toolTipContainer);
		}
		
		
		
		//create the title tag
		var taskTitle = document.createElement('h3');
		taskTitle.setAttribute('class', 'task-title');
		taskTitle.appendChild(document.createTextNode(tasks[t].title));
		
		//if the task is completed, it appears differently
		if (tasks[t].is_complete == '1') {
			taskImg.classList.add('ic-visible');
			taskContainer.classList.add('completed');
		}
		
		//append them both to the task container
		
		taskContainer.appendChild(taskImg);
		taskContainer.appendChild(taskTitle);
		
		//if this task has a description, then get it and append it to the task container too
		if (tasks[t].description != "") {
			var taskDescription = document.createElement('p');
			taskDescription.setAttribute('class', 'task-description');
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

function markTask(id) {
	//get this DOM element
	var thisTask = document.getElementById(id);
	var completed = thisTask.classList.contains('completed');
	
	//make i refer to the task in the collection doing a linear search
	for (var i = 0, length = tasks.length; i < length; i++) {
		if (tasks[i].id == id.slice(5))
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
			//javascript treats parsed json booleans as strings
			tasks[i].is_complete == '1' ? tasks[i].is_complete = '0' : tasks[i].is_complete = '1';
		} else { //state did not flip
			//error message?
		}
	}
	
	request.open("POST", "./CRUD/AJAX/mark_task.php?task_id="+id.slice(5));
	request.send();
}