window.apps = {
  url: '/',
  varName:false,
  varData:false,
  varFile:false,
  varView:false,
  name: function(name){
    this.varName = name; 
    return this;
  },
  file: function(file,callback){
    var blob = new Blob([file], {type : 'application/octet-stream'});
    var xhr = new XMLHttpRequest();
    xhr.open('POST', this.url, true);
    xhr.setRequestHeader('Accept', 'api[file]://'+this.varName);
    xhr.responseType = 'json';
    xhr.onload = function(e) {
      if (this.status == 200) {
        var list = this.response;
        callback(list);
      }
    }
    xhr.send(blob);
  },
  data: function(data,callback){
    var xhr = new XMLHttpRequest();
    var blob = new Blob([JSON.stringify(data)], {type : 'application/json'});
    xhr.open('POST', this.url, true);
    xhr.setRequestHeader('Accept', 'api://'+this.varName);
    xhr.responseType = 'json';
    xhr.onload = function(e) {
      if (this.status == 200) {
        callback(this.response);
      }
    }
    xhr.send(blob);
    return this;
  },
  view: function(callback){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', this.url, true);
    xhr.setRequestHeader('Accept', 'view://'+this.varName);
    xhr.responseType = 'json';
    xhr.onload = function(e) {
      if (this.status == 200) {
        // Add Script
        var jsId = 'addviewscript';
        var check = document.getElementById(jsId);
        if(check !== null){
          check.parentNode.removeChild(check);
        }
        var script = document.createElement('script');
        script.id = jsId;
        script.type = "text\/javascript";
        script.text = this.response.js;
        document.getElementsByTagName('head')[0].appendChild(script);
        callback(this.response.html);
      }
    }
    xhr.send();
    return this;
  }
}
