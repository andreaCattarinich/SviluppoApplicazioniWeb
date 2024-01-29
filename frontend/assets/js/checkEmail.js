async function checkEmail(){
    try {
        let email = document.getElementById('email').value;
        let formData = new FormData();
        formData.append('email', email.toLowerCase());

        const response = await fetch('../backend/check_email.php', {
            method: 'POST',
            body: formData,
        });

        const data = await response.json();

        let checkEmail = document.getElementById('check-email');
        let inputEmail = document.getElementById('email');

        checkEmail.className = '';
        inputEmail.className = '';

        if (!response.ok || data.message === 'Email not valid') {
            checkEmail.classList.add('invalid-email-note');
            inputEmail.classList.add('form-control', 'border-danger', 'border-2');
        } else {
            checkEmail.classList.add('valid-email-note');
            inputEmail.classList.add('form-control', 'border-success','border-2');
        }

        checkEmail.innerHTML = '<strong>' + data.message + '</strong>';
    }catch (error){
       console.log(error);
    }
}