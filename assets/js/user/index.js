
 $noSIRET =document.getElementById("inputNoSIRET");
 $name =document.getElementById("inputName");
 $username =document.getElementById("inputUsername");
 $email =document.getElementById("inputEmail");
 $phone =document.getElementById("inputPhone");
 $imagefile = document.getElementById("btnImageFile");


if($noSIRET){
    $noSIRET.disabled = true;
}
if($name){
    $name.disabled = true;
}
if($username){
    $username.disabled = true;
}
if($email){
    $email.disabled = true;
}
if($phone){
    $phone.disabled = true;
}
if($imagefile){
    $imagefile.hidden = false;
}

