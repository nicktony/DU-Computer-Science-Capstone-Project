//some variables to hold state information
var currentMonth;
var table;
var startDate;
var endDate;
var todayDate;

//holds all the loaded tasks retrieved from the server on load of a new month
var loadedTasks;

//on initial load of the screen
window.onload = function () {
	//create the table
	table = document.querySelector('table.internalcalendar tbody');
	
	//initialize
	InitializeMonthAndYear();
	
	//update
	UpdateMonthAndYear();
}

//initializes the values for the currentMonth and todayDate globals
function InitializeMonthAndYear() {
	let date = new Date();
	date = date.toISOString().split('T');
	date = date[0].split('-');
	currentMonth = new Date(date[0], date[1] - 1, 1);
	todayDate = new Date(date[0], date[1]-1, date[2]);
}

//clears the month header and sets it to whatever the currentMonth is
//updates the startDate and endDate for the table so tasks can be fetched from the server
function UpdateMonthAndYear() {
	//get referneces to the fields
	let monthField = document.getElementById('monthstring');
	let yearField = document.getElementById('yearstring');
	
	//clear all the children out of the fields
	while (monthField.firstChild)
		monthField.removeChild(monthField.lastChild);
	while (yearField.firstChild)
		yearField.removeChild(yearField.firstChild);
	
	//create new children and put them in place
	document.getElementById('monthstring').appendChild(document.createTextNode(currentMonth.toLocaleString('default', { month: 'long' })));
	document.getElementById('yearstring').appendChild(document.createTextNode(currentMonth.getFullYear()));
	
	//update the startDate and endDate for the new selected month
	startDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
	startDate.setDate(1 - startDate.getDay());
	
	endDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
	endDate.setDate(endDate.getDate() + 6 - endDate.getDay());
	
	
	//perform an AJAX request to fetch tasks for that date range from the server
	var request = new XMLHttpRequest();
	request.onload = function() {
		//on fetch of tasks, create the calendar with them
		CreateCalendar(JSON.parse(this.responseText));
	}
	request.open('POST', './fetchTasks.php?start_date='+startDate.toISOString().split('T')[0]+'&end_date='+endDate.toISOString().split('T')[0]);
	request.send();
}

//some event listeners for the month changing buttons
document.getElementById('next').addEventListener('click', () => {
	currentMonth.setMonth(currentMonth.getMonth() + 1);
	UpdateMonthAndYear();
});

document.getElementById('prev').addEventListener('click', () => {
	currentMonth.setMonth(currentMonth.getMonth() - 1);
	UpdateMonthAndYear();
});

document.getElementById('reset').addEventListener('click', () => {
	InitializeMonthAndYear();
	UpdateMonthAndYear();
});


//Create the calendar with the given tasks
function CreateCalendar(tasks) {
	//set the tasks to the global variable
	//(lets them be used in anonymous functions)
	loadedTasks = tasks;
	
	//remove all children from the table
	//make sure to put the title row back in
	let titleRow = table.firstChild;
	while (table.firstChild)
		table.removeChild(table.lastChild);
	table.appendChild(titleRow);
	
	//for every date in the date range
	while (startDate <= endDate) {
		//create a row for the week
		let weekRow = document.createElement('tr');
		
		//for every day in the week
		for (let day = 0; day < 7; day++) {
			//create a cell in the table for the day
			let weekDay = document.createElement('td');
			
			//sets the class of the cell differently if it's the current day
			if (startDate.toISOString().split('T')[0] == todayDate.toISOString().split('T')[0]) {
				weekDay.classList.add('currentday');
			}
			else {
				weekDay.classList.add('day');
			}
			
			//HTML for the rest of the cell
			weekDay.innerHTML = `
				<div class='dayicon'>
					<div class='daylogoactive'>
						<div class='option-linking' data-tasks-date="${startDate.toISOString().split('T')[0]}">
							<span class='linking-text daylogo-text'>${startDate.getDate()}</span>
							<svg
								aria-hidden='true'
								focusable='false'
								data-svg-arrows
								data-prefix='fad'
								data-icon='angle-double-right'
								role='img'
								xmlns='http://www.w3.org/2000/svg'
								viewBox='0 0 448 512'
								class='svg-inline--fa fa-angle-double-right fa-w-14 fa-5x'
								id='svg$j'
								style='transition: all .5s ease'>
								<g class='fa-group'>
									<path
										fill='currentColor'
										d='M224 273L88.37 409a23.78 23.78 0 0 1-33.8 0L32 386.36a23.94 23.94 0 0 1 0-33.89l96.13-96.37L32 159.73a23.94 23.94 0 0 1 0-33.89l22.44-22.79a23.78 23.78 0 0 1 33.8 0L223.88 239a23.94 23.94 0 0 1 .1 34z'
										class='fa-secondary'>
									</path>
									<path
										fill='currentColor'
										d='M415.89 273L280.34 409a23.77 23.77 0 0 1-33.79 0L224 386.26a23.94 23.94 0 0 1 0-33.89L320.11 256l-96-96.47a23.94 23.94 0 0 1 0-33.89l22.52-22.59a23.77 23.77 0 0 1 33.79 0L416 239a24 24 0 0 1-.11 34z'
										class='fa-third'>
									</path>
								</g>
							</svg>
						</div>
					</div>
				</div>`;
				
			//get a reference to the thing
			let thing = weekDay.querySelector('.option-linking');
			//give a click handler
			thing.addEventListener('click', (e) => {
				//rotate all arrows back to their starting position
				document.querySelectorAll('[data-svg-arrows]').forEach(a => { a.style.transform = "rotate(0deg)"; });
				
				
				//rotate this arrow to the selected position
				let svg = e.target.querySelector('svg');
				svg.style.transform = "rotate(90deg)";
				
				//write the descriptions for each task in the description box below the table
				let container = document.getElementById('taskdescriptions');
				
				while(container.firstChild)
					container.removeChild(container.lastChild);
				
				let descTable = document.createElement('table');
				descTable.classList.add('tasks');
				
				
				if (e.target.dataset.tasksDate in loadedTasks)
					loadedTasks[e.target.dataset.tasksDate].forEach( t => {
						let tr = document.createElement('tr');
						let taskTd = document.createElement('td');

						taskTd.appendChild(document.createTextNode(t.title+": "+t.description));
						tr.appendChild(taskTd);
						descTable.appendChild(tr);
					});
					
				//add description table to the end of the table and scroll to it
				container.appendChild(descTable);
				window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
			});//end event handler
			
			//Create a container for all the task titles for this day
			let titleContainer = document.createElement('div');
			titleContainer.classList.add('embeddedtask');
			let filler = document.createElement('div'); //needs to have at least one element in it
			filler.classList.add('embeddedtask-text');
			titleContainer.appendChild(filler);
			
			//for every task on that day, put its title in the container
			if (startDate.toISOString().split('T')[0] in tasks)
				tasks[startDate.toISOString().split('T')[0]].forEach(t => {
					let taskTitleDiv = document.createElement('div');
					taskTitleDiv.classList.add('embeddedtask-text');
					taskTitleDiv.appendChild(document.createTextNode(t.title));
					titleContainer.appendChild(taskTitleDiv);
				});
			//put the container in the cell
			weekDay.appendChild(titleContainer);
			
			//put the cell in the row of the table
			weekRow.appendChild(weekDay);
			
			//increment the day by one before continuing
			startDate.setDate(startDate.getDate() + 1);
		}
		//when every day has been added to this week, append the week row to the table before moving on to the next one
		table.appendChild(weekRow);
	}
}