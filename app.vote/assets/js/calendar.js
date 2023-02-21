const daysInMonth = (year, month) => {
    /*
        Returns number of days in a month.
    */
    if (month < 0) {
        return new Date(year - 1, 0, 0).getDate();
    }

    if (month > 11) {
        return new Date(year + 1, 11, 0).getDate();
    }

    return new Date(year, month + 1, 0).getDate();
}

Date.prototype.getWeekDay = function () {
    /*
        Returns the day of the week the day is on.
        Monday = 1 - Sunday = 7
    */

    let day = this.getUTCDay();
    if (day == 0) {
        return 7;
    }

    return day;
};

// Creates date with current time
let current_date = new Date();

// Gets current month and year
let current_year = current_date.getFullYear();
let current_month = current_date.getMonth();

// Constants for calendar
const MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
const SHORT_MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const CALENDAR = document.getElementById("calendar");

const increaseMonth = function () {
    /*
        Increases the current month and updates the calendar
        with the new month.
    */

    // Checks if there is a pop up
    if (CALENDAR.lastElementChild.classList.contains("pop-up")) {
        CALENDAR.lastElementChild.remove();
    }

    // Increases month by 1
    current_month++;

    // Checks if month is going over 12
    if (11 < current_month) {
        // Resets month and increases year by 1
        current_month = 0
        current_year++;
    }

    // Updates calendar with the current year and month
    updateCalendar(current_year, current_month);
    // Displays elections on current month
    getElections(current_year, current_month);
};

const decreaseMonth = function () {
    /*
        Decreases the current month and updates the calendar
        with the new month.
    */

    // Checks if there is a pop up
    if (CALENDAR.lastElementChild.classList.contains("pop-up")) {
        CALENDAR.lastElementChild.remove();
    }

    // Decreases month by 1
    current_month--;

    // Checks if month is smaller than 1
    if (current_month < 0) {
        // Resets current month and decreases year
        current_month = 11;
        current_year--;
    }

    // Updates calendar with the current year and month
    updateCalendar(current_year, current_month);
    // Displays elections on current month
    getElections(current_year, current_month);
};

// Getting buttons from DOM
let right = document.getElementById("right");
let left = document.getElementById("left");

// Adding event listeners 
right.addEventListener("click", increaseMonth);
left.addEventListener("click", decreaseMonth);

right.addEventListener('mousedown', (event) => { event.preventDefault(); }, false);
left.addEventListener('mousedown', (event) => { event.preventDefault(); }, false);


const updateCalendar = function (year, month) {
    /*
        Updates calendar to year and month givin.
    */

    // Updates date heading in calendar with new month and year
    document.getElementById("header-date").innerText = `${SHORT_MONTHS[month]} ${year}`;

    // Gets the day cells in the calendar grid
    let calendar = Array.from(CALENDAR.children).slice(7);

    // Gets the first day and number of days in the new month
    let day = new Date(year, month).getWeekDay();
    let days_in_month = daysInMonth(year, month);

    // Checks if the first day of the month does not start on a monday
    if (day != 1) {
        // Decreases month
        month--;
        if (month < 0) {
            month = 11;
            year--;
        }

        // Gets number of days in the month and the day the first cell in the calendar will start on
        days_in_month = daysInMonth(year, month);
        day = days_in_month - day + 2;
    }

    // Loops threw each cell in the calendar
    calendar.forEach(element => {
        // Sets the day in the cell to the current day 
        element.children[0].innerText = day;

        // Checks if the month is not the current month
        if (current_month != month) {
            element.classList.add("outside-month");
        }
        else if (element.classList.contains("outside-month")) {
            element.classList.remove("outside-month");
        }

        // Checks if the day is today 
        if (current_date.getDate() == day && current_date.getMonth() == month && current_date.getFullYear() == year) {
            element.children[0].classList.add("current-day");
        }
        else if (element.children[0].classList.contains("current-day")) {
            element.children[0].classList.remove("current-day");
        }

        // Clears all the events from the cell
        while (element.childNodes.length != 1) {
            element.childNodes[1].remove();
        }

        day++;

        // Checks if the day goes over the number of days in the month
        if (days_in_month < day) {
            month++;
            if (11 < month) {
                month = 0
                year++;
            }

            // Resets number of days in the month and day
            days_in_month = daysInMonth(year, month);
            day = 1;
        }
    });
};

