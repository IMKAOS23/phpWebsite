function closeLoginForm() {
    document.querySelector('.hidden-login').style.display = 'None';
    document.querySelector('.modal-overlay').style.display = 'None';
    document.body.classList.remove('modal-open');
};

function closeSignUpForm() {
    document.querySelector('.hidden-sign-up').style.display = 'None';
    document.querySelector('.modal-overlay').style.display = 'None';
    document.body.classList.remove('modal-open');
};

function showLoginForm() {
    document.querySelector('.hidden-login').style.display = "block";
    document.querySelector('.modal-overlay').style.display = 'block';
    document.body.classList.add('modal-open');
};

function showSignUpForm() {
    document.querySelector('.hidden-sign-up').style.display = "block";
    document.querySelector('.modal-overlay').style.display = 'block';
    document.body.classList.add('modal-open');
};