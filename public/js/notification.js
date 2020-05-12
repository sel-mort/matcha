var notificationData = document.getElementById("notification-data").textContent;
var dataContainer = document.getElementById("data-container");


function init_ui() {
    if (notificationData) {
        notificationData = JSON.parse(notificationData);
        var res = "";
        for (var i = 0; i < notificationData.length; i++) {
            var action = notificationData[i][1];
            var activated = parseInt(notificationData[i][2]);
            var actionMessage = "";
            if (action == "unlike")
                actionMessage = "Unliked you";
            else if (action == "like")
                actionMessage = "Liked you";
            else if (action == "report")
                actionMessage = "Reported you";
            else if (action == "block")
                actionMessage = "Blocked you";
            else if (action == "message")
                actionMessage = "Messaged you";
            else
                actionMessage = "Visited you";

            if (activated === 1) {
                res += "<div class='form big-form centered small-vertical-margin '> <i class = 'material-icons' >notification_important </i> <h3 class = 'fw500' > " + htmlEntities(notificationData[i][0]) + " </h3>" + actionMessage + "</div>";
            } else {
                res += "<div class='form big-form centered small-vertical-margin ' style='opacity:.5'> <p>Seen</p> <i class = 'material-icons' >notification_important </i> <h3 class = 'fw500' > " + htmlEntities(notificationData[i][0]) + " </h3>" + actionMessage + "</div>";
            }
        }
        if (!res)
            res = "<p class='center vertical-padding'>No notifications to show</p>";
        dataContainer.innerHTML = res;
    }
}

init_ui();

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}