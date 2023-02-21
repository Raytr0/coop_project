Date.prototype.getWeekDay = function () {
    /** 
     * Returns the day of the week the day is on.
     * Monday = 1 - Sunday = 7
    */

    let day = this.getUTCDay();
    if (day == 0) {
        return 7;
    }

    return day;
};

// Array that contains all the charts on the page
let charts = [];

// String to hold user type
let user_type;

// Schools to filter out
let school_filter;

// Constants for x-axis
const week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const month = [
    '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14',
    '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27',
    '28', '29', '30'
];

const year = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

const getMonday = (date) => {
    /** 
     * Returns the date of the monday of the
     * current week.
     * 
     * @param Date date
     * @return Date 
     */
    let day = date.getDay();

    diff = date.getDate() - day + (day == 0 ? -6 : 1);

    return new Date(date.setDate(diff));
};

const filter = (filter) => {
    /** 
     * Filters the chart based on what filter
     * was selected.
     * 
     * @param Event event 
     */

    if (filter == "Past Week") {
        for (let chart of charts) {
            let data = (user_type == "super") ?
                filterWeek(filterSchools(chart.data, school_filter)) : filterWeek(chart.data);

            chart.table.update(week, data);

            chart.chart.data = {
                labels: week,
                datasets: [{
                    backgroundColor: '#FF6384',
                    borderColor: '#FF6384',
                    data: data,
                    fill: false,
                }]
            }

            chart.chart.update();
        }
    }

    else if (filter == "Past Month") {
        for (let chart of charts) {
            let data = (user_type == "super") ?
                filterMonth(filterSchools(chart.data, school_filter)) : filterWeek(chart.data);

            chart.table.update(month, data);

            chart.chart.data = {
                labels: month,
                datasets: [{
                    backgroundColor: '#FF6384',
                    borderColor: '#FF6384',
                    data: data,
                    fill: false,
                }]
            }

            chart.chart.update();
        }
    }

    else if (filter == "Past Year") {
        for (let chart of charts) {
            let data = (user_type == "super") ?
                filterYear(filterSchools(chart.data, school_filter)) : filterWeek(chart.data);

            chart.table.update(year, data);

            chart.chart.data = {
                labels: year,
                datasets: [{
                    backgroundColor: '#FF6384',
                    borderColor: '#FF6384',
                    data: data,
                    fill: false,
                }]
            }

            chart.chart.update();
        }
    }
};

const filterWeek = (data) => {
    /** 
     * Filters data to results for 
     * the current week.
     * 
     * @param arr data
     * @return arr
     */
    let filtered_data = new Array(7).fill(0);
    let monday = getMonday(new Date());

    for (let item in data) {
        let item_date = new Date(item);

        if (monday.getDate() <= item_date.getDate() && 
            item_date.getDate() <= monday.getDate() + 6 &&
            monday.getMonth() <= item_date.getMonth() && 
            item_date.getMonth() <= monday.getMonth() + 29 &&
            monday.getFullYear() <= item_date.getFullYear() && 
            item_date.getFullYear() <= monday.getFullYear()) {
            filtered_data[item_date.getWeekDay()] = data[item];
        }
    }

    return filtered_data;
};

const filterMonth = (data) => {
    /** 
     * Filters data to results for 
     * the current month.
     * 
     * @param arr data
     * @return arr
    */
    let current_date = new Date();
    let filtered_data = new Array(30).fill(0);

    for (let item in data) {
        let item_date = new Date(item);

        if (current_date.getMonth() <= item_date.getDate() && 
            item_date.getDate() <= current_date.getMonth() &&
            current_date.getFullYear() <= item_date.getFullYear() && 
            item_date.getFullYear() <= current_date.getFullYear()) {
            filtered_data[item_date.getDate()] = data[item];
        }
    }

    return filtered_data;
};

const filterYear = (data) => {
    /** 
     * Filters data to results for 
     * the current year.
     * 
     * @param arr data
     * @return arr
     */
    let current_date = new Date();
    let filtered_data = new Array(12).fill(0);

    for (let item in data) {
        let item_date = new Date(item);

        if (current_date.getFullYear() <= item_date.getFullYear() && 
            item_date.getFullYear() <= current_date.getFullYear()) {
            filtered_data[item_date.getMonth()] = data[item];
        }
    }

    return filtered_data;
};

