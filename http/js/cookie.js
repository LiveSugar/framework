let cookie = {
  set:function(name,value,hours){
    let exdate=new Date(); 
    let endH=exdate.getHours()+hours; 
    exdate.setHours(hours); 
    document.cookie=name+ "=" +escape(value)+((hours==null) ? "" : ";expires="+exdate.toGMTString()+"; path=/;");
  },
  get:function(name){
    let start = document.cookie;
    start = start.split("; ");
    for(let key in start){
      let check = start[key].split("=");
      if(check[0] == name) {
        return unescape(check[1]);
      }
    }
  }
};
