import { getDataFromForm, showError } from './utils.js';

let loginForm = document.getElementById("login");
loginForm.addEventListener("submit", login);

async function login(event){
  event.preventDefault();
  try {
    let credentials = getDataFromForm(loginForm);
    const response = await fetch('../backend/login.php', {
      method: 'POST',
      body: credentials,
    });

    //throw new Error(`${response.status} ${response.statusText}`);
    if (!response.ok) throw new Error(`Check your credentials`);

    if(response.redirected){
      window.location.href = response.url;
    }

  }catch (error){
    showError(error.message);
  }
}
