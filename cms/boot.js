requirejs.config({
  waitSeconds:240,
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

requirejs(['cms/cms', 'cms/modules/login/main' ,'domReady!'],
function (j,a,b) {
alert(a)
});



