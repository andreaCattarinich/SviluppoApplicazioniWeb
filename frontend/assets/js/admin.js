import {getCookie, includeFooter, includeNavbar} from "./utils.js";

let searchForm;
try {
    await includeNavbar('navbarPersonal.html');
    await includeFooter('footer.html');

    searchForm = document.getElementById('search-form');
    searchForm.addEventListener('submit', searchUsers);
    searchUsers({ preventDefault: () => {} });

    document.addEventListener('click', clickButton);
} catch (error) {
    console.log(error);
}

async function searchUsers(event) {
    if(event) {
        event.preventDefault();
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

            if (response.status === 204) {
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
}

let STDroleButton =
    '    <div class="input-group">\n' +
    '        <button id="button0" class="btn btn-success" type="button">Change</button>\n' +
    '        <select class="form-select" id="input0" aria-label="Example select with button addon">\n' +
    '            <option selected="">Current</option>\n' +
    '            <option value="1">Admin</option>\n' +
    '            <option value="2">Moderator</option>\n' +
    '            <option value="3">Editor</option>\n' +
    '            <option value="4">Blocked</option>\n' +
    '        </select>\n' +
    '    </div>';
function createTable(jsonData) {
    let table = document.getElementById("table");
    table.innerHTML = '';
    // Get the keys (column names) of the first object in the JSON data
    let cols = Object.keys(jsonData[0]);

    // Create the header element
    let thead = document.createElement("thead");
    let tr = document.createElement("tr");

    // Loop through the column names and create header cells
    let th = document.createElement("th");
    th.scope = "col";
    th.innerText = "#";
    tr.appendChild(th);

    cols.forEach((item) => {
        let th = document.createElement("th");
        th.scope = "col";
        th.innerText = item; // Set the column name as the text of the header cell
        tr.appendChild(th); // Append the header cell to the header row
    });
    thead.appendChild(tr); // Append the header row to the header
    table.append(thead) // Append the header to the table
    let tBody = document.createElement('tbody');

    let counter = 1;
    // Loop through the JSON data and create table rows
    jsonData.forEach((item) => {
        let tr = document.createElement("tr");
        tr.classList.add(classColor(item));
        let th = document.createElement("th");
        th.scope = "row";
        th.innerText = counter.toString();
        tr.appendChild(th);

        // Get the values of the current object in the JSON data
        let vals = Object.values(item);

        let role = 0;
        vals.forEach((elem) => {
            let td = document.createElement("td");
            if(role === 3) {
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
        case 'Editor':
            return 'table-warning';
        case 'Blocked':
            return 'table-danger';
        default:
            return 'table-warning'; // NEW USER
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