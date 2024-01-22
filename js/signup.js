import {getDataFromForm, showError} from './utils.js';
import { myFetch } from './utils.js';

let registrationForm = document.getElementById("registration");
registrationForm.addEventListener("submit", registration);

async function registration(event){
  event.preventDefault();

  let personalData = getDataFromForm(registrationForm);
  
  let data = await myFetch('../backend/registration.php', personalData, 'POST');

  if(data.success){
    let title = document.getElementById("title");
    title.className = "alert alert-success";
    title.innerHTML = data.message;

    // Reindirizzamento dopo 2 secondi
    window.setTimeout(function(){
      window.location.href = 'signin.html';
    }, 2000);
  }else{
    showError(data.code, data.error); // Gestione degli errori
  }
}