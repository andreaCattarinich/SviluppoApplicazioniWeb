import {getCookie, isAdmin, startPage} from "./utils.js";

let STDroleButton =
    '    <div class="input-group">\n' +
    '        <button id="button0" class="btn btn-success" type="button">Change</button>\n' +
    '        <select class="form-select" id="input0" aria-label="Example select with button addon">\n' +
    '            <option selected="">Current</option>\n' +
    '            <option value="1">Moderator</option>\n' +
    '            <option value="2">Viewer</option>\n' +
    '            <option value="3">Blocked</option>\n' +
    '        </select>\n' +
    '    </div>';

try {
    if(isAdmin()) await startPage();
    else window.location.href = 'index.html';

    await loadUsers();
    document.getElementById("search").addEventListener('input', await loadUsers());
    document.addEventListener('click', clickButton);
} catch (error) {
    console.log(error);
}

async function loadUsers() {
    try {
        let search = document.getElementById('search').value;

        const token = getCookie('auth-token');
        const response = await fetch(`../backend/show_users.php?search=${search}`, {
            method: 'GET',
            headers: {
                Authentication: `Bearer ${token}`,
            },
        });

        const data = await response.json();

        if (!response.ok) throw new Error(`${response.status} ${data.message}`);

        if (response.status === 200 && data.message === 'No recent posts') {
            let table = document.getElementById("table");
            table.innerHTML = '';
            document.getElementById('title').innerText = 'No Data';
        }else{
            createTable(data.users);
        }
    } catch (error) {
        console.log(error);
    }
}

function createTable(jsonData) {
    let table = document.getElementById("table");
    table.innerHTML = '';

    let cols = Object.keys(jsonData[0]);

    let thead = document.createElement("thead");
    let tr = document.createElement("tr");

    let th = document.createElement("th");
    th.scope = "col";
    th.innerText = "#";
    tr.appendChild(th);

    cols.forEach((item) => {
        let th = document.createElement("th");
        th.scope = "col";
        th.innerText = item;
        tr.appendChild(th);
    });
    thead.appendChild(tr);
    table.append(thead);
    let tBody = document.createElement('tbody');

    let counter = 1;
    jsonData.forEach((item) => {
        let tr = document.createElement("tr");
        tr.classList.add(classColor(item));
        let th = document.createElement("th");
        th.scope = "row";
        th.innerText = counter.toString();
        tr.appendChild(th);

        let vals = Object.values(item);

        let role = 0;
        vals.forEach((elem) => {
            let td = document.createElement("td");
            if(role === 3 && elem !== 'Admin') {
                let roleButton = STDroleButton;
                roleButton = roleButton.replace(/0/g, counter.toString());
                roleButton = roleButton.replace('Current', elem.toString());
                td.innerHTML = roleButton;
            }else{
                td.innerText = elem;
            }
            role++;
            tr.appendChild(td);
        });
        counter++;
        tBody.appendChild(tr);
    });
    table.appendChild(tBody);
}

function classColor(item) {
    switch (item.role) {
        case 'Admin':
            return 'table-success';
        case 'Moderator':
            return 'table-primary';
        case 'Viewer':
            return 'table-warning';
        case 'Blocked':
            return 'table-danger';
    }
}

async function clickButton(event){
    if(event.target.tagName === 'BUTTON' && event.target.textContent === 'Change'){
        let button = event.target.id;
        try{
            let select = button.replace('button', 'input');
            let input = document.getElementById(select);
            let text = input.options[input.selectedIndex].text;

            let email = getEmailFromTable(parseInt(button.match(/\d+/g)));

            let formData = new FormData();
            formData.append('Role', text);
            formData.append('Email', email);

            const token = getCookie('auth-token');
            const response = await fetch('../backend/update_user.php', {
                method: 'POST',
                headers: {
                    Authentication: `Bearer ${token}`,
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) throw new Error(`${response.status} ${data.message}`);

            if(response.redirected){
                window.location.href = response.url;
            }
            location.reload();
        } catch (error){
            console.log(error);
        }
    }
}

function getEmailFromTable(numRow){
    const table = document.getElementById('table');
    const rows = table.getElementsByTagName('tr');

    for(let i=1; i<rows.length; i++){
        if(i === numRow) {
            const column = rows[i].getElementsByTagName('td');
            return column[2].innerText;
        }
    }
}