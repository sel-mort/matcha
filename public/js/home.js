// Select load button
var load = document.getElementById("load");
// Loader management
var loader = document.getElementById("loader-container");

function showLoader(duration) {
    loader.style.display = "block";
    setTimeout(function() { loader.style.display = "none"; }, duration);
}

// DataSet of users
var resData = [];

var canScroll = 1;

function postData(index) {
    return new Promise(function(resolve) {
        var xmlHttp = new XMLHttpRequest();
        var formData = new FormData();
        formData.append("index", index);
        xmlHttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                var res = xmlHttp.responseText;
                if (IsJsonString(res)) {
                    res = JSON.parse(res);
                    if (res != "undefined") {
                        var users = res.data[0];
                        resData = resData.concat(users);
                        if (users.length > 0) {
                            // Set button value to the next index based on upp
                            load.value = resData.length;
                            loadData(resData);
                        }
                    }
                    canScroll = 1;
                    resolve(res);
                }
            }
        }
        xmlHttp.open("POST", "search");
        xmlHttp.send(formData);
    });
}

// First post
postData(0);
// Post after clicking on the post button
load.addEventListener("click", function() {
    var index = resData.length;
    postData(index);
});
// Load data by scrolling
window.onscroll = function(ev) {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        if (canScroll) {
            canScroll = 0;
            var index = resData.length;
            postData(index);
        }
    }
};
// Defining data container
var resContainer = document.getElementById("res-container");

function loadData(resArray) {
    showLoader(resArray.length * 1000);
    var data = ""
    for (var i = 0; i < resArray.length; i++) {
        if (resArray[i]) {
            var username = resArray[i][1];
            var age = getAge(resArray[i][6]);
            var avatar = resArray[i][17];
            if (!avatar)
                avatar = "default";
            data += "<a href=" + "/user?username=" + username + "><div class='form-card' style='animation-delay:" + i / 10 + "s;'><img class='form-card-image' src='/images/avatars/" + avatar + ".png' onerror = this.src='/images/avatars/default.png';><div class='form-card-gradient'></div><div class='form-card-info'><p class='form-card-title'>" + username + "</p><p class='form-card-text'>" + age + " years old</p><button class='form-card-button'>View profile</button></div></div></a>";

        }
    }
    resContainer.innerHTML = data;
}
// Function to get age from birthdate
function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

// Sort management
var sortAge = document.getElementById("sort-age");
var sortLocation = document.getElementById("sort-location");
var sortScore = document.getElementById("sort-score");
var sortInterest = document.getElementById("sort-interest");

sortAge.addEventListener("click", function() {
    resData.sort(dynamicSort(6, -1));
    loadData(resData);
});

sortScore.addEventListener("click", function() {
    resData.sort(dynamicSort(12, -1));
    loadData(resData);
});

sortLocation.addEventListener("click", function() {
    resData.sort(dynamicSort(20, 1));
    loadData(resData);
});

sortInterest.addEventListener("click", function() {
    resData.sort(dynamicSort(18, -1));
    loadData(resData);
});

function dynamicSort(property, sortOrder) {
    // 1 DESC -1 ASC
    return function(a, b) {
        /* next line works with strings and numbers, 
         * and you may want to customize it to your needs
         */
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
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