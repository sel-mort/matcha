// Get verification code param
function getParam(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function(item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

const verificationCode = getParam("verification_code");
const dataSection = document.getElementById("data-section");

// Post
function post() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("verification_code", verificationCode);
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
    xmlHttp.open("POST", "activate");
    xmlHttp.send(formData);
};

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

post();

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}