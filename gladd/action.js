$(function() {
  $(".button").click(function() {
    // validate and process form here
  });
});

  $(function() {
    $('.error').hide();
    $(".button").click(function() {
      // validate and process form here

      $('.error').hide();
  	  var fname = $("input#firstname").val();
  		if (fname == "") {
        $("label#fname_error").show();
        $("input#firstname").focus();
        return false;
      }
  		var lname = $("input#lastname").val();
  		if (lname == "") {
        $("label#lname_error").show();
        $("input#lastname").focus();
        return false;
      }
  		var gnum = $("input#gnum").val();
  		if (gnum == "") {
        $("label#gnum_error").show();
        $("input#gnum").focus();
        return false;
      }

    });
  });

// Submit form

  var dataString = 'fname='+ name + '&$lname=' + lname + '&$gnum=' + gnum;
alert (dataString);return false;
$.ajax({
  type: "POST",
  url: "action.php",
  data: dataString,
  success: function() {
    $('#contact_form').html("<div id='message'></div>");
    $('#message').html("<h2>Contact Form Submitted!</h2>")
    .append("<p>We will be in touch soon.</p>")
    .hide()
    .fadeIn(1500, function() {
      $('#message').append("<img id='checkmark' src='images/check.png' />");
    });
  }
});
return false;
