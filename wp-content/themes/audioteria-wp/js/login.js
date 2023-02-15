window.addEventListener('DOMContentLoaded', () => {

    signInValidation();

});


function signInValidation(event) {

    const signInBtn = document.querySelector('button.woocommerce-form-login__submit');
    const emailLoginInput = document.getElementById('username');
    const passwordLoginInput = document.getElementById('password');


    //show error

    const showError = (input, message) => {
        // get the form-field element
        const formField = input;
        // add the error class

        formField.classList.add('error-border');

        // show the error message


        document.querySelector('.login-error').innerHTML = '';

        const errorEl = '<div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert"><li>' + message + '</li></ul></div>';
        if (document.querySelector('.login-error')) {
            document.querySelector('.login-error').innerHTML = errorEl;
        }

        return '';

    };

    //Show Success

    const showSuccess = (input, message = '') => {
        // get the form-field element
        const formField = input;
        // add the error class
        formField.classList.remove('error-border');
        formField.classList.add('error-success');


        // show the error message
        if (document.querySelector('.login-error')) {
            document.querySelector('.login-error').innerHTML = '';
        }


    };

    //check for valid email

    const isEmailValid = (email) => {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    };


    //check email

    const checkEmail = () => {

        let valid = false;
        const emailVal = emailLoginInput.value.trim();


        if (emailVal === '') {

            showError(emailLoginInput, 'Email is Required.');
        } else if (!isEmailValid(emailVal)) {

            showError(emailLoginInput, 'Email is not valid.')
        } else {
            showSuccess(emailLoginInput);
            valid = true;
        }
        return valid;
    }

    //Check password
    const checkPassword = () => {

        let valid = false;
        const passwordVal = passwordLoginInput.value.trim();
        const emailVal = emailLoginInput.value.trim();

        if (passwordVal === '' && emailVal === '') {
            showError(passwordLoginInput, 'Email & Password Fields are Required.');
        } else if (passwordVal === '') {
            showError(passwordLoginInput, 'Password Field is Required.');
        } else if (passwordVal.length < 5) {
            showError(passwordLoginInput, 'Password must has at least 8 characters that include at least 1 lowercase character, 1 uppercase characters, 1 number, and 1 special character in (!@#$%^&*)');
        } else {
            showSuccess(passwordLoginInput);
            valid = true;
        }

        return valid;
    };


    if (signInBtn) {
        signInBtn.addEventListener('click', function (event) {
            // event.preventDefault();

            checkEmail();
            checkPassword();

            console.log('login btn clicked');
            canLogin = checkPassword() && checkEmail();

            if (!canLogin) {
                event.preventDefault();
            }


        });
    }

}
