import {includeFooter, includeNavbar, isLogged} from './utils.js';
import {FOOTER, NAVBAR_HOMEPAGE} from "./constants.js";

try{
    isLogged();
    await includeNavbar(NAVBAR_HOMEPAGE);
    await includeFooter(FOOTER);

    const response = await fetch('../backend/logout.php');

    const data = await response.json();
    if (!response.ok){
        window.location.href = "index.html";
    }

    document.getElementById('title').innerText = data.message;
    document.getElementById('subtitle').classList.remove('d-none');
    document.getElementById('description').classList.remove('d-none');

    window.setTimeout(function(){
        window.location.href = "index.html";
    }, 4000);

} catch (error){
    console.log(error);
}