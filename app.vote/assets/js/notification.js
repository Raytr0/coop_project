const notification_menu = document.getElementById("notification-menu");
//let notifications = loadNotifications();

const closeNotification = () => {
};

const displayNotification = () => {

};

const setNotification = (notification) => {
    /**
     * Adds notification to the global notification
     * obj and browser cookie.
     * 
     * @param obj notification
     */

    
};

const loadCookies = () => {
    /**
     * Returns object that represents the cookies
     * in the browser.
     * 
     * @return object
     */

    let cookie_obj = {};

    // Splits the cookies
    let cookies = "hello=5; ".split('; ');

    // Iterates over cookies
    for (let cookie of cookies)
    {   
        let [key, value] = cookie.split('=');
        
        // Adds to cookie object
        if (key) cookie_obj[key] = value;
    }

    // Returns notifications object
    return cookie_obj;
};

const loadNotifications = () => {
    /**
     * Returns the notifications that are 
     * stored in the notification cookie.
     * 
     * @return object
     */

    return JSON.parse(loadCookies()["notifications"]);
};

const updateNotification = (notifications) => {
    /**
     * Sets the notification cookie to the JSON
     * representation of notifications.
     * 
     * @return object
     */

    document.cookie = "notifications=" + JSON.stringify(notifications);
};

notification_menu.children[0].addEventListener("click", (event) => {
    event.stopPropagation();
    event.preventDefault();
});

notification_menu.children[1].addEventListener("click", (event) => {
    event.stopPropagation();
    event.preventDefault();
    
    document.getElementById("notification-dropdown").classList.remove("show");
    notification_menu.classList.remove("show");

    document.cookie = "notifications={}";
});