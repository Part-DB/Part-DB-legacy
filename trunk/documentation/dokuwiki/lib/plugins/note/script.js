/* Add Note buttons to the toolbar */
/* from http://wiki.splitbrain.org/wiki:tips:toolbarbutton */

/* Disabled because this does not allow to select a text and turn it into a note like the type:format does
var notes_arr = new Array(); // key = insertion string , value = icon filename.
notes_arr['<note></note>\\n']='tb_note.png';
notes_arr['<note tip></note>\\n']='tb_tip.png';
notes_arr['<note important></note>\\n']='tb_important.png';
notes_arr['<note warning></note>\\n']='tb_warning.png';

toolbar[toolbar.length] = {"type":"picker",
                           "title":"Notes",
                           "icon":"../../plugins/note/images/tb_note.png",
                           "key":"",
                           "list": notes_arr,
                           "icobase":"../plugins/note/images"};
*/
 
if(toolbar){ 
    toolbar[toolbar.length] = {"type":"format", "title":"note", "key":"", 
                               "icon":"../../plugins/note/images/tb_note.png", 
                               "open":"<note>", "close":"</note>"
                              }; 
    toolbar[toolbar.length] = {"type":"format", "title":"tip", "key":"", 
                               "icon":"../../plugins/note/images/tb_tip.png", 
                               "open":"<note tip>", "close":"</note>"
                              }; 
    toolbar[toolbar.length] = {"type":"format", "title":"important", "key":"", 
                               "icon":"../../plugins/note/images/tb_important.png", 
                               "open":"<note important>", "close":"</note>"
                              }; 
    toolbar[toolbar.length] = {"type":"format", "title":"warning", "key":"", 
                               "icon":"../../plugins/note/images/tb_warning.png", 
                               "open":"<note warning>", "close":"</note>"
                              }; 
}
