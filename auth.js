document.addEventListener("DOMContentLoaded", function () {

    var form = document.querySelector("form");
    if (!form) return;

    form.addEventListener("submit", function (e) {

        clearErrors();

        var isValid = true;

        var username = form.querySelector("input[name='username']");
        var password = form.querySelector("input[name='password']");
        var confirmPassword = form.querySelector("input[name='confirm_password']");

        // Username validation
        if (username) {
            if (username.value.trim() === "") {
                showError(username, "Username is required");
                isValid = false;
            }
        }

        // Password validation
        if (password) {
            if (password.value.trim() === "") {
                showError(password, "Password is required");
                isValid = false;
            } else if (password.value.length < 6) {
                showError(password, "Password must be at least 6 characters");
                isValid = false;
            }
        }

        // Confirm password (register only)
        if (confirmPassword) {
            if (confirmPassword.value.trim() === "") {
                showError(confirmPassword, "Please confirm your password");
                isValid = false;
            } else if (password && password.value !== confirmPassword.value) {
                showError(confirmPassword, "Passwords do not match");
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
        }

    });

    // Show / Hide password on double click
    var passwordInputs = document.querySelectorAll("input[type='password']");

    for (var i = 0; i < passwordInputs.length; i++) {
        passwordInputs[i].addEventListener("dblclick", function () {
            if (this.type === "password") {
                this.type = "text";
            } else {
                this.type = "password";
            }
        });
    }

});


function showError(input, message) {

    var error = document.createElement("small");
    error.className = "error-message";
    error.textContent = message;

    input.style.border = "1px solid red";

    input.parentNode.appendChild(error);
}


function clearErrors() {

    var errors = document.querySelectorAll(".error-message");
    for (var i = 0; i < errors.length; i++) {
        errors[i].remove();
    }

    var inputs = document.querySelectorAll("input");
    for (var j = 0; j < inputs.length; j++) {
        inputs[j].style.border = "";
    }
}
