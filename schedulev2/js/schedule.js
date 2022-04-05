var currentMonth;
var table;
var startDate;
var endDate;
var todayDate;
var loadedTasks;

window.onload = function () {
	table = document.querySelector('table.internalcalendar tbody');
	
	InitializeMonthAndYear();
	
	UpdateMonthAndYear();
}

function InitializeMonthAndYear() {
	let date = new Date();
	date = date.toISOString().split('T');
	date = date[0].split('-');
	currentMonth = new Date(date[0], date[1] - 1, 1);
	todayDate = new Date(date[0], date[1]-1, date[2]);
}

function UpdateMonthAndYear() {
	let monthField = document.getElementById('monthstring');
	let yearField = document.getElementById('yearstring');
	
	while (monthField.firstChild)
		monthField.removeChild(monthField.lastChild);
	while (yearField.firstChild)
		yearField.removeChild(yearField.firstChild);
	
	document.getElementById('monthstring').appendChild(document.createTextNode(currentMonth.toLocaleString('default', { month: 'long' })));
	document.getElementById('yearstring').appendChild(document.createTextNode(currentMonth.getFullYear()));
	
	startDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
	startDate.setDate(1 - startDate.getDay());
	
	endDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
	endDate.setDate(endDate.getDate() + 6 - endDate.getDay());
	
	
	var request = new XMLHttpRequest();
	request.onload = function() {
		CreateCalendar(JSON.parse(this.responseText));
	}
	request.open('POST', './fetchTasks.php?start_date='+startDate.toISOString().split('T')[0]+'&end_date='+endDate.toISOString().split('T')[0]);
	request.send();
}

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

function CreateCalendar(tasks) {
	loadedTasks = tasks;
	
	let titleRow = table.firstChild;
	while (table.firstChild)
		table.removeChild(table.lastChild);
	table.appendChild(titleRow);
	
	while (startDate <= endDate) {
		let weekRow = document.createElement('tr');
		for (let day = 0; day < 7; day++) {
			let weekDay = document.createElement('td');
			
			if (startDate.toISOString().split('T')[0] == todayDate.toISOString().split('T')[0]) {
				weekDay.classList.add('currentday');
			}
			else {
				weekDay.classList.add('day');
			}
			
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
				
			let thing = weekDay.querySelector('.option-linking');
			thing.addEventListener('click', (e) => {
				document.querySelectorAll('[data-svg-arrows]').forEach(a => { a.style.transform = "rotate(0deg)"; });
				
				let svg = e.target.querySelector('svg');
				svg.style.transform = "rotate(90deg)";
				
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
					
				container.appendChild(descTable);
				window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })
			});
			
			let titleContainer = document.createElement('div');
			titleContainer.classList.add('embeddedtask');
			let filler = document.createElement('div');
			filler.classList.add('embeddedtask-text');
			titleContainer.appendChild(filler);
			if (startDate.toISOString().split('T')[0] in tasks)
				tasks[startDate.toISOString().split('T')[0]].forEach(t => {
					let taskTitleDiv = document.createElement('div');
					taskTitleDiv.classList.add('embeddedtask-text');
					taskTitleDiv.appendChild(document.createTextNode(t.title));
					titleContainer.appendChild(taskTitleDiv);
				});
			weekDay.appendChild(titleContainer);
			
			
			weekRow.appendChild(weekDay);
			startDate.setDate(startDate.getDate() + 1);
		}
		table.appendChild(weekRow);
	}
}