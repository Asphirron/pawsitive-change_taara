<?php
function displayNav($activePage){
    echo "
    <aside>
        <nav class='side-nav flex-c'>
            
            <!-- MOBILE TOP BAR -->
            <div class='mobile-nav-bar'>
                <button id='hamburger-btn' class='hamburger hidden' onclick='toggleMobileNav()'>☰</button>
                <div class='mobile-logo'>
                    <img class='logo' src='logo.png'>
                    <strong><h2>TAARA Admin</h2></strong>
                </div>
            </div>

            <div id='nav-links' class='nav-links flex-c' style='row-gap: 20px;'>

                <a href='index.php' class='".($activePage == "dashboard" ? "active" : "")."'>Dashboard</a>

                <!-- ANIMALS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "animals" ? "active" : "")."'>
                        Animals
                    </button>
                    <div class='dropdown-content'>
                        <a href='animals-records.php'>Animals Records</a>
                        <a href='animals-activities.php'>Activity Log</a>
                        <a href='animals-vaccinations.php'>Vaccinations</a>
                    </div>
                </div>

                <!-- ADOPTIONS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "adoptions" ? "active" : "")."'>
                        Adoptions
                    </button>
                    <div class='dropdown-content'>
                        <a href='adoptions-records.php'>Adoption Records</a>
                        <a href='adoptions-application.php'>Adoption Applications</a>
                        <a href='adoptions-screening.php'>Adoption Screening</a>
                    </div>
                </div>

               <!-- DONATIONS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "donations" ? "active" : "")."'>
                        Donations
                    </button>
                    <div class='dropdown-content'>
                        <a href='donations-topdonors.php'>Top Donors</a>
                        <a href='donations-monetary.php'>Monetary Donation</a>
                        <a href='donations-inkind.php'>Inkind Donation</a>
                    </div>
                </div>

                <a href='inventory.php' class='".($activePage == "inventory" ? "active" : "")."'>Inventory</a>

                <!-- REPORTS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "reports" ? "active" : "")."'>
                        Reports
                    </button>
                    <div class='dropdown-content'>
                        <a href='reports-rescue.php'>Rescue Reports</a>
                        <a href='reports-poi.php'>Points of Interest</a>
                        <a href='reports-map.php'>Interactive Map</a>
                    </div>
                </div>

                <!-- VOLUNTEERS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "events" ? "active" : "")."'>
                        Volunteers
                    </button>
                    <div class='dropdown-content'>
                        <a href='volunteers-records.php'>Volunteer Records</a>
                        <a href='volunteers-application.php'>Volunteer Application</a>
                    </div>
                </div>

                <!-- EVENTS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "events" ? "active" : "")."'>
                        Events
                    </button>
                    <div class='dropdown-content'>
                        <a href='events-upcoming.php'>Event Records</a>
                        <a href='events-calendar.php'>Event Calendar</a>
                    </div>
                </div>

                <a href='../includes/logout.php'>Logout</a>

            </div>
        </nav>
    </aside>";
}

?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    let dropdownButtons = document.querySelectorAll(".dropdown-btn");

    dropdownButtons.forEach(button => {
        button.addEventListener("click", function() {
            const parent = this.parentElement;
            parent.classList.toggle("show");
        });
    });
});
</script>
