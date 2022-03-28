$('input[type="checkbox"]').on('change', function(e) {
  if($(this).prop('checked'))
  {
      $(this).next().val(1);
  } else {
      $(this).next().val(0);
  }
});