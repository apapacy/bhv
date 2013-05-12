requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl: '../bhv/vendors',
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths: {
        bhv: '..',
		app: '../../test'
    }
});

// Start the main app logic.
requirejs(['jquery-1.9.1', 'bhv/util', 'bhv/classes', 'bhv/widget/Combobox','app/widget/ComboboxCennic'],
function   (jQuery, bhv,   classes, cmb,cmbc) {
    //jQuery, canvas and the app/sub module are all
    //loaded and can be used here now.
alert(1)
jQuery = $;

var N=0;
v=jQuery('#di p').map(
	function(){
//		alert(jQuery(this).html());
		jQuery("<span>123</span>").appendTo(jQuery(this)); 
		return true;
	}
);


var combo1 = classes.create(cmb, "combobox1", "comb1", 2, 10,
     "dbo.cennic", "kod", "name", "name")
//var combo1 = bhv.create("bhv.widget.Combobox","combobox1", "comb1", 2, 10,
//     "dbo.cennic", "kod", "name", "name")
var combo2 = bhv.create(cmbc, "combobox2","comb2",19);

var cp = document.getElementById("combopane")
function a(a0,b){alert(a.length), alert(arguments.length)}
//a(1)

for (i=3; i<20; i++){
void function(){	 
var i0
i0=i
var sp = document.createElement("SPAN")
sp.id="id"+i
cp.appendChild(sp)
//new bhv.Combobox(sp, undefined, i0, 10,     "dbo.cennic", "kod", "name", "name")
//function inner(){new bhv.Combobox(sp, undefined, i0, 10,     "dbo.cennic", "kod", "name", "name")}
window.setTimeout(function(){bhv.create(cmbc, sp, undefined, i0);},100)
	 //alert(i)
}()
}

});
