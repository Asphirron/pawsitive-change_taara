<?php
function displayNav($activePage){
    echo "
    <aside>
        <nav class='side-nav flex-c'>
            
            <!-- MOBILE TOP BAR -->
            <div class='flex-c center'>
                <div class='mobile-logo'>
                    <img class='logo' src='logo.png'>
                    <strong><h2>TAARA Admin</h2></strong>
                </div>
            </div>

            <div id='nav-links' class='nav-links flex-c'>


                <!-- DASHBOARD DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "index" ? "active" : "")."'>
                        <i class='fa fa-home nav-icon'></i> Dashboard 
                    </button>
                    <div class='dropdown-content'>
                        <a href='index.php'>Go to Dashboard</a>
                    </div>
                </div>

                <!-- ANIMALS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "animals" ? "active" : "")."'>
                        <i class='fa fa-paw nav-icon'></i> Animals 
                    </button>
                    <div class='dropdown-content'>
                        <a href='animals-records.php'>Records</a>
                        <a href='animals-activities.php'>Activity Log</a>
                        <a href='animals-vaccinations.php'>Vaccinations</a>
                    </div>
                </div>

                <!-- ADOPTIONS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "adoptions" ? "active" : "")."'>
                        <i class='fa fa-heart nav-icon'></i> Adoptions
                    </button>
                    <div class='dropdown-content'>
                        <a href='adoptions-records.php'>Records</a>
                        <a href='adoptions-application.php'>Applications</a>
                        <a href='adoptions-screening.php'>Screening</a>
                    </div>
                </div>

                <!-- DONATIONS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "donations" ? "active" : "")."'>
                        <i class='fa fa-hand-holding-usd nav-icon'></i> Donations
                    </button>
                    <div class='dropdown-content'>
                        <a href='donations-topdonors.php'>Top Donors</a>
                        <a href='donations-monetary.php'>Monetary</a>
                        <a href='donations-inkind.php'>In-kind</a>
                    </div>
                </div>

                <!-- INVENTORY DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "inventory" ? "active" : "")."'>
                        <i class='fa fa-box nav-icon'></i> Inventory
                    </button>
                    <div class='dropdown-content'>
                        <a href='inventory.php'>Records</a>
                    </div>
                </div>

                <!-- REPORTS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "reports" ? "active" : "")."'>
                        <i class='fa fa-file-alt nav-icon'></i> Reports
                    </button>
                    <div class='dropdown-content'>
                        <a href='reports-rescue.php'>Rescue</a>
                        <a href='reports-poi.php'>Points of Interest</a>
                        <a href='reports-map.php'>Map</a>
                    </div>
                </div>

                <!-- VOLUNTEERS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "volunteers" ? "active" : "")."'>
                        <i class='fa fa-users nav-icon'></i> Volunteers
                    </button>
                    <div class='dropdown-content'>
                        <a href='volunteers-records.php'>Records</a>
                        <a href='volunteers-application.php'>Applications</a>
                    </div>
                </div>

                <!-- EVENTS DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "events" ? "active" : "")."'>
                        <i class='fa fa-calendar nav-icon'></i> Events
                    </button>
                    <div class='dropdown-content'>
                        <a href='events-upcoming.php'>Records</a>
                        <a href='events-calendar.php'>Calendar</a>
                    </div>
                </div>

                <!-- LOGOUT DROPDOWN -->
                <div class='dropdown'>
                    <button class='dropdown-btn ".($activePage == "logout" ? "active" : "")."'>
                        <i class='fa fa-sign-out-alt nav-icon'></i> Logout
                    </button>
                    <div class='dropdown-content'>
                        <a href='../includes/logout.php'>Are you sure?</a>
                    </div>
                </div>

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
