// Search post management
var age = document.getElementById("age");
var score = document.getElementById("score");
var distance = document.getElementById("distance");
var longitude = document.getElementById("longitude");
var latitude = document.getElementById("latitude");

// Loader management
var loader = document.getElementById("loader-container");

function showLoader(duration) {
    loader.style.display = "block";
    setTimeout(function() { loader.style.display = "none"; }, duration);
}

// Select search button
var search = document.getElementById("search");

// Select load button
var load = document.getElementById("load");

// DataSet of users
var resData = [];

function searchData(index, age, score, distance, interest, longitude, latitude) {
    return new Promise(function(resolve) {
        var xmlHttp = new XMLHttpRequest();
        var formData = new FormData();
        formData.append("index", index);
        formData.append("age", age);
        formData.append("score", score);
        formData.append("distance", distance);
        formData.append("interest", interest);
        formData.append("longitude", longitude);
        formData.append("latitude", latitude);
        xmlHttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                var res = xmlHttp.responseText;
                if (IsJsonString(res)) {
                    res = JSON.parse(res);
                    if (res.data != "undefined") {
                        var users = res.data[0];
                        resData = users;
                        if (users.length > 0) {
                            loadData(resData);
                        }
                    }
                }
                canScroll = 1;
                resolve(res);
            }
        }
        xmlHttp.open("POST", "search");
        xmlHttp.send(formData);
    });
}

var canScroll = 0;

function postData(index, age, score, distance, interest, longitude, latitude) {
    return new Promise(function(resolve) {
        var xmlHttp = new XMLHttpRequest();
        var formData = new FormData();
        formData.append("index", index);
        formData.append("age", age);
        formData.append("score", score);
        formData.append("distance", distance);
        formData.append("interest", interest);
        formData.append("longitude", longitude);
        formData.append("latitude", latitude);
        xmlHttp.onreadystatechange = function() {
            if (this.readyState == 4) {
                var res = xmlHttp.responseText;
                if (IsJsonString(res)) {
                    res = JSON.parse(res);
                    if (res.data != "undefined") {
                        var users = res.data[0];
                        resData = resData.concat(users);
                        if (users.length > 0) {
                            // Set button value to the next index based on upp
                            load.value = resData.length;
                            loadData(resData);
                        }
                    }
                }
                canScroll = 1;
                resolve(res);
            }
        }
        xmlHttp.open("POST", "search");
        xmlHttp.send(formData);
    });
}
// Post after clicking on the post button
load.addEventListener("click", function() {
    var index = resData.length;
    postData(index, age.value, score.value, distance.value, interest.value, longitude.value, latitude.value);
});
// Load data by scrolling
window.onscroll = async function(ev) {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        var index = resData.length;
        if (canScroll) {
            canScroll = 0;
            await postData(index, age.value, score.value, distance.value, interest.value, longitude.value, latitude.value);
        }
    }
};
// Defining data container
var resContainer = document.getElementById("res-container");

function loadData(resArray) {
    showLoader(resArray.length * 100);
    var data = "";
    for (var i = 0; i < resArray.length; i++) {
        if (resArray[i]) {
            var username = resArray[i][1];
            var age = getAge(resArray[i][6]);
            var avatar = resArray[i][17];
            if (!avatar)
                avatar = "default";
            //data += "<div class='form-card' style='animation-delay:" + i / 10 + "s;'><img class='form-card-image' src='/images/avatars/" + avatar + ".png'><div class='form-card-gradient'></div><div class='form-card-info'><p class='form-card-title'>" + username + "</p><p class='form-card-text'>" + age + " years old</p><button class='form-card-button'>View profile</button></div></div>";
            data += "<a href=" + "/user?username=" + username + "><div class='form-card' style='animation-delay:" + i / 10 + "s;'><img class='form-card-image' src='/images/avatars/" + avatar + ".png'><div class='form-card-gradient'></div><div class='form-card-info'><p class='form-card-title'>" + username + "</p><p class='form-card-text'>" + age + " years old</p><button class='form-card-button'>View profile</button></div></div></a>";
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

// Search UI management
var formRange = document.getElementsByClassName("form-range");
var formRangeValue = document.getElementsByClassName("form-range-value");

for (var i = 0; i < formRange.length; i++) {
    formRange[i].addEventListener("change", function() {
        var valueId = this.getAttribute("valueId");
        formRangeValue[valueId].innerHTML = this.value;
    });
}


var interest = document.getElementById("interest");
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

search.addEventListener("click", function() {
    var resData = [];
    resContainer.innerHTML = "";
    searchData(0, age.value, score.value, distance.value, interest.value, longitude.value, latitude.value);
});

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

// Map UI management
var mapBox = document.getElementById("map-box");
var mapBoxShow = document.getElementById("map-box-show");
mapBoxShow.style.cursor = "pointer";

function showMap() {
    mapBox.style.display = "block";
}

function hideMap() {
    mapBox.style.display = "none";
    longitude.value = "";
    latitude.value = "";
}

hideMap();

var mapShown = 0;

mapBoxShow.addEventListener("click", function() {
    if (mapShown) {
        hideMap();
        mapBoxShow.innerHTML = "Custom position ?";
        mapShown = 0;
    } else {
        showMap();
        mapBoxShow.innerHTML = "Back to you position";
        mapShown = 1;
    }
});