function getTasks(id) {
	//open up a HTTP request and tell it what to replace
	var request = new XMLHttpRequest();
	
	request.onload = function() {
		const tasks = JSON.parse(this.responseText);
		
		var newHTML = "";
		for (var t in tasks) {
			newHTML += getTaskHTML(tasks[t]);
		}
		document.getElementById("task-body").innerHTML = newHTML;
	}
	
	//post the URL along with the user's ID
	request.open("POST", "./CRUD/AJAX/getTasks.php?user_id="+id);
	request.send();
}

function getTaskHTML(task) {
	var tHTML = '<div class="task-container" id=' + task.id + '>';
	tHTML += '<img class="ic-hidden" src="img/taskcheck.png" />';
	tHTML += '<h3 class="task-title">' + task.title + '</h3>';
	tHTML += '<p class="task-description">' + task.description + '</p>';
	tHTML += '</div>';
	
	return tHTML;
}