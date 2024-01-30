import {getCookie, getDataFromForm, handleError, includeFooter, includeNavbar} from './utils.js';
import {FOOTER, NAVBAR_HOMEPAGE} from "./constants.js";

if(getCookie('auth-token'))
  window.location.href = 'blog.html';

let loginForm = document.getElementById("login");
loginForm.addEventListener("submit", login);

try{
  await includeNavbar(NAVBAR_HOMEPAGE);
  await includeFooter(FOOTER);

  const urlParams = new URLSearchParams(window.location.search);
  const myParam = urlParams.get('success');
  if(myParam){
    let title = document.getElementById("title");
    title.className = "alert alert-success";
    title.innerHTML = 'Registration Successfully';
    document.getElementById('email').value = myParam;
  }
} catch (error){
  console.log(error);
}

async function login(event){
  event.preventDefault();
  try {
    let credentials = getDataFromForm(loginForm);
    const response = await fetch('../backend/login.php', {
      method: 'POST',
      body: credentials,
    });

    //const data = await response.json();
    if (!response.ok){
      if(response.status === 500) throw new Error(`Service Temporarily Unavailable`);
      else throw new Error(`Check your credentials`);
    }

    window.location.href = 'profile.html';

  }catch (error){
    handleError(error.message);
  }
}
