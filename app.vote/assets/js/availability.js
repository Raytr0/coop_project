'use strict';

const username_availability = function (username) {
    /*
        Displays error to user if username entered is taken.
    */

    // Checks if username is empty
    if (username != "") {
        // Creates new HTTP request
        let query = new XMLHttpRequest();
        query.open("GET", encodeURI("http://localhost:8000/get/availability.php?column=username&value=" + username));

        query.onreadystatechange = () => {
            // Cheks if request has finished successfully
            if (query.readyState == 4 && query.status == 200) {
                // Checks if username is taken
                if (JSON.parse(query.responseText)["taken"]) {
                    // Displays error to user
                    document.getElementById('username_error').innerText = "Username taken.";
                } else {
                    // Removes any error that is being displayed
                    document.getElementById('username_error').innerText = "";
                }
            }
        };

        // Sends request
        query.send();
    } else {
        // Removes any error that is being displayed
        document.getElementById('username_error').innerText = "";
    }
};

document.querySelector('#username').addEventListener("blur", (event) => {
    /*
        Displays error when user click on another element and entered value
        is taken.
    */

    // Gets username entered
    let username = event.target.value;

    username_availability(username);
});


const email_availability = function (email) {
    /*
        Displays error to user if email entered is taken.
    */

    // Checks if email is empty
    if (email != "") {
        // Creates new HTTP request
        let query = new XMLHttpRequest();
        query.open("GET", encodeURI("http://localhost:8000/get/availability.php?column=email&value=" + email));

        query.onreadystatechange = () => {
            // Cheks if request has finished successfully
            if (query.readyState == 4 && query.status == 200) {
                // Checks if email is taken
                if (JSON.parse(query.responseText)["taken"]) {
                    // Displays error to user
                    document.getElementById('email_error').innerText = "Email taken.";
                } else {
                    // Removes any error that is being displayed
                    document.getElementById('email_error').innerText = "";
                }
            }
        };

        // Sends request 
        query.send();
    } else {
        // Removes any error that is being displayed
        document.getElementById('email_error').innerText = "";
    }
};

document.querySelector('#email').addEventListener("blur", (event) => {
    /*
        Displays error when user click on another element and entered value
        is taken.
    */

    // Gets email entered
    let email = event.target.value;

    email_availability(email);
});

document.querySelector('#password').addEventListener("blur", (event) => {
    /*
        Removes error when user click on another element.
    */
    document.getElementById('password_error').innerText = " ";
});