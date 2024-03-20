document.addEventListener("DOMContentLoaded", function () {
    const map = L.map("map", {
        worldCopyJump: true
    }).setView([46.227638, 2.213749], 6);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Custom convention icon
    const conventionIcon = L.icon({
        iconUrl: iconConv,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
    });

    let user, data_popup, convention, data_popup_convention;
    let markers = L.markerClusterGroup();

    fetch(dataPath)
        .then(response => response.json())
        .then(data => {
            for (let i = 0; i < data.users.length; i++) {
                user = data.users[i];
                data_popup = user.popup;
                marker_user = L.marker([user.lat, user.lng]).bindPopup(data_popup);
                markers.addLayer(marker_user);
            }

            for (let i = 0; i < data.conventions.length; i++) {
                convention = data.conventions[i];
                data_popup_convention = convention.popup;
                marker_convention = L.marker([convention.lat, convention.lng], {
                    icon: conventionIcon
                }).bindPopup(data_popup_convention);
                markers.addLayer(marker_convention);
            }
        });

    map.addLayer(markers);
});