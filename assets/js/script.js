let isLoggedIn = false; // simulate user login status

    const createBtn = document.getElementById('create-resume');

    createBtn.addEventListener('click', () => {
        if(!isLoggedIn){
            alert('Please login first!');  // <-- alert
            window.location.href = 'login.php'; // redirect to login page
        } else {
            window.location.href = 'form.html'; // dashboard / resume form
        }
    });