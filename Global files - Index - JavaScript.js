function goSomewhere(form) { 
  var hname = window.location.hostname;
  var index=form.langSelect.selectedIndex;
  var selectedValue = form.langSelect.options[index].value;
  if (selectedValue != "") {
    var path = selectedValue;
    var destination = hname+"/"+path;
  } else {
    var destination = hname+"/";
  }
  var loc = "http://"+destination;
  window.location.assign(loc);	
}
