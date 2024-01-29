import { includeNavbar, includeFooter } from "./utils.js";

try{
    await includeNavbar('navbarHomepage.html');
    await includeFooter('footer.html');
} catch (error){
    console.log(error);
}
