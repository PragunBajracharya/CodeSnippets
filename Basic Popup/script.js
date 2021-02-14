let button = document.querySelector("#contact-popup");
let popup  = document.querySelector(".popup");
let body   = document.querySelector("body")
button.addEventListener('click', function() {
  popup.style.display = "block";
  body.classList.add("noscrolljs");
});

popup.addEventListener('click', function(event) {
  if(event.target.className === "popup" || event.target.className === "close-popup"){
   this.style.display = "none";
   body.classList.remove("noscrolljs");
  }
});


function getCookie(cname) {
 var name = cname + "=";
 var decodedCookie = decodeURIComponent(document.cookie);
 var ca = decodedCookie.split(';');
 for(var i = 0; i <ca.length; i++) {
  var c = ca[i];
  while (c.charAt(0) == ' ') {
   c = c.substring(1);
  }
  if (c.indexOf(name) == 0) {
   return c.substring(name.length, c.length);
  }
 }
 return "";
}

 let d = new Date();
 d.setDate(d.getDate() + 7);
 let cookie = getCookie("popup");
 if (!cookie) {
   setTimeout(function(){
  popup.style.display = "block";
  document.cookie = "popup=true; expires=" + d;
 }, 5000);
 }