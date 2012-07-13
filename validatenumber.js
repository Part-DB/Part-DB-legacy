// see:
// http://stackoverflow.com/questions/469357/html-text-input-allow-only-numeric-input

function validateNumber(evt) 
{
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    if ((key < 48 || key > 57) && !(key == 8 || key == 9 || key == 13 || key == 37 || key == 39 || key == 44 || key == 46) )
    {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
