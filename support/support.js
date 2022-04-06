
//get the form labels and inputs
var createNewUserForm = document.forms['support_form'];
var labels = Array.from(createNewUserForm.querySelectorAll('input ~ label'));
var inputs = Array.from(createNewUserForm.querySelectorAll('input'));

//for each input add an event listener that checks if the value of the textfield has changed
inputs.forEach(input => {
	input.addEventListener('change', e => {
		if (e.target.value.length == 0) { //if it's empty
			e.target.nextSibling.nextSibling.classList.remove('slide-up'); //let is slide down
		} else {
			//else keep it up
			e.target.nextSibling.nextSibling.classList.add('slide-up');
		}
	});
});