//default visibility
document.getElementById('recurrence_container').style.display = 'none';
//set the event listener for the checkboxy for this subform
document.getElementById('recurrence_checkbox').onchange = function () {
	//depending on the state of the checkboxy, show the subform
	if (this.checked == true) {
		document.getElementById('recurrence_container').style.display = 'block';
	} else {
		document.getElementById('recurrence_container').style.display = 'none';
	}
}

// prevent the default form submit behavior
document.forms['taskCreateForm'].addEventListener('submit', function(e) {
	e.preventDefault();
	createTask();
	
});


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
		taskImg.setAttribute('src', 'img/taskcheck.png');
		
		//create the title tag
		var taskTitle = document.createElement('h3');
		taskTitle.setAttribute('class', 'task-title');
		taskTitle.appendChild(document.createTextNode(tasks[t].title));
		
		//if the task is completed, it appears differently
		if (tasks[t].is_complete == true) {
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
		if (tasks[i].id === id.slice(5))
			break;
	}
	
	var request = new XMLHttpRequest();
	
	request.onload = function () {
		const response = this.responseText;
		
		if (response == 1) { //state flipped OK
			//flip the UI stuff
			if (completed) {
				thisTask.classList.remove('completed');
				var imgTag = thisTask.getElementsByTagName('img');
				imgTag[0].classList.remove('ic-visible');
			} else {
				thisTask.classList.add('completed');
				var imgTag = thisTask.getElementsByTagName('img');
				imgTag[0].classList.add('ic-visible');
			}
			
			//flip the state of the task in the collection
			tasks[i].is_complete = !tasks[i].is_complete;
		} else { //state did not flip
			//error message?
		}
	}
	
	request.open("POST", "./CRUD/AJAX/mark_task.php?task_id="+id.slice(5));
	request.send();
}