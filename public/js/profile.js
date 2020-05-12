const interest = document.getElementById("interest");
var interestList = document.getElementById("interest-list");

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

interest.addEventListener("keyup", parseInterest);

function parseInterest() {
    var tags = "";
    if (interest.value.trim() !== '') {
        var res = interest.value;
        var res = res.split("#");
        if (res.length > 0) {
            tags = "<label class='form-label' for='interest'>Tags</label>";
            for (var i = 0; i < res.length; i++) {
                if (res[i].trim() !== '')
                    tags += "<div class='form-tag'>" + htmlEntities(res[i].toLowerCase()) + "</div>";
            }
        }
    }
    interestList.innerHTML = tags;
}

const avatar = document.getElementById("avatar");
const file = document.getElementById("file");
const addFile = document.getElementById("add-file");
// File input management
addFile.addEventListener("click", function() {
    file.click();
});

file.onchange = function(evt) {
    var tgt = evt.target || window.event.srcElement,
        files = tgt.files;
    if (FileReader && files && files.length) {
        var fr = new FileReader();
        fr.onload = function() {
            if (isBase64(fr.result.split(',')[1])) {
                avatar.src = fr.result;
                avatar.style.height = avatar.clientWidth + "px";
            }
        }
        fr.readAsDataURL(files[0]);
    }
}

window.onresize = function(event) {
    avatar.style.height = avatar.clientWidth + "px";
};

avatar.onload = function() {
    avatar.style.height = avatar.clientWidth + "px";
}

// Avatar error management
avatar.addEventListener("error", function() {
    this.src = "../images/user.png";
});

// Location management

var position = document.getElementById("add-position");
var latitude = document.getElementById("latitude");
var longitude = document.getElementById("longitude");

position.addEventListener("click", function() {
    getLocation();
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        /* x.innerHTML = "Geolocation is not supported by this browser."; */
    }
}

function showPosition(position) {
    /* x.innerHTML = "Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude; */
    latitude.value = position.coords.latitude;
    longitude.value = position.coords.longitude;
}



// Data Post management
const save = document.getElementById("save");
const username = document.getElementById("username");
const firstName = document.getElementById("firstname");
const lastName = document.getElementById("lastname");
const password = document.getElementById("password");
const email = document.getElementById("email");
const age = document.getElementById("age");
const gender = document.getElementsByName("gender");
const orientation = document.getElementsByName("orientation");
const bio = document.getElementById("bio");
const score = document.getElementById("score");

save.addEventListener("click", function() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("username", username.value);
    formData.append("firstname", firstname.value);
    formData.append("lastname", lastname.value);
    formData.append("password", password.value);
    formData.append("email", email.value);
    formData.append("age", age.value);
    formData.append("bio", bio.value);
    formData.append("file", file.files[0]);
    formData.append("female-gender", gender[0].checked);
    formData.append("male-gender", gender[1].checked);
    formData.append("female-orientation", orientation[0].checked);
    formData.append("male-orientation", orientation[1].checked);
    formData.append("both-orientation", orientation[2].checked);
    formData.append("interest", interest.value);
    formData.append("latitude", latitude.value);
    formData.append("longitude", longitude.value);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                showAlerts(res);
                showErrors(res);
                file.value = "";
            }
        }
    }
    xmlHttp.open("POST", "profile");
    xmlHttp.send(formData);
});

// UI Data management
const dataSection = document.getElementById("data-section");

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

var user_profile_data = document.getElementById("user-profile-data").innerHTML;
user_profile_data = JSON.parse(user_profile_data);


function fill_inputs() {
    username.value = user_profile_data.username;
    firstName.value = user_profile_data.firstname;
    lastName.value = user_profile_data.lastname;
    email.value = user_profile_data.email;
    age.value = user_profile_data.age;
    bio.value = user_profile_data.bio;
    score.innerHTML = user_profile_data.score + " point(s)";

    for (var i = 0; i < user_profile_data.interest.length; i++) {
        interest.value += "#" + user_profile_data.interest[i] + " ";
    }
    parseInterest();

    if (user_profile_data.avatar)
        avatar.src = "/public/images/avatars/" + user_profile_data.avatar + ".png";
    if (user_profile_data.gender == 0)
        gender[0].checked = true;
    else
        gender[1].checked = true;
    if (user_profile_data.orientation == 0)
        orientation[0].checked = true;
    else if (user_profile_data.orientation == 1)
        orientation[1].checked = true;
    else {
        orientation[2].checked = true;
    }
}

fill_inputs();

// Basic test of base64 validation
function isBase64(str) {
    if (str != undefined) {
        if (str === '' || str.trim() === '') { return false; }
        try {
            return btoa(atob(str)) == str;
        } catch (err) {
            return false;
        }
    }
    return false;
}