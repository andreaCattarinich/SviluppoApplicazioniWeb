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
      let page = event.target.innerText;
      loadPage(page);
      // loadPagination(page);
  }
}

async function loadPage(page){
  let data = await myFetch('../backend/show_posts2.php', '0', 'POST');

  if(data.success){
    let posts = document.getElementById("posts");
    posts.innerHTML = '';

    for (let i = 0; i < data.num_posts; i++) {
      let postDiv = document.createElement("div");
      postDiv.innerHTML = data[i].post;
      posts.appendChild(postDiv);
    }
  }
  loadPagination(page);
}

async function loadPagination(page){
  let formData = new FormData();
  formData.append("currPage", page);

  let data = await myFetch('../backend/get_pagination.php', formData, 'POST');
  if(data.success){
    console.log(data);
    let pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
  
    for(let i=1; i<= data.pagination_numbers; i++){
      let li = document.createElement('li');
      li.classList.add("page-item");


      if(data.currPage == i){
        li.classList.add("active");
        li.ariaCurrent = "page";
        let span = document.createElement('span');
        span.classList.add("page-link");
        span.textContent = i;
        li.appendChild(span);
        pagination.appendChild(li);
      }else{
        let a = document.createElement('a');
        a.classList.add("page-link");
        a.textContent = i;
        a.href = "#";
        
        li.appendChild(a);
        pagination.appendChild(li);
  
      }
      // <li class="page-item active" aria-current="page">
      // <span class="page-link">2</span>
      // </li>

    }

  }
}

async function addPost(event){
  event.preventDefault();

  let content = tinymce.get("myTextarea").getContent({format: 'html'});
  if(content){
    const token = getCookie('rememberMe');
    const cookieRememberMe = new FormData();
    cookieRememberMe.append("Token", token);
  
    let data = await myFetch('../backend/show_profile.php', cookieRememberMe, 'POST');
  
    if(!data.success){
      window.location.href = 'blog.html';
    }

    let fullname = data.firstname + " " + data.lastname;
    // console.log(fullname);
    let HTMLPost = createStandardPost(content, fullname);

    let formData = new FormData();
    formData.append('Email', data.email);
    formData.append('Post', HTMLPost.outerHTML);
    formData.append('Date', Date.now());
  
    data = await myFetch('../backend/add_post.php', formData, 'POST');
    // console.log(data);
    if(data.success){
      console.log(data);
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