// Reset arrow animations
function resetArrows(exception) {
    for (let i = 1; i <= 31; i++) {
        if (i != exception) {
            var element = document.getElementById('svg'+ i);

            $(element).css({
                'transform': 'rotate(0deg)'
            });
        }
    }
}