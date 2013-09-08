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
  parent: 'combo1',
  url: 'ci/index.php/cms/test/collection',
  urlRoot: 'ci/index.php/cms/test/model',
  keyName: 'kod',
  searchName: 'name',
  displayName: 'name'
});
alert('hi')
alert(combobox1.items.length)
//combobox1.items.add({id:162})
//combobox1.items.add({id:165})
//combobox1.input.set('searchValue', 'класс');
//combobox1.items.at(0).set('ttttttttttttt', "5555555555555555")
combobox1.read();
combobox1.setValue(1);
//combobox1.setValue(25);


/*combobox1.fetch( {
data:{name:'qwerty'},

success:function(model,xhr,options){alert("1"+JSON.stringify(xhr))},
error:function(model,xhr,options){alert(2)}
} 
);*/

});



