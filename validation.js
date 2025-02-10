let form = document.querySelector('#loginForm');
//Ecouter la soumission du formulaire
form.addEventListener('submit', function(e){
e.preventDefault();
if(validEmail(form.Email) && validPassword(form.Password)){
form.submit();
}
});
//Ecouter la modification de l'email
form.Email.addEventListener('change', function(){
validEmail(this);
});
//Ecouter la modification de l'email
form.Password.addEventListener('change', function(){
validPassword(this);
});
// ***************** Valisation Email *********************************
const validEmail = function(inputEmail){
//création de la reg exp pour la validation de l"émail
3
let emailRegExp = new RegExp('^[a-zA-Z0-9.-_]+@{1}[a-zA-Z0-9.-_]+[.]{1}[az]{2,10}$','g');
let testEmail = emailRegExp.test(inputEmail.value);
let small = inputEmail.nextElementSibling;
if(testEmail){
small.innerHTML = 'Adresse valide';
small.classList.remove('text-danger');
small.classList.add('text-success');
return true;
}
else{
small.innerHTML = "S'il vous plait entrez votre mot de passe!";
small.classList.remove('text-success');
small.classList.add('text-danger');
return false;
}
};
// ***************** Valisation Password *********************************
const validPassword = function(inputPassword){
let valide = false;
let msg;
if (inputPassword.value.length < 3)
{
    msg= "S'il vous plaît entrez votre nom d'utilisateur";
}
else {
msg= "S'il vous plaît entrez votre nom d'utilisateur";
valide = true;
}
let small = inputPassword.nextElementSibling;
if(valide){
small.classList.remove('text-danger');
small.classList.add('text-success');
return true;
}
else{
4
small.innerHTML = msg;
small.classList.remove('text-success');
small.classList.add('text-danger');
return false;
}
}