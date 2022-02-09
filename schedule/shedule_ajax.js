$(document).ready(function() {
alert("Hello World");
    const d = new Date();

    var day = d.getDay();
    var month = d.getMonth();
    var month_string = name = month[d.getMonth()];
    var year = d.getFullYear();

    $('#prev').click(function() {
        month = month - 1;
        if (month == 0) {
            month = 12;
            year = year - 1;
        }
        $('#calendar').load('/schedule.php', {
            selected_day: day,
            selected_month: month,
            selected_year: year
        });
    });

    $('#next').click(function() {
        month = month + 1;
        if (month == 13) {
            month = 1;
            year = year + 1;
        }
        $('#calendar').load('/schedule.php', {
            selected_day: day,
            selected_month: month,
            selected_year: year
        });
    });
});