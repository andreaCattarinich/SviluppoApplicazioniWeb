import { getDataFromForm, showError} from './utils.js';
import { myFetch } from './utils.js';

let loginForm = document.getElementById("login");
loginForm.addEventListener("submit", login);

async function login(event){
  event.preventDefault();

  let credentials = getDataFromForm(loginForm);
  // console.log(...credentials);

  let data = await myFetch('../backend/login.php', credentials, 'POST');

  if(data.success && data.token){
    console.log(data);
    localStorage.setItem('auth-token', data.token);

    window.location.href = 'profile.html'; // Reindirizzamento
  }else{
    showError(data.code, data.error); // Gestione degli errori
  }
}
