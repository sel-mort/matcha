var matchedUsersData = document.getElementById("matched-users-data").innerHTML;
var chatUsersContainer = document.getElementById("chat-users");
var messageContainer = document.getElementById("message-container");
var chatInput = document.getElementById("chat-input");
var send = document.getElementById("send");
var selectedUser = -1;
var selectedUserAvatar = "default";
var selectedUsername = null;
var alertSound = new Audio('../sounds/alert.mp3');

if (matchedUsersData)
    matchedUsersData = JSON.parse(matchedUsersData);

// Init matched users ui
function init_ui() {
    var res = "";
    for (var i = 0; i < matchedUsersData.length; i++) {
        var username = matchedUsersData[i][0][1];
        var id = matchedUsersData[i][0][0];
        var avatar = matchedUsersData[i][0][17];
        res += "<div value=" + id + " avatar = " + avatar + " username = " + username + " class='form-badge matched-user'><img class='app-small-image rounded inline-block' src='/public/images/avatars/" + avatar + ".png' onerror = this.src='/images/avatars/default.png';><p class='inline-block'>" + htmlEntities(username) + "</p></div>"
    }
    if (!res) {
        res = "<p class='center vertical-padding'>No matched users</p>";
    }
    chatUsersContainer.innerHTML = res;
    /*
    if (matchedUsersData.length) {
        selectedUser = parseInt(matchedUsersData[0][0][0]);
    }*/
}

init_ui();

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Set the selected user
var matchedUser = document.getElementsByClassName("matched-user");

for (var i = 0; i < matchedUser.length; i++) {
    matchedUser[i].addEventListener("click", function() {
        unselect();
        this.style.backgroundColor = "#111";
        this.style.color = "#FFF";
        selectedUser = this.getAttribute("value");
        selectedUsername = this.getAttribute("username");
        selectedUserAvatar = this.getAttribute("avatar");
        getMessage();
        connect();
    });
}

function unselect() {
    for (var i = 0; i < matchedUser.length; i++) {
        matchedUser[i].style.backgroundColor = "#FFF";
        matchedUser[i].style.color = "#000";
    }
}

// Post methode to get messages
function getMessage() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("user_id", selectedUser);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            /* alert(res); */
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                if (res.data != "undefined")
                    loadMessage(res);
                /* showAlerts(res);
                showErrors(res); */
            }
        }
    }
    xmlHttp.open("POST", "message");
    xmlHttp.send(formData);
}

// Load messages to the UI
function loadMessage(res) {
    var element = "";
    var el = "";
    if (res.data[0]) {
        for (var i = 0; i < res.data[0].length; i++) {
            var message = res.data[0][i][2];
            message = htmlEntities(message);
            //element += "<p>" + res.data[0][i][2] + "</p>";
            var sender = res.data[0][i][3];
            if (sender === selectedUser)
                el = "<div class='text-left'><div class='chat-message-box-left small-padding'><img class='app-small-image rounded inline-block float-left' src='/public/images/avatars/" + selectedUserAvatar + ".png' onerror = this.src='/images/avatars/default.png';><div class='chat-message inline-block fw500 '>" + message + "</div></div></div>";
            else
                el = "<div class='text-right'><div class='chat-message-box-right small-padding'><img class='app-small-image rounded inline-block float-right' src='/public/images/avatars/" + res.data[1][0][17] + ".png' onerror = this.src='/images/avatars/default.png';><div class='chat-message inline-block fw500 '>" + message + "</div></div></div>";
            element += el;
        }
    }
    messageContainer.innerHTML = element;
    messageContainer.scrollTop = messageContainer.scrollHeight;
}

// Send messages to database
function setMessage() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    formData.append("user_id", selectedUser);
    formData.append("message", chatInput.value);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            /* alert(res); */
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                getMessage();
                /* showAlerts(res);
                showErrors(res); */
            }
        }
    }
    xmlHttp.open("POST", "save");
    xmlHttp.send(formData);
}

// Connection to ws
var conn;

function connect() {
    try {
        conn = new WebSocket('ws://localhost:8080?username=' + selectedUsername);
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            //console.log(e.data);
            if (IsJsonString(e.data)) {
                var message = JSON.parse(e.data);
                if (selectedUsername == message[1])
                    addMessage(message[0]);
            }
        };
    } catch (err) {
        //  Block of code to handle errors
    }
}

send.addEventListener("click", function() {
    if (selectedUsername) {
        // Send message using ws
        conn.send(chatInput.value);
        // Set message in database
        setMessage();
    }
});

// Add UI message
function addMessage(message) {
    element = "<div class='text-left'><div class='chat-message-box-left small-padding'><img class='app-small-image rounded inline-block float-left' src='/public/images/avatars/" + selectedUserAvatar + ".png' onerror = this.src='/images/avatars/default.png';><div class='chat-message inline-block fw500 '>" + htmlEntities(message) + "</div></div></div>";
    messageContainer.innerHTML += element;
    messageContainer.scrollTop = messageContainer.scrollHeight;
    playAlertSound();
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function playAlertSound() {
    var p = alertSound.play();
    p.then((event) => {
        //console.log(event);
    }).catch((err) => {
        //console.log(err.message);
    });
}