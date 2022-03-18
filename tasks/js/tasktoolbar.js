//get the reference to the toolbar
const toolbar = document.querySelector('.toolbar');

//reset the margin of the main body of the page based on the height of the toolbar
document.querySelector('.tasks').style.marginTop = toolbar.clientHeight + 5 + "px";

//reference to the tasks in the DOM
var domTasks;

//task deletion queue
var tasksToBeDeleted = new Array();

//track the deletion state
var deletionState = false;

//click event for all the buttons in the toolbar
toolbar.addEventListener('click', e => {
	if (e.target.matches('[data-button-delete]')) {
		//get all the tasks in the DOM
		domTasks = Array.from(document.querySelectorAll('[data-task]'));
		/* 
			When switching back to the normal state from the deletionState we
				- switch all task event handlers back to the marking event handlers
				- remove the deletion-indicator from each of the task divs
				- if any of the tasks were marked for deletion, we remove them from the tasks queue
				- if any of the tasks were marked for deletion, AJAX them away
		*/
		if (deletionState) {
			domTasks.forEach(t => {
				t.classList.remove('deletion-indicator');
				t.removeEventListener('click', deleteTaskClick);
				t.addEventListener('click', markTask);
			});
			
			//open up HTTP connection and use ajax to send the list of task ids to delete
			var request = new XMLHttpRequest();
			request.onload = function () {
				const response = this.responseText;
				
				//1 means all the tasks were successfully deleted
				if (response == 1) {
					//filter out the tasks from the frontend queue
					tasks = tasks.filter(obj => {
						let returnFlag = true;
						
						for (let i = 0; i < tasksToBeDeleted.length; i++) {
							if (tasksToBeDeleted[i] == obj.id) {
								returnFlag = false;
							}
						}
						
						if (returnFlag)
							return obj;
					});
					
					//update the UI
					updateTaskUI();
				} else {
					//anything else reported back means something went wrong.
					//There may be inconsistencies from here
					console.log("Consistency failure");
				}
			};
			request.open("POST", "../tasks/CRUD/AJAX/deleteTask.php?info="+JSON.stringify(tasksToBeDeleted));
			request.send();
			
			deletionState = false;
			
		/* 
			When switching to the deletion state from the normal state we
				- switch all task event handlers from marking to deletion
				- add the deletion-indicator to each of the task divs
		*/
		} else {
			domTasks.forEach(t => {
				t.classList.add('deletion-indicator');
				t.removeEventListener('click', markTask);
				t.addEventListener('click', deleteTaskClick);
			});
			deletionState = true;
			tasksToBeDeleted = [];
		}
		
	} else if (e.target.matches('[data-button-sort-standard]')) {
		updateTaskUI();
	} else if (e.target.matches('[data-button-sort-rollover]')) {
		updateTaskUI(TaskClusterByRollover);
	} else if (e.target.matches('[data-button-sort-interval]')) {
		updateTaskUI(TaskClusterByRepetitionInterval);
	} else {
		//toolbar clicked but no button was
	}
});

function deleteTaskClick(e) {
	//get the id of the task
	var taskId = this.id.slice(5);
	console.log(e.target);
	
	//add it to the list if it's not there, remove it if it is
	if (tasksToBeDeleted.includes(taskId))
	{
		e.target.classList.remove('marked');
		let idx = tasksToBeDeleted.indexOf(taskId);
		if (idx !== -1)
			tasksToBeDeleted.splice(idx, 1);
	} else {
		tasksToBeDeleted.push(taskId);
		e.target.classList.add('marked');
	}
}