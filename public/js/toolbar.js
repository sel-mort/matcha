var toolbarMenu = document.getElementById("vertical-toolbar-menu");
var verticalToolbarList = document.getElementById("vertical-toolbar-list");

toolbarMenu.addEventListener("click", function() {
    if (getComputedStyle(verticalToolbarList, null).display == "block")
        hideVerticalToolbar();
    else
        showVerticalToolbar();
});

function showVerticalToolbar() {
    verticalToolbarList.style.display = "block";
}

function hideVerticalToolbar() {
    verticalToolbarList.style.display = "none";
}