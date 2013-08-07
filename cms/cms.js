define(['jquery', 'bhv/util', 'bhv/classes', 'underscore', 'backbone', 'domReady'],

function (jq, util, classes, us, bb) {
alert(1)
var cms = new Object()
cms.bhv = cms.util = util;
cms.classes = classes;
cms.jq = jq;
cms.us = _;
cms.bb = Backbone;
alert(cms);
return cms;
});



