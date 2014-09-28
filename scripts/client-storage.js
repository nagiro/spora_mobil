storage = {
    storedObjects: 0
};

function initStorage() {
    for(var key in localStorage) {
        localStorage[key] = null;
    }
}

function storeObject(obj) {
    var result = $.each(obj, function(key, value) {
        var ret = (localStorage[key] !== null);

        localStorage[key] = value;

        return ret;
    });

    storage.storedObjects++;
}

function syncObjects() {

}
