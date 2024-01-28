import { getCookie } from "./utils.js";

document.addEventListener("DOMContentLoaded", loadPage(1));
document.getElementById('pagination').addEventListener('click', loadAnotherPage);
document.getElementById("add-button").addEventListener("click", addPost);

async function loadPage(page){
    try {
        const token = getCookie('auth-token');
        const response = await fetch(`../backend/show_posts.php?page=${page}`, {
           method: 'GET',
           headers: {
               Authentication: `Bearer ${token}`,
           },
        });

        //throw new Error(`${response.statusText}`);
        if (!response.ok) throw new Error(`${response.status} ${response.statusText}`);

        if(response.redirected){
            window.location.href = response.url;
        }

        if (response.status === 204){
            document.getElementById('title').innerText = 'No Recent Posts';
            return;
        }

        const data = await response.json();
        console.log(data);

        let posts = document.getElementById("posts");
        posts.innerHTML = '';

        for (let i = 0; i < data.posts.length; i++) {
            let postDiv = document.createElement("div");
            postDiv.classList.add('card', 'my-4', 'ms-4');

            let bodyDiv = document.createElement('div');
            bodyDiv.classList.add('card-body');

            let cardTitle = document.createElement('h5');
            cardTitle.classList.add('card-title');
            cardTitle.innerText = data.posts[i].fullname;

            let cardSubtitle = document.createElement('span');
            //cardSubtitle.classList.add('card-subtitle', 'mb-2', 'text-body-secondary', 'badge', 'text-bg-success');
            cardSubtitle.classList.add('card-subtitle', 'mb-2', 'badge', classColor(data.posts[i]));
            cardSubtitle.innerHTML = data.posts[i].role;

            let time = document.createElement('p');
            let small = document.createElement('small');
            small.innerText = data.posts[i].created_at;
            time.appendChild(small);

            let contentPost = document.createElement('div');
            contentPost.innerHTML = data.posts[i].content;

            postDiv.appendChild(bodyDiv);
            bodyDiv.appendChild(cardTitle);
            bodyDiv.appendChild(cardSubtitle);
            bodyDiv.appendChild(time);
            bodyDiv.appendChild(contentPost);
            posts.appendChild(postDiv);
        }
        loadPagination(page, data.num_pagination);
    }catch (error){
        console.log(error);
    }
}

function loadAnotherPage(event) {
  if (event.target.classList.contains('page-link')) {
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

function loadPagination(currentPage, numPagination){
    let pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    //<editor-fold desc="PREVIOUS">
    let li = document.createElement('li');
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
    try{
        let content = tinymce.get("myTextarea").getContent({format: 'html'});
        const formData = new FormData();
        formData.append('content', content);

        const token = getCookie('auth-token');
        const response = await fetch('../backend/add_post.php', {
            method: 'POST',
            headers: {
                Authentication: `Bearer ${token}`,
            },
            //body: JSON.stringify({post : content}),
            body: formData,
        });

        // if (!response.ok) throw new Error(`${response.status} ${response.statusText}`);
        if (!response.ok) throw new Error(`${response.statusText}`);

        location.reload();
        //window.location.href = "blog.html";
    } catch (error){
        window.location.href = 'signin.html';
    }
}

function classColor(item) {
    switch (item.role) {
        case 'Admin':
            return 'text-bg-success';
        case 'Moderator':
            return 'text-bg-primary';
        case 'Editor':
            return 'text-bg-warning';
        case 'Blocked':
            return 'text-bg-danger';
        default:
            return 'text-bg-dark';
    }
}
