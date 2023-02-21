'use strict';

const username_availability = function (username) {
    /*
        Displays error to user if username entered is taken.
    */

    // Checks if username is empty
    if (username !== "") {
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
    if (email !== "") {
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

//for some reason this activates anyways, despite being commented out. ignore

/* AJAX has been added to the relevant page to do this
I was not aware this existed, as it threw errors in the console and failed to work; there wasn't any need to use this afterwards as I had fixed the issue before noticing this  
document.querySelector('#school-board').addEventListener("change",  (event) => {
    
        //Updates the school section with the schools of the 
        //selected school board.
    

    // Gets shcool_board
    let school_board = event.target.value;

    // Creates new HTTP request
    let query = new XMLHttpRequest();
    query.open("GET", encodeURI("http://localhost:8000/get/schools.php?school_board=" + shool_board));

    query.onreadystatechange = () => {
        // Cheks if request has finished successfully
        if (query.readyState == 4 && query.status == 200) {
            // Converts response text to javascript object
            let schools_info = JSON.parse(query.responseText);
            // Get schools select field
            let schools = document.querySelector('#school');

            // Removes all options (execpt the first) in the schools select field
            while (schools.children[1]) 
            {
                schools.removeChild(schools.children[1]);
            }

            // Loops threw schools received
            for (let i = 0; i < schools_info.length; i++)
            {
                // Creates option element
                let option = document.createElement("option");

                // Sets option values
                option.value = schools_info[i]["school_id"];
                option.innerText = schools_info[i]["school_name"];

                // Adds element to school select field
                schools.appendChild(option);
            }
            
            // Sets the value back to default
            schools.value = "default";
        }
    };

    // Sends request 
    query.send();
});
*/

const valid_date = function (date) {
    /*
        Returns wether or not date correctly formated.

        return: bool
    */
    return /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/.test(date);
}

document.querySelector('#birth-date').addEventListener("blur", (event) => {
    /*
        Displays error when user click on another element and entered value
        is not properly formated.
    */

    // Gets date entered
    let birth_date = event.target.value;

    // Checks if the date is properly formated
    if (birth_date != "" && !valid_date(birth_date)) {
        // Displays error
        document.getElementById('birth-date_error').innerText = "Invalid date.";
    } else {
        // Removes error
        document.getElementById('birth-date_error').innerText = "";
    }
});