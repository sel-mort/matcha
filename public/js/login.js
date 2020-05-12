const username = document.getElementById("username");
const password = document.getElementById("password");

const login = document.getElementById("login");
const dataSection = document.getElementById("data-section");

// Post
login.addEventListener("click", function() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("username", username.value);
    formData.append("password", password.value);
    formData.append("longitude", longitude);
    formData.append("latitude", latitude);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            /* alert(res); */
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                showAlerts(res);
                showErrors(res);
                if (this.status === 200)
                    window.location.href = "/profile";
            }
        }
    }
    xmlHttp.open("POST", "login");
    xmlHttp.send(formData);
});

// UI Data management
function showErrors(res) {
    if (res.error.length) {
        dataSection.innerHTML = "";
        var errors = "";
        for (var i = 0; i < res.error.length; i++) {
            errors += "<div class='temp-error-window'>" + res.error[i] + "</div>"
        }
        dataSection.innerHTML += errors;
    }
}

function showAlerts(res) {
    if (res.alert.length) {
        dataSection.innerHTML = "";
        var alerts = "";
        for (var i = 0; i < res.alert.length; i++) {
            alerts += "<div class='temp-alert-window'>" + res.alert[i] + "</div>"
        }
        dataSection.innerHTML += alerts;
    }
}


// Get user location using IP + (ipapi)

var longitude;
var latitude;
var request = new XMLHttpRequest();
request.open('GET', 'https://ipapi.co/json/', true);

request.onload = function() {
    if (request.status >= 200 && request.status < 400) {
        // Success!
        var data = JSON.parse(request.responseText);
        ip = data.ip;
        longitude = data.longitude;
        latitude = data.latitude;
    } else {
        // We reached our target server, but it returned an error
    }
};

request.onerror = function() {
    // There was a connection error of some sort
};

request.send();

/* longitude = "-6.8887306";
latitude = "32.8781004"; */

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}