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

export function showError(error, code = null){
  // If I want to manage codes error, use code parameter
  let title = document.getElementById("title");
  let subtitle = document.getElementById("subtitle");  

  title.className = "alert alert-danger";
  title.innerHTML = error;
  
  subtitle.innerHTML = "Please retry";
}

export function getCurrentData(){
  const currentDate = new Date();
  const year = currentDate.getFullYear();
  const month = (currentDate.getMonth() < 10 ? '0' : '') + (currentDate.getMonth() + 1);
  const day = (currentDate.getDate() < 10 ? '0' : '') + currentDate.getDate();
  const hours = (currentDate.getHours() < 10 ? '0' : '') + currentDate.getHours();
  const minutes = (currentDate.getMinutes() < 10 ? '0' : '') + currentDate.getMinutes();

  return `${day}/${month}/${year} - ${hours}:${minutes}`;
}

/******* AJAX *******/
export async function myFetch(url, input, method){
  try{
    let token = getCookie('auth-token');
    let requestConfig = {
      method: method,
      headers: {
        Authentication: `Bearer ${token}`,
      },
    };
    // AGGIUNGO IL BODY SE E' UN POST
    if (method === 'POST') {
      requestConfig.body = input;
    }

    let response = await fetch(url, requestConfig);

    if(response.status === 401){
      localStorage.removeItem('auth-token');
      window.location.href = 'signin.html';
    }

    /*
    if (response.status < 200 || response.status > 299) {
      localStorage.removeItem('auth-token');
      //window.location.href = 'signin.html';
    }
    */

    return await response.json();
  } catch (error){
    console.log('Errore: ', error);
  }
}
export function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2)
    return parts.pop().split(';').shift();
}
/*
export function getTokenFromLocalStorage(){
  let token = localStorage.getItem('auth-token');
  // TODO: controllare...
  if(!token){
    // window.location.href = 'signin.html';
    console.log('auth-token non trovato.');
  }
  return token;
}
*/