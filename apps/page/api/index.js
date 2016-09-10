$(function(){
  $('#methods').on('change',function(e){
    var json = $(this).val();
    var json = JSON.parse(json);
    $('#method').html('<span class="label label-primary">api://'+json.name+'</span> <span  class="label label-default">'+json.params.join(', ')+'</span><hr>');
    // Create Form
    $('#form').empty();
    $('#result').empty();
    $('#form').append('<form class="form-horizontal"></form>');
    if(json.params.length > 0){
      for(i in json.params){
        var field = json.params[i];
        $('#form').find('form').append(
          '<div class="form-group">'+
            '<label for="'+field+'" class="col-sm-2 control-label">'+field+'</label>'+
            '<div class="col-sm-10">'+
              '<input type="text" class="form-control" id="'+field+'" name="'+field+'" placeholder="'+field+'">'+
            '</div>'+
          '</div>'
        );
      }
    }
    $('#form').find('form').append('<div class="form-group"><div class="col-sm-offset-2 col-sm-10"><button type="button" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-triangle-right"></span></button></div></div>');
    // Send form
    $('#form').off().on('click','form button',function(){
      var form = $(this).parent().parent().parent();
      var data = {};
      form.find('input').each(function(i,e){
        var fieldName = $(e).attr('name');
        var fieldValue = $(e).val();
        data[fieldName] = fieldValue; 
      });
      apps.name(json.name).data(data).exec(function(res){
        console.log(res);
        $('#result').text(JSON.stringify(res," ",2));
      });
    });
  });
});
