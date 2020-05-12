var notificationSound = new Audio('../sounds/notification.mp3');

// Post
function post() {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4) {
            var res = xmlHttp.responseText;
            /* alert(res); */
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                if (res.data[0] != "undefined") {
                    if (res.data[0] != 0) {
                        //console.log(res.data[0]);
                        showAlert();
                        playNotificationSound();
                        //clearInterval(sI);
                    } else {
                        hideAlert();
                    }
                }
            }
        }
    }
    xmlHttp.open("POST", "alert");
    xmlHttp.send(formData);
};

var sI = setInterval(function() { post(); }, 3000);

var notificationAlertContainer = document.getElementById("notification-alert");

function showAlert() {
    notificationAlertContainer.innerHTML = "<a href='/notification'><button id='notification-link' class='small-button' title='New notification'><i class='material-icons'>notification_important</i></button></a>";
}

function hideAlert() {
    notificationAlertContainer.innerHTML = "";
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function playNotificationSound() {
    var p = notificationSound.play();
    p.then((event) => {
        //console.log(event);
    }).catch((err) => {
        //console.log(err.message);
    });
}