const displayDayElections = function (event, date) {
    /*
        Displays a popup with all the elections of a givin date.
    */

    // Checks if there is already a pop up
    if (CALENDAR.lastElementChild.classList.contains("pop-up")) {
        CALENDAR.lastElementChild.remove();
    }

    // Gets the cell that triggered the event
    var cell = event.target.parentElement;

    // Creates pop up
    var pop_up = document.createElement("div");
    pop_up.classList.add("pop-up")
    pop_up.style.position = "absolute";

    // Adds each election to the pop up
    for (let i = 0; i < cell.children.length - 1; i++) {
        // Copies element
        let element = cell.children[i].cloneNode(true);

        element.onclick = (event) => {
            window.location.href = `
                http://localhost:8000/pages/election-details.php?election=${element.id}
            `;
        };

        // Displays and adds element to pop up
        element.style.display = "block";
        pop_up.appendChild(element);
    }

    // Formats heading of pop up
    pop_up.children[0].textContent = DAYS[date.getDay()] + " " + pop_up.children[0].textContent;
    pop_up.children[0].classList.remove("current-day");
    pop_up.children[0].onclick = (event) => event.stopPropagation();

    // Adds close button to pop up
    let close = document.createElement("img");
    close.src = "/assets/svg/close.svg";
    close.classList.add("close", "close-svg");
    close.onclick = (event) => {
        pop_up.style.animation = "fadeout 0.4s ease";
        setTimeout(pop_up.remove.bind(pop_up), 350);
        event.stopPropagation();
    };

    // Adds close button
    pop_up.children[0].appendChild(close);

    // Calculates position for popup
    let viewport_offset = cell.getBoundingClientRect();
    let navbar = document.getElementById("navbar");
    pop_up.style.top = viewport_offset.top - (viewport_offset.height / 8) + window.scrollY + "px";

    if (parseInt(window.getComputedStyle(navbar, null)["left"]) < 0) {
        pop_up.style.left = viewport_offset.left - (112.5 - viewport_offset.width / 2) + "px";
    }
    else {
        pop_up.style.left = viewport_offset.left - (112.5 - viewport_offset.width / 2) - navbar.getBoundingClientRect().width + "px";
    }

    // Adds pop up
    CALENDAR.appendChild(pop_up);
};


const addElection = function (election_name, election_date, selector, class_list) {
    /*
        Adds an election to the calendar.
    */

    election_date = new Date(election_date);

    // Calculates the cell that the election will be in 
    let position = 7 + (election_date.getWeekDay() - 1);
    let first_day_month = new Date(current_year, current_month, 1).getWeekDay();;
    if (election_date.getMonth() == current_month) {
        position = 7 + (first_day_month - 1) + (election_date.getDate() - 1);
    }
    if (election_date.getMonth() == current_month + 1 || (election_date.getMonth() == 0 && current_month == 11)) {
        position = 7 + (first_day_month - 1) + (election_date.getDate() - 1) + daysInMonth(current_year, current_month);
    }

    // The cell the election will be added to 
    let cell = CALENDAR.children[position];

    // Creates div to display election
    let election = document.createElement("div");
    election.id = selector;
    election.textContent = election_name;

    // Adds classes to the election
    election.classList.add(...class_list);

    // Redirects to the elections detail page when the election is clicked
    election.onclick = (event) => {
        window.location.href = `
            http://localhost:8000/pages/election-details.php?election=${event.target.id}
        `;
    };

    // Checks if the cell has to many events
    if (cell.children.length >= 4 + 1) {

        let num_more = 1;
        election.style.display = "none";

        // Checks if the cell already contains an element that displays whether there is more
        if (cell.lastChild.classList.contains("more")) {
            num_more = parseInt(cell.lastChild.textContent) + 1;
            cell.lastChild.remove();
        }
        else {
            cell.lastChild.style.display = "none";
            num_more++;
        }

        // Creates a more button 
        var more = document.createElement("div");
        more.classList.add("event", "more");
        more.textContent = `${num_more} more`;

        more.onclick = (event) => displayDayElections(event, election_date);
    }

    cell.appendChild(election);

    // Checks if the more button was defined
    if (typeof (more) !== 'undefined') cell.appendChild(more);
};

const getElections = async function (year, month) {
    /*
        Gets elections for a givin month.
    */

    // Creates new HTTP request
    let query = new XMLHttpRequest();

    // Starting and finishing day on the calendar
    let start_day = parseInt(CALENDAR.children[7].children[0].textContent);
    let end_day = parseInt(CALENDAR.lastElementChild.children[0].textContent);

    // Calculates the starting and finishing dates displayed on the calendar
    let start = `${current_year}-${current_month + 1}-${start_day} 0:0:0`;
    let end = `${current_year}-${current_month + 2}-${end_day} 23:59:59`;

    if (start_day != 1) {
        if (month - 1 < 0) {
            start = `${current_year - 1}-${12}-${start_day} 0:0:0`;
        }
        else {
            start = `${current_year}-${current_month}-${start_day} 0:0:0`;
        }
    }

    if (current_month + 1 > 11) {
        end = `${current_year + 1}-${0}-${end_day} 23:59:59`;
    }

    // Sets location for request
    query.open("GET", encodeURI(`http://localhost:8000/get/elections.php?start=${start}&end=${end}}`));

    query.onreadystatechange = () => {
        // Checks if request has finished successfully
        if (query.readyState == 4 && query.status == 200) {
            // Parses response
            let elections = JSON.parse(query.responseText);

            // Adds each election to the calendar
            for (let election of elections) {
                addElection(
                    election["election_name"],
                    election["starting_date"],
                    election["election_selector"],
                    ["event", "start"]
                );
            }
        }
    };

    // Sends request 
    query.send();
};

// Updates calendar with the current year and month
updateCalendar(current_year, current_month);
// Displays elections on current month
getElections(current_year, current_month);