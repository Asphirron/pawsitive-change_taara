
function openMapModal(element_id){
    alert(element_id);
}

/*const modal = document.getElementById("mapModal");
  const confirmBtn = document.getElementById("confirmLocation");

  document.querySelectorAll(".open-map").forEach(btn => {
    btn.addEventListener("click", () => {
      modal.style.display = "block";
      activeButton = btn;
      google.maps.event.trigger(map, "resize"); // Fix display issues
      map.setCenter({ lat: 14.5995, lng: 120.9842 });
    });
  });

  confirmBtn.addEventListener("click", () => {
    if (selectedCoords && activeButton) {
      activeButton.innerText = `${selectedCoords.lat().toFixed(6)}, ${selectedCoords.lng().toFixed(6)}`;
      modal.style.display = "none";
    }
  });

  // Close modal on outside click
  window.onclick = (e) => {
    if (e.target == modal) {
      modal.style.display = "none";
    }
  };

  window.onload = initMap;

  */