import { getCookie, getDataFromForm } from "./utils.js";

let firstname, lastname, email, instagram;

document.addEventListener('DOMContentLoaded', showProfile);
document.addEventListener('click', clickButton);

async function showProfile(event){
  event.preventDefault();
  try{
    const token = getCookie('auth-token');
    const response = await fetch('../backend/show_profile.php', {
      method: 'GET',
      headers: {
        Authentication: `Bearer ${token}`,
      },
    });

    if (!response.ok) throw new Error(`${response.status} ${response.statusText}`);

    const data = await response.json();

    firstname = data.firstname;
    lastname = data.lastname;
    email = data.email;
    instagram = data.instagram;
    loadData(); // TODO: passare data come parametro e gestire tutto dalla funzione
  } catch (error){
    window.location.href = 'signin.html';
  }
}

async function clickButton(event){
  if(event.target.tagName === 'BUTTON'){
    let button = event.target;
    if(button.textContent === 'Edit profile'){
      loadEditMode();
    }else if(button.textContent === 'Update'){
      await updateProfile();
    }else if(button.textContent === 'Cancel'){
      loadLandingPage();
    }
  }
}

function loadEditMode(){
  // CREO CAMPI DI INPUT
  createInputField('firstname');
  createInputField('lastname');
  createInputField('email');
  createInputField('instagram');

  // MODIFICO BOTTONE (EDIT -> UPDATE)
  let cancelButton = document.getElementById("edit-button");
  cancelButton.id = 'cancel-button';
  cancelButton.classList.add('me-1');
  cancelButton.textContent = 'Cancel';

  // CREO BOTTONE UPDATE
  let updateButton = document.createElement('button');
  updateButton.id = 'update-button';
  updateButton.type = 'button';
  updateButton.classList.add("btn", "btn-success", "float-end");
  updateButton.textContent = 'Update';

  // AGGIUNGO IL BOTTONE CANCEL
  cancelButton.parentNode.insertBefore(updateButton, cancelButton.previousSibling);
}

function loadLandingPage(){
  // RESETTO IL DIV ERRORE
  let updateError = document.getElementById("update-error");
  updateError.classList.add("d-none", "alert-warning");

  // RESETTO I CAMPI
  createShowField('firstname');
  createShowField('lastname');
  createShowField('email');
  createShowField('instagram');

  loadData();
  
  // RIMUOVO IL BOTTONE UPDATE
  let updateButton = document.getElementById("update-button");
  updateButton.parentNode.removeChild(updateButton);
  
  // MODIFICO IL BOTTONE (Cancel -> Edit)
  let cancelButton = document.getElementById("cancel-button");
  cancelButton.id = "edit-button";
  cancelButton.textContent = 'Edit profile';
}

async function updateProfile(){
  try{
    let updateForm = document.getElementById("update-form");
    let data2Update = getDataFromForm(updateForm);
    const response = await fetch('../backend/update_profile.php', {
      method: 'POST',
      body: data2Update,
    })


    //throw new Error(`${response.status} ${response.statusText}`);
    if (!response.ok) throw new Error(`${response.statusText}`);

    const data = await response.json();

    firstname = data.firstname;
    lastname = data.lastname;
    email = data.email;
    instagram = data.instagram;

    loadLandingPage();

    // SEGNALO IL CORRETTO FUNZIONAMENTO
    let updateError = document.getElementById("update-error");
    updateError.classList.remove("d-none", "alert-warning");
    updateError.classList.add("alert-success");
    updateError.textContent = data.message;

  } catch (error){
    let updateError = document.getElementById("update-error");
    updateError.classList.remove("d-none", "alert-success");
    updateError.classList.add("alert-warning");
    updateError.textContent = error.message;
  }
}

function createInputField(field) {
  let id = 'show_'.concat(field);
  //console.log(id);
  let parameter = document.getElementById(id);

  let inputField = document.createElement('input');
  // TODO: controllare il tipo di parametro (text, telefono, nome, web)
  
  inputField.id = field;
  inputField.type = 'text';
  inputField.className = 'form-control';
  inputField.name = field;
  inputField.value = parameter.textContent;

  switch(field){
    case 'email':
      inputField.type = 'email';
      inputField.classList.add("text-bg-secondary","text-dark", "bg-opacity-25");
      inputField.readOnly = true;  
    break;
    case 'instagram':
      inputField.value = parameter.textContent.substring(1);
    break;
  }

  parameter.parentNode.replaceChild(inputField, parameter);
}

function createShowField(field) {
  let inputField = document.getElementById(field);

  let value, link;
  switch(field){
    case "firstname":
      value = firstname;
      link = false;
      break;
    case "lastname":
      value = lastname;
      link = false;
      break;
    case "email":
      value = email;
      link = false;
      break;

    case "instagram":
      value = instagram;
      link = true;
      break;
  }

  if(link){
    let linkField = document.createElement('a');
    linkField.id = "show_".concat(field);
    linkField.classList.add("link-opacity-100");
    linkField.textContent = value;
    inputField.parentNode.replaceChild(linkField, inputField);
  }else{
    let p = document.createElement('p');
    p.id = "show_".concat(field);
    p.classList.add('text-muted', 'mb-0');
    p.textContent = value;
    inputField.parentNode.replaceChild(p, inputField);
  }

}

function loadData(){
  document.getElementById("fullname").textContent = firstname + " " + lastname;

  document.getElementById("show_firstname").textContent = firstname;
  document.getElementById("show_lastname").textContent = lastname;
  document.getElementById("show_email").textContent = email;
  
  let instagramVisual = document.getElementById("show_instagram");
  instagramVisual.textContent = "@" + instagram;
  instagramVisual.href = "https://www.instagram.com/" + instagram;
  
}