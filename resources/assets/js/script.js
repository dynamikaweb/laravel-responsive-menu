document.addEventListener('DOMContentLoaded', function () {
    var menu = document.getElementById('dl-menu');
    if (menu && !menu.dlmenu) {
        new DLMenu(menu);
    }
});
