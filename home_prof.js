//js for home page professor edition

function initialization() {

	var code = localStorage.getItem('session_code');
	if(code == null){
		window.location.href = 'login.html';
	}
	document.getElementById('fname_welcome').innerHTML = localStorage.getItem('username');
	show_all_TAs();
}



function show_all_TAs(){
  var user = localStorage.getItem('username');
        $.get('home_list_users.php?username='+user).done(function(result) {
                result = result.slice(0, -1);
                tastring = result.split("~");
                for(var i = 0; i <  tastring.length; i++){
                        ta_1 = tastring[i].split("'");//this is an array
						if (ta_1 == "") {
							break;
						}

                        var table = document.getElementById("ta_table");
                        var row = table.insertRow(-1);

                        var delete_button = document.createElement("BUTTON");
                        delete_button.id = ta_1[0];
                        delete_button.className = "delete_button";
                        delete_button.innerHTML = "&times;";
                        var bt = row.insertCell(0);
                        bt.appendChild(delete_button);

                        var username = row.insertCell(1);
                        var firstname = row.insertCell(2);
                        var lastname = row.insertCell(3);
                        var email = row.insertCell(4);
                        var accounttype = row.insertCell(5);
                        username.innerHTML = ta_1[0];
                        firstname.innerHTML = ta_1[1];
                        lastname.innerHTML = ta_1[2];
                        email.innerHTML = ta_1[3];
                        accounttype.innerHTML = ta_1[4];

                        //only inserts the necessary departments
                        if (typeof ta_1[5] != "undefined" & typeof ta_1[6] == "undefined"){
                                var dept1 = row.insertCell(6);
                                dept1.innerHTML = ta_1[5];
                        }
                        else if (typeof ta_1[6] != "undefined"){
                                var departments = ta_1[5] + ", " + ta_1[6];
                                var dept1 = row.insertCell(6);
                                dept1.innerHTML = departments;
                        }
                        else {var dept = row.insertCell(6);}
                 }                                                                                                                                                     })
                                                                                                                                                                   }

function invite_ta(){
	var email = prompt("Please enter the TA's email:", "TA email");
	var account_type = "ta";
	if (email == null || email == "") {
		//alert( "User cancelled the prompt.");
		return false;
	} else {
		var serializedData = '&email='+email+'&account_type='+account_type; //serialized data to send to post

		$.ajax({
			url: "invite.php",
			type: "POST",
			data: serializedData,
			success: function(data)
			{
				var text = "The TA with email "+email+" has permission code "+data+". Please email this code to the TA with instructions to create an account.";
				alert(text); //alert the returned code and the email
			}
		});
                }

}



$(document).ready(function() {
    $("#ta_table").on("click",".delete_button", function (event) {
        var id = this.id;
        var row_node = this;

    if (confirm("Would you like to delete " + event.target.id + "?") == false) {
	//pressed cancel
	return false;	
    } else {
	//clicked OK so we delete user
        $.ajax({
            url: 'delete_user.php',
            type: 'POST',
            data: {username: id},
            success: function(result) {
                alert("Account deleted successfully.");
                var row = row_node.parentNode.parentNode;
                row.parentNode.removeChild(row);
                console.log(row_node.parentNode.parentNode);
                console.log(result);
             }
     });
}
    });
});


function logout(){
	localStorage.removeItem("session_code");
	window.location.href = 'login.html';
}
