<?php
// Checks if page has been requested with get and was not included
if ($_SERVER["REQUEST_METHOD"] == "GET" && !defined("INCLUDED"))
{
    // Specifing that it will return json format
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: http://localhost");

    // Makes sure that there is a country variable in the url
    if (!isset($_GET["country"]))
    {
        // Returns nothing
        echo NULL;
    }
    else
    {
        $provinces = [
            "Alberta",
            "British Columbia",
            "Manitoba",
            "New Brunswick",
            "Newfoundland and Labrador",
            "Nova Scotia",
            "Ontario",
            "Prince Edward Island",
            "Quebec",
            "Saskatchewan",
            "Northwest Territories",
            "Nunavut",
            "Yukon",
        ];
        
        $states = [
            "Alabama",
            "Alaska",
            "Arizona",
            "Arkansas",
            "California",
            "Colorado",
            "Connecticut",
            "Delaware",
            "District of Columbia",
            "Florida",
            "Georgia",
            "Hawaii",
            "Idaho",
            "Illinois",
            "Indiana",
            "Iowa",
            "Kansas",
            "Kentucky",
            "Louisiana",
            "Maine",
            "Maryland",
            "Massachusetts",
            "Michigan",
            "Minnesota",
            "Mississippi",
            "Missouri",
            "Montana",
            "Nebraska",
            "Nevada",
            "New Hampshire",
            "New Jersey",
            "New Mexico",
            "New York",
            "North Carolina",
            "North Dakota",
            "Ohio",
            "Oklahoma",
            "Oregon",
            "Pennsylvania",
            "Rhode Island",
            "South Carolina",
            "South Dakota",
            "Tennessee",
            "Texas",
            "Utah",
            "Vermont",
            "Virginia",
            "Washington",
            "West Virginia",
            "Wisconsin",
            "Wyoming",
        ];

        if ($_GET["country"] == "canada")
        {
            // Returns json encoded provinces
            echo json_encode($provinces);
        }

        else if ($_GET["country"] == "united-states")
        {
            // Returns json encoded provinces
            echo json_encode($states);
        }
    }
}
?>