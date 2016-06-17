// Stop form action

$('#gladd').submit(function(){
 return false;
});

// Execute php in background

$('#submit').click(function(){
	$.post(
		$('#gladd').attr('action'),
		$('#gladd :input').serializeArray(),
		function(result){
			$('#result').html(result);
		}
	);
  location.reload('true');
});

//document.getElementById("gladd").reset();
$('#gladd').trigger('reset'); // To reset form fields
