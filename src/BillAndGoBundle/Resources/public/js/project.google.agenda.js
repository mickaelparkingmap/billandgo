/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */


$(".billandgo_add_project").submit(function (e) {
    e.preventDefault();
    var client = $("#billandgobundle_project_refClient option:selected").html();
    var name = $("#billandgobundle_project_name").val();
    var deadline = $("#billandgobundle_project_deadline").val();
    var desc = $("#billandgobundle_project_description").val();

    console.log(client, name, deadline, desc);
    if (statusG === 1) {
        addEvent(client, name, deadline, desc)
    }
    else {
        $(".billandgo_add_project").unbind("submit").submit();
    }

});



$("#createprojectwithoutdesc").submit(function (e) {
    e.preventDefault();
    var client = $(this).attr("attr-client");
    var name = $("#project_name").val();
    var deadline = $("#project_deadline").val();
    var desc = $("#pdescription").html();
    console.log(client, name, deadline, desc);
    if (statusG === 1) {
        addEvent2(client, name, deadline, desc, $("#createprojectwithoutdesc"))
    }
    else {
        $("#createprojectwithoutdesc").unbind("submit").submit();
    }

});


$(".form_project").each(function () {
    $(this).bind('submit', function (e) {
        e.preventDefault();
        var client = $(this).attr("attr-client");
        var name = $(this).find(".project_name").val();
        var deadline = $(this).find(".project_deadline").val();
        var desc = $(this).find(".pdesc").html();
        console.log(client, name, deadline, desc);
        if (statusG === 1) {
            addEvent2(client, name, deadline, desc, $(this))
        }
        else {
            $(this).unbind("submit").submit();
        }

    });
});



var addEvent2 = function (client, name, deadline, desc, id) {
    var event = {
        'summary': 'Projet : '+ name,
        'description': 'Projet pour le client '+ client + ' - ' + desc,
        'start': {
            'dateTime': (new Date()).toISOString(),
            'timeZone': 'Europe/Paris'
        },
        'end': {
            'dateTime': (new Date(deadline)).toISOString(),
            'timeZone': 'Europe/Paris'
        }
    };

    var request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event
    });

    request.execute(function(event) {
        id.unbind("submit").submit();
    });


};


var addEvent = function (client, name, deadline, desc) {
    var event = {
        'summary': 'Projet : '+ name,
        'description': 'Projet pour le client '+ client + ' - ' + desc,
        'start': {
            'dateTime': (new Date()).toISOString(),
            'timeZone': 'Europe/Paris'
        },
        'end': {
            'dateTime': (new Date(deadline)).toISOString(),
            'timeZone': 'Europe/Paris'
        }
    };

    var request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event
    });

    request.execute(function(event) {
        $(".billandgo_add_project").unbind("submit").submit();
    });


};

var cur = 0;
var getCalendarDateRange = function() {
    if (0 !== cur) {
        var calendar = $('#calendar').fullCalendar('getCalendar');
        var view = calendar.view;
        var start = view.start._d;
        var end = view.end._d;
        var dates = {start: start, end: end};
    }
    else {
        var date2 = new Date();
        var firstDay = new Date(date2.getFullYear(), date2.getMonth(), 1);
        var lastDay = new Date(date2.getFullYear(), date2.getMonth() + 1, 0);
        var dates = {start: firstDay, end: lastDay};
    }
    cur++;
    return dates;
};


// Client ID and API key from the Developer Console
var CLIENT_ID = '1004743587999-bq41bcvaibn4480ou05mt1dgb53q2cp2.apps.googleusercontent.com';
var API_KEY = 'AIzaSyAGFAxqVUeOHS7o1D9izB5U-E2y51wbzgY';

// Array of API discovery doc URLs for APIs used by the quickstart
var DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest"];

// Authorization scopes required by the API; multiple scopes can be
// included, separated by spaces.
var SCOPES = "https://www.googleapis.com/auth/calendar";

var authorizeButton = document.getElementById('authorize-button');
var signoutButton = document.getElementById('signout-button');

/**
 *  On load, called to load the auth2 library and API client library.
 */
function handleClientLoad() {
    gapi.load('client:auth2', initClient);
}

/**
 *  Initializes the API client library and sets up sign-in state
 *  listeners.
 */
var statusG = 1;
function initClient() {
    gapi.client.init({
        apiKey: API_KEY,
        clientId: CLIENT_ID,
        discoveryDocs: DISCOVERY_DOCS,
        scope: SCOPES
    }).then(function () {
        // Listen for sign-in state changes.
        gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

        // Handle the initial sign-in state.
        updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    });
}

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        statusG = 1;
    } else {
        statusG = 0;
    }
}
