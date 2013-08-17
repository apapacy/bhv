
requirejs.config({
  waitSeconds:120,
   baseUrl: '../bhv/vendors',
   paths: {
     bhv: '..',
     app: '../../test'
   },
   urlArgs: "bust=" + (new Date()).getTime(),
   map: {
     '*' : {'jquery': 'jquery-1.9.1'}
   }
});

requirejs(['jquery', 'bhv/util', 'bhv/classes', 'app/widget/ComboboxCennic1', 'domReady'],
function (jQ, util, classes, cmbc) {
window.bhv = util;
classes.create(cmbc, "combobox1", "comb1", 2,20,"cennic","kod","name","det")
classes.create(cmbc, "combobox2","comb2",19,20,"cennic","kod","name","det");
var cp = document.getElementById("combopane")
for (var i=3; i<20; i++){
  var i0
  i0=i
  var sp = document.createElement("SPAN")
  sp.id="id"+i
  cp.appendChild(sp)
  classes.create(cmbc, sp, undefined, i0,20,"cennic","kod","name","det");
}

});


/*requirejs.config({
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
	},
	urlArgs: "bust=" +  (new Date()).getTime(),
	map: {
		'*' : {'jquery': 'jquery-1.9.1'}
	}
});
requirejs(['jquery', 'domReady!'],
function   (jQ) {alert(require('jquery'))});
//alert(requirejs('jquery-1.9.1'))
// Start the main app logic.
requirejs(['jquery', 'bhv/util', 'bhv/classes', 'app/widget/ComboboxCennic', 'domReady!'],
function   (jQ, bhv,   classes, cmbc) {
    //jQuery, canvas and the app/sub module are all
    //loaded and can be used here now.


var N=0;
v=jQuery('#di p').map(
	function(){
//		alert(jQuery(this).html());
		jQuery("<span>123</span>").appendTo(jQuery(this)); 
		return true;
	}
);


combo1 = classes.create(cmbc, "combobox1", "comb1", 2, 10,
     "dbo.cennic", "kod", "name", "name")
//var combo1 = bhv.create("bhv.widget.Combobox","combobox1", "comb1", 2, 10,
//     "dbo.cennic", "kod", "name", "name")
var combo2 = classes.create(cmbc, "combobox2","comb2",19);

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
window.setTimeout(function(){classes.create(cmbc, sp, undefined, i0);},100)
	 //alert(i)
}()
}

});


*/