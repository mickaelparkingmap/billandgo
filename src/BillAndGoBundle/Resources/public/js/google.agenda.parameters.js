/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */

var addEvent = function () {
    $(".loading-element").show();
    var range = getCalendarDateRange();
    gapi.client.calendar.events.list({
        'calendarId': 'primary',
        'timeMin': range.start.toISOString(),
        'timeMax': range.end.toISOString(),
        'showDeleted': false,
        'singleEvents': true,
        'orderBy': 'startTime',
    }).then(function(response) {
        var events = response.result.items;
        if (events.length > 0) {
            var myCalendar = $('#calendar');
            myCalendar.fullCalendar();

            for (var i = 0; i < events.length; i++) {
                var event = events[i];
                var allday = (typeof event.start.date !== "undefined");
                myCalendar.fullCalendar( 'removeEvents', event.id)
                var myEvent = {
                    id : event.id,
                    title: event.summary,
                    allDay: allday,
                    start: formatingDate2(event.start),
                    end: formatingDate2(event.end),
                    url: event.htmlLink,
                    backgroundColor : '#f2634f',
                    borderColor: '#f2634f',
                    description : (typeof event.description !== "undefined")? event.description : "Non renseigné",
                    location : (typeof event.location !== "undefined")? event.location : "Non renseigné"
                };
                myCalendar.fullCalendar( 'renderEvent', myEvent );
            }
        }
        $(".loading-element").hide();
    });
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

var gastatus = 0;

/**
 *  Initializes the API client library and sets up sign-in state
 *  listeners.
 */
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
        authorizeButton.onclick = handleAuthClick;
        signoutButton.onclick = handleSignoutClick;
    });
}

/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        authorizeButton.style.display = 'none';
        signoutButton.style.display = 'block';
        gastatus = 1;
        $("#row_event_append, .row_pre_event").show();
        $(".agenda-element2").show();

    } else {
        gastatus = 0;
        authorizeButton.style.display = 'block';
        signoutButton.style.display = 'none';
        $(".agenda-element2").hide();
    }
}

/**
 *  Sign in the user upon button click.
 */
function handleAuthClick(event) {
    gapi.auth2.getAuthInstance().signIn();
}

/**
 *  Sign out the user upon button click.
 */
function handleSignoutClick(event) {
    gapi.auth2.getAuthInstance().signOut();
}

/**
 * Append a pre element to the body containing the given message
 * as its text node. Used to display the results of the API call.
 *
 * @param {string} message Text to be plfullCalendaraced in pre element.
 */
function appendPre(message) {
    var pre = document.getElementById('row_event_append');
    pre.innerHTML = message;
}


