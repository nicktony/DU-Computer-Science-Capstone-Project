//global variable to check rotation of svg
var rotated = false;

//arrow animation
$('#arrowSVG').click(function() {
    if (rotated == false) {
        $(this).css({
            'transform': 'rotate(90deg)'
        });
        rotated = true;
    } else if (rotated == true) {
        $(this).css({
            'transform': 'rotate(0deg)'
        });
        rotated = false;
    }
});