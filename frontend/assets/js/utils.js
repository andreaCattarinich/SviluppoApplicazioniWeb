import {FOOTER, NAVBAR_ADMIN, NAVBAR_VIEWER} from "./constants.js";

/************** INCLUDE ******************/
export async function includeNavbar(navbarPath){
  const response = await fetch(navbarPath);
  document.getElementById('navbar').innerHTML = await response.text();
}

export async function includeFooter(footerPath){
  const response = await fetch(footerPath);
  document.getElementById('footer').innerHTML = await response.text();
}
/************** FUNCTIONS ******************/
export function getDataFromForm(nameForm){
  const formData = new FormData(nameForm);
  const data = new URLSearchParams();

  formData.forEach((value, key) =>{
    data.append(key, value);
  });

  // Aggiungo il controllo del remember me (quando è presente)
  let rememberMe = document.getElementById("rememberMe");
  if(rememberMe){
    data.append("rememberMe", rememberMe.checked);
  }
  return data;
}

export function handleError(error){
  // If I want to manage codes error, use code parameter
  let title = document.getElementById("title");
  let subtitle = document.getElementById("subtitle");  

  title.className = "alert alert-danger";
  title.innerHTML = error;
  
  subtitle.innerHTML = "Please retry";
}

export function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2)
    return parts.pop().split(';').shift();
}

function parseJwt (token) {
  let base64Url = token.split('.')[1];
  let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  let jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function (c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));

  return JSON.parse(jsonPayload);
}

export function isLogged(){
  if(!getCookie('auth-token'))
    window.location.href = 'signin.html';
}

export function isAdmin(){
  let token = getCookie('auth-token');
  let payload = parseJwt(token);
  return payload['role'] === 'Admin';
}

export async function startPage() {
  isLogged();
  if (isAdmin())
    await includeNavbar(NAVBAR_ADMIN);
  else
    await includeNavbar(NAVBAR_VIEWER);

  await includeFooter(FOOTER);
}