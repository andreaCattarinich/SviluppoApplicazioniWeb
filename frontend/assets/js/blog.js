import {getCookie, startPage, parseJwt} from "./utils.js";

try{
    await startPage();

    let payload = parseJwt(getCookie('auth-token'));
    //console.log(payload);
    document.getElementById('user').innerText =
        payload.data.firstname + ' ' + payload.data.lastname + ' - ' + payload.role ;

    await loadPage(1);
    document.getElementById('pagination').addEventListener('click', loadAnotherPage);
    document.getElementById("add-button").addEventListener('click', addPost);
    document.getElementById("search").addEventListener('input', async function searchPost(){
        let search = document.getElementById('search').value;
        await loadPage(1, search);
    });

} catch (error){
    console.log(error);
}

async function loadPage(page, search = ''){
    try {
        const token = getCookie('auth-token');
        const response = await fetch(`../backend/show_posts.php?page=${page}&search=${search}`, {
           method: 'GET',
           headers: {
               Authentication: `Bearer ${token}`,
           },
        });

        const data = await response.json();

        if (!response.ok) throw new Error(`${response.status} ${data.message}`);

        // IF ANY POST
        if(data.posts){
            // PRINT THEM
            document.getElementById('title').innerText = 'Recent Posts';
            let posts = document.getElementById("posts");
            posts.innerHTML = '';

            // PRINT POST ONE BY ONE
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

            // LOAD PAGINATION
            loadPagination(page, data.num_pagination);
        }else{
            // ELSE SHOW MESSAGE
            document.getElementById('title').innerText = data.message;
            let posts = document.getElementById("posts");
            posts.innerHTML = '';
            if(document.getElementById('pagination'))
                document.getElementById('pagination').innerHTML = '';
        }

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
    loadPage(newPage, document.getElementById('search').value);
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
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) throw new Error(`${response.status} ${data.message}`);

        location.reload();
    } catch (error){
        console.log(error);
        handleErrorBlog()
    }
}

function classColor(item) {
    switch (item.role) {
        case 'Admin':
            return 'text-bg-success';
        case 'Moderator':
            return 'text-bg-primary';
        case 'Viewer':
            return 'text-bg-warning';
        case 'Blocked':
            return 'text-bg-danger';
        default:
            return 'text-bg-dark';
    }
}

function handleErrorBlog() {
    let div = document.getElementById('info');
    div.classList.remove('d-none');
}