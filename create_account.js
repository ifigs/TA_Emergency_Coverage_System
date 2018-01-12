function makeSalt() {
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < 10; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}

//create account
function initialization(){
	if ($(window).width() < 572) {

                $('form').css('margin', 'auto');
                $('form').css('width', 'auto');
                $('#submit_button').css('margin-right', '');
                $('#submit_button').css('margin', 'auto');
                $('#submit_button').css('width', 'auto');
                $('.error').css('display', 'flex');
                $('.error').css('margin-top', '0px');
                $('.error').css('margin-left', '0px');
	}

}





/*
	if($(window).width() < 393) {
		$('submit_button').css('width', 'auto');
		$('submit_button').css('margin', 'auto');
		$('form').css('margin-right', '');
		$('.error').css('margin-top', '-1px');
		$('.error').css('margin-left', '0px');
		
	}
	if ($(window).width() < 572) {
		$('form').css('margin-right', '0px');
		$('submit_button').css('margin', 'auto');
		$('submit_button').css('width', 'auto');
		$('.error').css('display', 'flex');
		$('.error').css('margin-top', '-1px');
		$('.error').css('margin-left', '0px');
	}
*/

