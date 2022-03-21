//get a reference to the change password field
const formBody = document.getElementById('form-body');
//reference to the toast message box
const toaster = document.getElementById('toaster');
//reference to the button that initiates the change password action
const passwordChanger = document.getElementById('passwordChanger');
passwordChanger.addEventListener('click', initiateChangePassword);


//create the fields for each step of the process
const cpField = document.createElement('input');
cpField.placeholder = "Enter your current password";
cpField.type = "password";
cpField.setAttribute('required', '');
cpField.pattern = "^.{5,20}$";
cpField.title = "Must be between 5 and 20 characters in length.";
cpField.classList.add('cp-input');
cpField.addEventListener('keyup', passwordConfirm); //they respond to the enter key

const npField = document.createElement('input');
npField.placeholder = "Enter a new password";
npField.type = "password";
npField.setAttribute('required', '');
npField.pattern = "^.{5,20}$";
npField.title = "Must be between 5 and 20 characters in length.";
npField.classList.add('cp-input');
npField.addEventListener('keyup', newPassword);

const cnpField = document.createElement('input');
cnpField.placeholder = "Confirm your new password";
cnpField.type = "password";
cnpField.setAttribute('required', '');
cnpField.pattern = "^.{5,20}$";
cnpField.title = "Must be between 5 and 20 characters in length.";
cnpField.classList.add('cp-input');
cnpField.addEventListener('keyup', confirmNewPassword);

//save the password for the confirmation step
var newPassword;

//keep track of the step we're on
var currentStep = 0;

//if the change password action has been initiated, prevent the enter key from submitting the
//edit profile page
const submitButton = document.getElementById('save');
submitButton.addEventListener('click', (e) => {
	if (currentStep > 0)
		e.preventDefault();
});


//this function displays error messages as a toast bar at the top of the screen
//it allows rapid messages in succession as well
function makeToast(message) {
	let toast = document.createElement('div');
	toast.classList.add('toast');
	toast.appendChild(document.createTextNode(message));
	toast.addEventListener('animationend', e => {
		toaster.removeChild(e.target);
	});
	
	toaster.appendChild(toast);
	toast.classList.add('show');
}

//Event: when the user presses the change password button
function initiateChangePassword() {
	//remove elements from the div that contains the button
	while(formBody.firstChild)
		formBody.removeChild(formBody.lastChild);
	//put the confirm password field there and bring it to focus
	formBody.appendChild(cpField);
	cpField.focus();
	cpField.classList.add('in');//animate
	
	//set the current step
	currentStep = 1;
}

//Event: user pressed the enter key after entering their current password
function passwordConfirm() {
	if (event.key === "Enter") {
		//validate the HTML field for their current password
		let valid = cpField.reportValidity();
		if (!valid) return;
		
		//do ajax to confirm password
		var request = new XMLHttpRequest();
		request.onload = function () {
			if (this.responseText == "1") {
				//if we're good, remove the current field and add the next one
				//after the transition
				cpField.classList.remove('in');
				cpField.classList.add('out');
				cpField.addEventListener('transitionend', transitionEnd);
					
				currentStep = 2;
			} else {
				//server says that wasn't their password
				makeToast("Incorrect Password");
				cpField.value = "";
			}
		};
		request.open('POST', './changePassword.php?old_password='+cpField.value);
		request.send();
	}
}

//Event: User entered their new password
function newPassword() {
	if (event.key === "Enter") {
		//validate it
		let valid = npField.reportValidity();
		if (!valid) return;
		
		//fade out that field and bring in the new one at the end of the transition
		npField.classList.remove('in');
		npField.classList.add('out');
		npField.addEventListener('transitionend', transitionEnd);
		
		//now we're on step three
		currentStep = 3;
	}
}

//Event: user entered their new password a second time
function confirmNewPassword() {
	if (event.key === "Enter") {
		//validate it
		let valid = cnpField.reportValidity();
		if (!valid) return;
		
		//if match with the old field send the new password to the server
		if (cnpField.value === npField.value) {
			//more ajax
			var request = new XMLHttpRequest();
			request.onload = function () {
				if (this.responseText == "1") {
					//server says change was good
					//go to final step
					cnpField.classList.remove('in');
					cnpField.classList.add('out');
					cnpField.addEventListener('transitionend', transitionEnd);
					currentStep = 4;
				} else {
					//server says something was wrong, go back to beginning
					makeToast("Something went wrong. Please try again later");
					currentStep = 0;
					resetChangePassword();
				}
			};
			request.open('POST', './changePassword.php?new_password='+cnpField.value);
			request.send();
			
		} else {
			//password fields didn't match, go back to step 2
			npField.value = "";
			cnpField.value = "";
			cnpField.classList.remove('in');
			cnpField.classList.add('out');
			cnpField.addEventListener('transitionend', transitionEnd);
			makeToast("Your passwords did not match");
			currentStep = 2;
		}
	}
}

//If we go back to the beginning, just wipe the form-body and re-place the button
function resetChangePassword() {
	while(formBody.firstChild)
		formBody.removeChild(formBody.lastChild);
	
	npField.value = ""; //clear fields too
	cnpField.value = "";
	cpField.value = "";
	
	formBody.appendChild(passwordChanger);
}

//fires at the end of all of the transitions, using the step counter
//to determine what fields to bring in next
function transitionEnd() {
	//remove elements from the form body
	while(formBody.firstChild)
		formBody.removeChild(formBody.lastChild);
	
	//depending on the current step, bring in a new field and give it focus
	switch (currentStep) {
		case 2:
			cpField.removeEventListener('transitionend', transitionEnd);
			formBody.appendChild(npField);
			npField.focus();
			npField.classList.remove('out');
			npField.classList.add('in');
			break;
		case 3:
			npField.removeEventListener('transitionend', transitionEnd);
			formBody.appendChild(cnpField);
			cnpField.focus();
			cnpField.classList.remove('out');
			cnpField.classList.add('in');
			break;
		case 4: //final step
			//tell the user everything is awesome
			cnpField.removeEventListener('transitionend', transitionEnd);
			formBody.innerHTML = "Password Changed<span style='color:green' class='material-icons'>done</span>";
			currentStep = 0;
			break;
	}
}