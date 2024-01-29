import {getDataFromForm, handleError, includeFooter, includeNavbar} from './utils.js';

let registrationForm = document.getElementById("registration");
registrationForm.addEventListener("submit", registration);

try{
  await includeNavbar('navbarHomepage.html');
  await includeFooter('footer.html');

} catch (error){
  console.log(error);
}

async function registration(event){
  event.preventDefault();
  try {
    let userData = getDataFromForm(registrationForm);
    const response = await fetch('../backend/registration.php', {
      method: 'POST',
      body: userData,
    });

    const data = await response.json();

    if(!response.ok) throw new Error(`${data.message}`);

    if(response.status === 201)
      window.location.href = `signin.html?success=${data.email}`;

  }catch (error){
    handleError(error.message);
  }
}