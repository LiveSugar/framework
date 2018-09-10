$(function($){

  //
  // form plugin
  //
  $.fn.form = function(callback){
    $this = this;
    if(typeof callback == 'function'){ // INPUT FORM
      
      // event input
      $this.off('input','input[type=text], textarea, [contenteditable="true"]').on('input','input[type=text], input[type=number], textarea, [contenteditable="true"]',function(e){
        let val = $(this).attr('contenteditable');
        if(val == 'true') { val = $(this).text(); }
        else { val = $(this).val(); }

        let name = $(this).attr('data-name');
        if(name == undefined) name = $(this).attr('name');

          var res = {
            name:name,
            value:val,
            type:'input',
            form:getAllDataOfForm(),
            element:this
          };
          callback(res);
      });
      // event submit
      $this.off('click','button, submit, [data-type="button"]').on('click','button, submit, [data-type="button"]',function(e){
        let name = $(this).attr('data-name');
        if(name == undefined) name = $(this).attr('name');
        var res = {
          name:name,
          type:'button',
          form:getAllDataOfForm(),
          element:this,
          target:e.target
        };
        callback(res);
      });
      // event checkbox
      $this.off('change','input[type=checkbox]').on('change','input[type=checkbox]',function(e){
        if($(this).is(':checked') == true) { var value = 'on'; }
        else { var value = 'off'; }
        var res = {
          name:$(this).attr('name'),
          value:value,
          type:'checkbox',
          form:getAllDataOfForm(),
          element:this
        };
        callback(res);
      });
      // event input type file
      $this.find($('input[type=file]')).each(function(i,e){
        $(e).off('change').on('change',function(e){
          var res = {
            name:$(e.target).attr('name'),
            value:e.target.files,
            type:'file',
            form:getAllDataOfForm()
          };
          callback(res);
        });
      });
      // get all data of form
      var getAllDataOfForm = function(){
        var dataForm = {};
        // input, textarea
        $this.find($('input[type=text],textarea,input[type=number]')).each(function(i,e){
            var name = $(e).attr('name');
            var value = $(e).val();
            dataForm[name] = value;
        });
        // checkbox
        $this.find($('input[type=checkbox]:checked,input[type=hidden]')).each(function(i,e){
          var name = $(e).attr('name');
          var value = $(e).val();
          if(dataForm[name] === undefined || typeof dataForm[name] == 'string'){
            dataForm[name] = [];
          }
          dataForm[name].push(value);
        });
        // return
        return dataForm;
      };
    } else if(typeof callback == 'object') { // FILL FORM
      let data = callback;
      if(data === null){
        $this.find($('input[type=text],textarea,input[type=number]')).each(function(i,e){
            var name = $(e).val('');
        });
        // checkbox
        $this.find($('input[type=checkbox]')).each(function(i,e){
          var name = $(e).prop('checked',false);
        });
      } else {
        for(var name in data){
          var value = data[name];
          var el = $this.find('[name="'+name+'"]');
          var type = el.attr('type');
          var tag = el.get(0).tagName;
          if(type == 'text' || tag == 'TEXTAREA') el.val(value);
          if(type == 'checkbox'){
            if(typeof value == 'object'){
              el.each(function(i,e){
                if($.inArray($(e).val(),value) > -1) $(e).attr('checked',true);
              });
            }
          }
        };
      };
    }
  }
});
