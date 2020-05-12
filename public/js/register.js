const username = document.getElementById("username");
const firstname = document.getElementById("firstname");
const lastname = document.getElementById("lastname");
const password = document.getElementById("password");
const email = document.getElementById("email");

const register = document.getElementById("register");
const dataSection = document.getElementById("data-section");

// Post
register.addEventListener("click", function() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("username", username.value);
    formData.append("firstname", firstname.value);
    formData.append("lastname", lastname.value);
    formData.append("password", password.value);
    formData.append("email", email.value);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            /* alert(res); */
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                showAlerts(res);
                showErrors(res);
            }
        }
    }
    xmlHttp.open("POST", "register");
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

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}