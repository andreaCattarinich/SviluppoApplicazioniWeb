import { getDataFromForm, showError } from './utils.js';

let registrationForm = document.getElementById("registration");
registrationForm.addEventListener("submit", registration);

async function registration(event){
  event.preventDefault();
  try {
    let userData = getDataFromForm(registrationForm);
    const response = await fetch('../backend/registration.php', {
      method: 'POST',
      body: userData,
    });


    //throw new Error(`${response.status} ${response.statusText}`);
    if (!response.ok) throw new Error(`${response.statusText}`);

    if(response.status === 201){
      let title = document.getElementById("title");
      title.className = "alert alert-success";
      title.innerHTML = response.statusText;

      // Reindirizzamento dopo 2 secondi
      window.setTimeout(function(){
        window.location.href = 'signin.html'; // TODO: 'signin.html?email=...'
      }, 2000);
    }

  }catch (error){
    showError(error.message)
  }
}