const get = async () => {
    /** 
     * Gets data for the dashboard and 
     * displays it.
     */
    let response = await fetch("http://localhost:8000/get/data.php");
    let json = await response.json();
    let data = JSON.parse(JSON.stringify(json));
    user_type = data.type;

    if (data.type == "candidate") {
        displayElections(data["elections"]);

        graph(data["num_visits"]["data"], filterWeek(data["num_visits"]["data"]), data["num_visits"]["title"], week, 1);
    }

    else if (data.type == "local") {
        let chart_num = 0;

        for (let item in data) {
            if (item == "type") continue;

            graph(data[item]["data"], filterWeek(data[item]["data"]), data[item]["title"], week, chart_num);

            chart_num++;
        }
    }

    else if (data.type == "super") {
        displaySchools(data);

        school_filter = "All";
        let chart_num = 0;

        for (let item in data) {
            if (item == "type") continue;

            graph(data[item]["data"], filterWeek(filterSchools(data[item]["data"], school_filter)), data[item]["title"], week, chart_num);

            chart_num++;
        }
    }
};

const graph = (data, chart_data, title, labels, chart_num) => {
    /** 
     * Creates a chart and graphs the data
     * givin.
     * 
     * @param arr data
     * @param arr chart_data
     * @param string title
     * @param arr labels
     * @param number char_num
     */
    let canvas = document.getElementById("chart" + chart_num).children[0];
    let table = new Table(labels, chart_data, canvas.parentElement);

    charts.push({
        data: data,
        table: table,
        chart: new Chart(canvas, {
            original_data: data,
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    backgroundColor: '#FF6384',
                    borderColor: '#FF6384',
                    data: chart_data,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                devicePixelRatio: 2,
                title: {
                    display: true,
                    text: title,
                    fontSize: 18,
                    fontColor: "#2C2C2C",

                },
                legend: {
                    display: false,
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    x: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    },
                    y: {
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        },

                    },
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: 10,
                            beginAtZero: true,
                        }
                    }],
                    scaleLabel: {
                        fontSize: 24,
                    },
                }
            }
        }
        )
    });
};

const displayElections = (elections) => {
    /** 
     * Displays the elections givin.
     * 
     * @param arr elections
     */
    let heading = document.createElement("h1");
    heading.textContent = "Current Elections";
    heading.style["margin-bottom"] = "10px";

    let election_list = document.createElement("ul");
    election_list.style.overflow = "auto";

    if (elections.length > 0) {
        for (let election of elections) {
            let item = document.createElement("li");
            item.innerHTML = "&bull; " + election;
            item.classList.add("election");

            election_list.appendChild(item);
        }

        let chart = document.getElementById("chart0");
    }
    else {
        election_list.textContent = "You are not running for any current election"
    }

    let chart = document.getElementById("chart0");

    chart.firstChild.remove();
    chart.firstChild.remove();

    chart.appendChild(heading);
    chart.appendChild(election_list);
};

const displaySchools = (data) => {
    /** 
     * Displays the schools in the dropdown
     * school menu
     * 
     * @param obj data
     */
    let school_filters = document.getElementById("schools");
    let schools = new Map();

    for (let chart in data) {
        for (let school in data[chart].data) {
            if (!schools.has(school)) {
                let option = document.createElement("option");
                option.innerText = school;

                school_filters.appendChild(option);

                schools.set(school, 1);
            }
        }
    }
}

const filterSchools = (data, filter) => {
    /** 
     * Filters certain schools data from the data
     * givin.
     * 
     * @param obj data
     * @param string filter
     * @return obj
     */
    let filtered_data = {};

    for (let school in data) {
        for (let date in data[school]) {
            if (filter == "All") {
                filtered_data[date] = data[school][date];
            }
            else if (filter == school) {
                filtered_data[date] = data[school][date];
            }
        }
    }

    return filtered_data;
}

class Table {
    constructor (headings, data, location) {
        this.table = document.createElement("table");
        this.container = document.createElement("div");
        this.container.classList.add("table-container");
        this.table.classList.add("table");
        this.header = document.createElement("tr");
        this.row = document.createElement("tr");

        this.constructHeader(headings);
        this.constructRow(data);
        this.display(location)
    }

    update (headings, data) {
        clearElement(this.header);
        clearElement(this.row);

        this.constructHeader(headings);
        this.constructRow(data);
    }

    display (location) {
        this.table.appendChild(this.header);
        this.table.appendChild(this.row);
        this.container.appendChild(this.table);
        location.appendChild(this.container);
    }

    constructHeader (headings) {
        for (let heading of headings) {
            let element = document.createElement("th");
    
            element.innerText = heading;
    
            this.header.appendChild(element);
        }
    }

    constructRow (data) {
        for (let item of data) {
            let element = document.createElement("td");
    
            element.innerText = item;
    
            this.row.appendChild(element);
        }
    }
}

const clearElement = (element) => {
    while(element.firstChild){
        element.removeChild(element.firstChild);
    }
}

get();