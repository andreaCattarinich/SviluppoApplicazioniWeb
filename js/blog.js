import { getCurrentData } from "./utils.js";
import { myFetch } from './utils.js';

document.addEventListener("DOMContentLoaded", main);

function main(){
  loadPage(1);
  // loadPagination(1);
  document.getElementById('pagination').addEventListener('click', loadAnotherPage);

  let add = document.getElementById("add-button");
  add.addEventListener("click", addPost);
}

function loadAnotherPage(event) {
  if (event.target.classList.contains('page-link')) {
    // Verifica se il testo del nodo figlio è "Next"
    let newPage;
    let pressed = event.target.childNodes[0].textContent;
    if (pressed === 'Next') {
      let active = document.getElementsByClassName('active');
      newPage = parseInt(active[0].childNodes[0].textContent) + 1;
    }else if(pressed === 'Previous'){
      let active = document.getElementsByClassName('active');
      newPage = parseInt(active[0].childNodes[0].textContent) - 1;
    }else{
      newPage = parseInt(event.target.innerText);
    }
    loadPage(newPage);
  }
}

async function loadPage(page){
  let data = await  myFetch(`../backend/show_posts.php?page=${page}`, null, 'GET');

  console.log(data);
  if(data.success){
    let posts = document.getElementById("posts");
    posts.innerHTML = '';

    for (let i = 0; i < data.posts.length; i++) {
      let postDiv = document.createElement("div");
      postDiv.innerHTML = data.posts[i].Post;
      posts.appendChild(postDiv);
    }
    loadPagination(page, data.num_pagination);
  }else{
    window.location.href = 'signin.html';
  }
}

function loadPagination(currentPage, numPagination){
    let pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    //<editor-fold desc="PREVIOUS">
    let li = document.createElement('li');
    li.classList.add("page-item");
    currentPage === 1 ? li.classList.add('disabled') : null;
    let a = document.createElement('a');
    a.classList.add("page-link");
    a.textContent = "Previous";
    a.href = "#";
    li.appendChild(a);
    pagination.appendChild(li);
    //</editor-fold>

    // ADD NUMBERS BEETWEN Previous ( ) Next
    for(let i=1; i<= numPagination; i++){
      let li = document.createElement('li');
      li.classList.add("page-item");

      if(currentPage === i){
        li.classList.add("active");
        li.ariaCurrent = "page";
        let span = document.createElement('span');
        span.classList.add("page-link");
        span.textContent = i.toString();

        li.appendChild(span);
        pagination.appendChild(li);
      }else{
        let a = document.createElement('a');
        a.classList.add("page-link");
        a.textContent = i.toString();
        a.href = "#";
        li.appendChild(a);
        pagination.appendChild(li);
      }
    }

    //<editor-fold desc="NEXT">
    li = document.createElement('li');
    li.classList.add("page-item");
  currentPage === numPagination ? li.classList.add('disabled') : null;
    a = document.createElement('a');
    a.classList.add("page-link");
    a.textContent = "Next";
    a.href = "#";
    li.appendChild(a);
    pagination.appendChild(li);
    //</editor-fold>
}

async function addPost(event){
  event.preventDefault();

  let content = tinymce.get("myTextarea").getContent({format: 'html'});
  if(content){
    let HTMLPost = createStandardPost(content, 'StandardName');

    let formData = new FormData();
    formData.append('post', HTMLPost.outerHTML);

    let data = await myFetch('../backend/add_post.php', formData, 'POST');

    if(data.success){
      // Return with ?success=true

      window.location.href = "blog.html";
    }else{
      console.log(data);
    }
  }
}

function createStandardPost(content, fullname){
  // SECTION
  const newSection = document.createElement('div');
  newSection.id = "new_section";
  newSection.classList.add("card", "text-center", "m-4");
  // HEADER
  const newHeader = document.createElement('div');
  newHeader.classList.add("card-header");
  newHeader.textContent = fullname;
  // BODY
  let newBody = document.createElement('div');
  newBody.classList.add("card-body");
  newBody.innerHTML = content;
  // FOOTER
  let newFooter = document.createElement('div');
  newFooter.classList.add("card-footer", "text-muted");
  newFooter.innerHTML = getCurrentData();

  newSection.appendChild(newHeader);
  newSection.appendChild(newBody);
  newSection.appendChild(newFooter);

  return newSection;
}