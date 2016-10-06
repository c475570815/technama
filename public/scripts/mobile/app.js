$(function () {
  'use strict';

  //picker
  $(document).on("pageInit", "#page-picker", function(e, id, page) {
    

  });

  $("#picker").picker({
      toolbarTemplate: '<header class="bar bar-nav">\
       <button class="button button-link pull-right close-picker">\
      确定\
      </button>\
      <h1 class="title">标题</h1>\
      </header>',
      cols: [
        {
          textAlign: 'center',
          values: ['优良', '一般', '差', '很差'],
          cssClass: 'picker-items-col-normal'
        }
      ]
    });
  // 
  $.init();
});
