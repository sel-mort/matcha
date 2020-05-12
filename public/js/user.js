// UI Data management
const dataSection = document.getElementById("data-section");

function showErrors(res) {
    if (res.error)
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
    if (res.alert)
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

const username = document.getElementById("username");
const firstName = document.getElementById("firstname");
const lastName = document.getElementById("lastname");
const age = document.getElementById("age");
const gender = document.getElementById("gender");
const orientation = document.getElementById("orientation");
const bio = document.getElementById("bio");
const score = document.getElementById("score");
const interest = document.getElementById("interest");
const like = document.getElementById("like");
const report = document.getElementById("report");
const block = document.getElementById("block");
const connection_status = document.getElementById("connection-status");
const liked_status = document.getElementById("liked-status");

function fill_inputs() {
    username.textContent = user_profile_data.username;
    firstName.textContent = user_profile_data.firstname;
    lastName.textContent = user_profile_data.lastname;
    age.textContent = user_profile_data.age;
    bio.textContent = user_profile_data.bio;
    score.textContent = user_profile_data.score + " point(s)";
    if (user_profile_data.online == "0")
        connection_status.innerHTML = "<div class='red-dot'></div> " + user_profile_data.last_connection;
    else {
        connection_status.innerHTML = "<div class='green-dot'></div>  Connected";
    }

    if (user_profile_data.avatar)
        avatar.src = "/public/images/avatars/" + user_profile_data.avatar + ".png";
    if (user_profile_data.gender == 0)
        gender.textContent = "Female";
    else
        gender.textContent = "Male";
    if (user_profile_data.orientation == 0)
        orientation.textContent = "Female";
    else if (user_profile_data.orientation == 1)
        orientation.textContent = "Male";
    else {
        orientation.textContent = "Both";
    }
    if (user_profile_data.like == 1)
        like.classList.add("active");
    if (user_profile_data.report == 1)
        report.classList.add("active");
    if (user_profile_data.block == 1)
        block.classList.add("active");
    if (user_profile_data.liked == 0) {
        liked_status.style.display = "none";
    }
    getInterest(user_profile_data.interest);
}

fill_inputs();

window.onresize = function(event) {
    avatar.style.height = avatar.clientWidth + "px";
};

avatar.onload = function() {
    avatar.style.height = avatar.clientWidth + "px";
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function getInterest(str) {
    var tags = "";
    if (str.length > 0) {
        tags = "<label class='form-label' for='interest'>Tags</label>";
        for (var i = 0; i < str.length; i++) {
            if (str[i].trim() !== '')
                tags += "<div class='form-tag'>" + htmlEntities(str[i].toLowerCase()) + "</div>";
        }
    }
    interest.innerHTML = tags;
}

// Post
// The variable (done) to check if like, block, report done or no 

var done = 0;
like.addEventListener("click", async function() {
    await linkUser(username.textContent, this.name);
    if (done) {
        if (this.classList.contains("active"))
            this.classList.remove("active");
        else {
            this.classList.add("active");
            block.classList.remove("active");
        }
    }
});

report.addEventListener("click", async function() {
    await linkUser(username.textContent, this.name);
    if (done) {
        if (this.classList.contains("active"))
            this.classList.remove("active");
        else
            this.classList.add("active");
    }
});

block.addEventListener("click", async function() {
    await linkUser(username.textContent, this.name);
    if (done) {
        if (this.classList.contains("active"))
            this.classList.remove("active");
        else {
            this.classList.add("active");
            like.classList.remove("active");
        }
    }
});

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function linkUser(username, action) {
    return new Promise(function(resolve) {
        var xmlHttp = new XMLHttpRequest();
        var formData = new FormData();
        formData.append("action", action);
        formData.append("username", username);
        xmlHttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                var res = xmlHttp.responseText;
                if (IsJsonString(res)) {
                    if (res)
                        res = JSON.parse(res);
                    showAlerts(res);
                    showErrors(res);
                    if (res.error.length)
                        done = 0;
                    else
                        done = 1;
                    resolve(done);
                }
            }
        }
        xmlHttp.open("POST", "user");
        xmlHttp.send(formData);
    });
}