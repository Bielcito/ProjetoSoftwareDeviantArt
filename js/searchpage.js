/*global $*/

var clicked = false;

function checkCheckbox()
{
    var tag = document.getElementById('tag');
    var title = document.getElementById('title');
    var category = document.getElementById('category');
    if(!tag.checked && !title.checked && !category.checked)
    {
        if(clicked)
        {
            console.log("hehe");
            return false;
        }
        
        $("#speech").fadeIn();
        clicked = true;
        
        setTimeout(
            function() 
            {
                clicked = false;
                $("#speech").fadeOut();
            }, 3000);
        return false;
    }
    else
    {
        $("#speech").fadeOut();
        return true;
    }
}