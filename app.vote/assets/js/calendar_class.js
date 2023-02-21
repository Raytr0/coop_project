class Calendar {
    constructor () {
        this.date = new Date();
        this.year = this.date.getFullYear();
        this.month = this.date.getMonth();
        this.title = document.querySelector("#date");
        this.calendar = document.querySelector(".calendar-body");
        this.pop_up = null;
    }

    increaseMonth() {}
    decreaseMonth() {}
    addElection() {}
    addPopUp(cell) {
        if (this.pop_up != null) {
            this.pop_up.remove();
        }
    }
    getElections() {}
    updateCalendar() {}
}

class PopUp {
    constructor (cell) {}

    positionPopUp() {
        let viewport_offset = this.cell.getBoundingClientRect();
        let navbar = document.querySelector("#navbar");
        this.pop_up.style.top = viewport_offset.top - (viewport_offset.height / 8) + window.scrollY + "px";
    
        if (parseInt(window.getComputedStyle(navbar, null)["left"]) < 0) {
            this.pop_up.style.left = viewport_offset.left - (100 - viewport_offset.width / 2) + "px";
        }

        else {
            this.pop_up.style.left = viewport_offset.left - (100 - viewport_offset.width / 2) - navbar.getBoundingClientRect().width + "px";
        }
    }
}