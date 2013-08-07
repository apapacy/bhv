
requirejs.config({
  waitSeconds:120,
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

requirejs(['jquery', 'bhv/util', 'bhv/classes', 'underscore', 'backbone', 'domReady'],
function (jQ, util, classes, us, bb) {
window.bhv = util;
alert(jQ)
alert(_)
alert(Backbone)
});



