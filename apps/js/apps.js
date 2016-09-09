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
  file: function(file){
    this.varFile = file;
    return this;
  },
  data: function(data){
    this.varData = data;
    return this;
  },
  view: function(path){
    this.varView = path;
    return this;
  },
  exec: function(callback){
    if(this.varData !== false){
      var blob = new Blob([JSON.stringify(this.varData)], {type : 'application/json'});
    }
    if(this.varFile !== false){
      var blob = new Blob([this.varFile], {type : 'application/octet-stream'});
    }
    var xhr = new XMLHttpRequest();
    xhr.open('POST', this.url, true);
    if(this.varData !== false){
      xhr.setRequestHeader('Accept', 'api://'+this.varName);
    }
    if(this.varFile !== false){
      xhr.setRequestHeader('Accept', 'api[file]://'+this.varName);
    }
    if(this.varView !== false){
      xhr.setRequestHeader('Accept', 'view://'+this.varView);
    }
    xhr.setRequestHeader('Content-Type', 'application/octet-stream');
    if(this.varData !== false){
      xhr.responseType = 'json';
    }
    if(this.varView !== false){
      xhr.responseType = 'json';
    }
    xhr.onload = function(e) {
      if (this.status == 200) {
        if(this.varView !== false){
          callback(this.response.html);
        } else {
          callback(this.response);
        }
      }
    };
    xhr.send(blob);
  }
}
