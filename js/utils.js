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

export function showError(code, error){
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
  const month = (currentDate.getMonth() < 10 ? '0' : '') + currentDate.getMonth() + 1;
  const day = (currentDate.getDate() < 10 ? '0' : '') + currentDate.getDate();
  const hours = (currentDate.getHours() < 10 ? '0' : '') + currentDate.getHours();
  const minutes = (currentDate.getMinutes() < 10 ? '0' : '') + currentDate.getMinutes();

  // TODO cancellare
  // const currentDate = new Date();
  // const year = currentDate.getFullYear();
  // const month = currentDate.getMonth() + 1;
  // const day = currentDate.getDate();
  // const hours = currentDate.getHours();
  // const minutes = (currentDate.getMinutes() < 10 ? '0' : '') + currentDate.getMinutes();
  
  return `${day}/${month}/${year} - ${hours}:${minutes}`;
}

/******* AJAX *******/
export async function myFetch(url, input, type){
  try{
    let response = await fetch(url, {
      method: type,
      body: input,
    });

    return await response.json();
  } catch (error){
    console.log('Errore: ', error);
  }
}