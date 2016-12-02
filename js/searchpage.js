var clicked = false;

function checkCheckbox()
{
	console.log('entrou no checkCheckbox');
	var flag = false;
	var searchbar = document.getElementById('searchbar');
    var tag = document.getElementById('tag');
    var title = document.getElementById('title');
    var category = document.getElementById('category');
    if(!tag.checked && !title.checked && !category.checked)
    {
    	$("#speech").fadeIn();
        clicked = true;
        
        setTimeout(
            function() 
            {
                clicked = false;
                $("#speech").fadeOut();
            }, 3000);
    }
    else
    {
        $("#speech").fadeOut();
        flag = true;
    }
    
    return flag;
}

function buttonClick(coddeviation, line)
{
	var xhttp = new XMLHttpRequest();
	var params = "coddeviation=" + coddeviation;
	xhttp.open("POST", "UserDeviation.php", true);
	xhttp.onreadystatechange = function() 
	{
		if (this.readyState == 4 && this.status == 200) 
		{
			if(this.responseText == "true")
			{
				document.getElementById('button' + line).innerHTML = "Remover dos favoritos.";
			}
			else
			{
				alert('Deu um erro. :(');
			}
		}
	};
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.setRequestHeader("Content-length", params.length);
	xhttp.setRequestHeader("Connection", "close");
	xhttp.send(params);
}