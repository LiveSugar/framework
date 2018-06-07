// ({}).apps('nameMethod',function(response){});
// ({"nameVar":"dataVar"}).apps('nameMethod',function(response){});


//
// UPLOAD
// EXAMPLE: e.target.files.upload(function(file){});
//
FileList.prototype.upload = function(callback){
  for(let i=0; i<=this.length-1; i++){
    let file = this[i];
    let blob = new Blob([file], {type : "application/octet-stream"});
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/", true);
    xhr.responseType = "json";
    xhr.onload = function(e) {
      if (this.status == 200) {
        let res = this.response;
        callback(res);
      }
    };
    xhr.send(blob);
  }
};

//
// APPS
// EXAMPLE: (function(response){}).apps('nameApps',{'name':'value'});
//
Function.prototype.apps = function(name,data){
  let callback = this;
  let blob = new Blob([JSON.stringify(data)], {type : 'application/json'});
  let xhr = new XMLHttpRequest();
  xhr.open('POST', '/'+name, true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.responseType = 'json';
  xhr.onload = function(e) {
    if (this.status == 200) {
      callback(this.response);
    }
  };
  xhr.send(blob);

  return this;
};
