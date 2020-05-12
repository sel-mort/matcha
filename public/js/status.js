var user_profile_data = document.getElementById("user-profile-data").innerHTML;
user_profile_data = JSON.parse(user_profile_data);

function getContent(connectionStatus) {
    var xmlHttp = new XMLHttpRequest();
    var formData = new FormData();
    if (connectionStatus)
        formData.append("client_connection_status", connectionStatus);
    formData.append("username", user_profile_data.username);
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var res = xmlHttp.responseText;
            //console.log(res);
            if (IsJsonString(res)) {
                res = JSON.parse(res);
                //dataSection.innerText = res.serverConnectionStatus;
                //getContent(res.serverConnectionStatus);
                update_ui(res);
                getContent();
            }
        }
    }
    xmlHttp.open("POST", "status");
    xmlHttp.send(formData);
}

getContent();

function update_ui(res) {
    if (res.serverConnectionStatus == 0) {
        connection_status.innerHTML = "<div class='red-dot'></div> " + res.lastConnection;
    } else {
        connection_status.innerHTML = "<div class='green-dot'></div>  Connected";
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