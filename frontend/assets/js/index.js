import { includeNavbar, includeFooter } from "./utils.js";
import {FOOTER, NAVBAR_HOMEPAGE} from "./constants.js";

try{
    await includeNavbar(NAVBAR_HOMEPAGE);
    await includeFooter(FOOTER);
} catch (error){
    console.log(error);
}
