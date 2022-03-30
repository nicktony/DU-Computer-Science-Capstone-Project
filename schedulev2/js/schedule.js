var currentMonth;
var table;
var startDate;
var endDate;
var todayDate;

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
	
	//AJAX stuff goes here
	//get a JSON object that bundles all the tasks that occur from startDate to endDate
	//use when that arrives, use the data to populate a new calendar created using JS
	CreateCalendar();
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

function CreateCalendar() {
	let titleRow = table.firstChild;
	while (table.firstChild)
		table.removeChild(table.lastChild);
	table.appendChild(titleRow);
	
	console.log(todayDate);
	
	while (startDate <= endDate) {
		let weekRow = document.createElement('tr');
		for (let day = 0; day < 7; day++) {
			let weekDay = document.createElement('td');
			
			if (startDate.toISOString() == todayDate.toISOString()) {
				weekDay.classList.add('currentday');
			}
			else {
				weekDay.classList.add('selectedday');
			}
			
			weekDay.innerHTML = `
				<div class='dayicon'>
					<div class='daylogoactive'>
						<div class='option-linking'>
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
				
				//no fetching from server here, just append text nodes with task descriptions in a box below the table
			});
				
			/*
				foreach task fetched from the server for this day
				let t = document.createElement('div');
				t.classList.add('embeddedtask-text');
				t.appendChild(document.createTextNode(    THE TASK TITLE    ));
				weekDay.appendChild(t);
			*/
			
			
			weekRow.appendChild(weekDay);
			startDate.setDate(startDate.getDate() + 1);
		}
		table.appendChild(weekRow);
	}
}



