document.querySelector('#country').addEventListener("change",  (event) => {
    /*
        Updates the school section with the schools of the 
        selected school board.
    */

    // Gets country selected
    let country = event.target.value;

    // Creates new HTTP request
    let query = new XMLHttpRequest();
    query.open("GET", encodeURI("http://localhost:8000/get/states.php?country=" + country));

    query.onreadystatechange = () => {
        // Cheks if request has finished successfully
        if (query.readyState == 4 && query.status == 200) {
            // Converts response text to javascript object
            let states_info = JSON.parse(query.responseText);
            // Get states select field
            let states = document.querySelector('#state');

            // Removes all options (execpt the first) in the states select field
            while (states.children[1]) 
            {
                states.removeChild(states.children[1]);
            }

            // Loops threw states received
            for (let i = 0; i < states_info.length; i++)
            {
                // Creates option element
                let option = document.createElement("option");

                // Sets option values
                option.value = states_info[i];
                option.innerText = states_info[i];

                // Adds element to school select field
                states.appendChild(option);
            }

            // Sets the value back to default
            states.value = "default";
        }
    };

    // Sends request 
    query.send();
});