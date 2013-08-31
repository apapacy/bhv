requirejs.config({
  waitSeconds: 240,
   baseUrl: 'bhv/vendors',
   paths: {
     bhv: '..',
     app: '../../test',
     cms: '../../cms'
   },
   urlArgs: "bust=" + (new Date()).getTime(),
   map: {
     '*' : {'jquery': 'jquery-1.9.1'}
   }
});

requirejs(['bhv/widget/combobox/Combobox', 'domReady!'], function ( cmb ) {

combobox1 = new cmb({
  url: 'ci/index.php/cms/test/collection'
});

combobox1.input.set('serachValue', 'qwerty1');
combobox1.read();
/*combobox1.fetch( {
data:{name:'qwerty'},

success:function(model,xhr,options){alert("1"+JSON.stringify(xhr))},
error:function(model,xhr,options){alert(2)}
} 
);*/

});



