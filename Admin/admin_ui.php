<?php


function displayNav($activePage){
    echo "
    <aside>
        <nav class='side-nav flex-c center'>
            
            <!-- MOBILE TOP BAR -->
            <div class='mobile-nav-bar'>
                <button id='hamburger-btn' class='hamburger hidden' onclick='toggleMobileNav()'>
                    ☰
                </button >

                <div class='mobile-logo'>
                    <img class='logo' src='logo.png'>
                    <strong><h2>TAARA Admin</h2></strong>
                </div>
            </div>

            <!-- NAV LINKS -->
            <div id='nav-links' class='nav-links flex-c' style='row-gap: 20px;'>
                <a href='index.php' class='".($activePage == "dashboard" ? "active" : "")."'>Dashboard</a>
                <a href='animals-records.php' class='".($activePage == "animals" ? "active" : "")."'>Animals</a>
                <a href='adoptions-records.php' class='".($activePage == "adoptions" ? "active" : "")."'>Adoptions</a>
                <a href='donations-topdonors.php' class='".($activePage == "donations" ? "active" : "")."'>Donations</a>
                <a href='inventory.php' class='".($activePage == "inventory" ? "active" : "")."'>Inventory</a>
                <a href='reports-rescue.php' class='".($activePage == "reports" ? "active" : "")."'>Reports</a>
                <a href='volunteers-records.php' class='".($activePage == "volunteers" ? "active" : "")."'>Volunteers</a>
                <a href='events-upcoming.php' class='".($activePage == "events" ? "active" : "")."'>Events</a>
                <a href='../includes/logout.php'>Logout</a>
            </div>

        </nav>
    </aside>";
}
?>

