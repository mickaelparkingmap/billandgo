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

var url_ga = null;
/**
 *  Called when the signed in status changes, to update the UI
 *  appropriately. After a sign-in, the API is called.
 */
function updateSigninStatus(isSignedIn) {
    if (isSignedIn) {
        url_ga = $("#row_google_agenda").attr("path-elem");
        listUpcomingEvents();
    } else {
       $("#row_google_agenda").hide();
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
 * @param {string} message Text to be placed in pre element.
 */
function appendPre(message) {
    var pre = document.getElementById('row_event_append');
    pre.innerHTML = message;
}

/**
 * Print the summary and start datetime/date of the next ten events in
 * the authorized user's calendar. If no events are found an
 * appropriate message is printed.
 */
function listUpcomingEvents() {

    gapi.client.calendar.events.list({
        'calendarId': 'primary',
        'timeMin': (new Date()).toISOString(),
        'showDeleted': false,
        'singleEvents': true,
        'maxResults': 4,
        'orderBy': 'startTime'
    }).then(function(response) {
        var events = response.result.items;
        if (events.length > 0) {
            var html = "";
            var color = ['bg-green', 'bg-purple', 'bg-black', 'bg-blue'];
            for (var i = 0; i < events.length; i++) {
                var event = events[i];
                var when = formatingDate(event.start);
                var end = formatingDate(event.end);
                if (!when) {
                    when = event.start.date;
                }

                html = html + (
                    ' <div class="col-md-3 onlick" onclick="location.href=\'' + url_ga + '\'">\n' +
                    '                    <!-- Widget: user widget style 1 -->\n' +
                    '                    <div class="box box-widget widget-user-2 box-client ' + color[i] + '-client">\n' +
                    '                        <!-- Add the bg color to the header using any of the bg-* classes -->\n' +
                    '                        <div class="widget-user-header ' + color[i] + ' ">\n' +
                    '                            <div class="widget-user-image" style="margin-top: -35px;">\n' +
                    '                                <span class="img-circle"><i class="fa fa-calendar-o fa-3x ppdashbboard" ></i> </span>\n' +
                    '                            </div>\n' +
                    '                            <!-- /.widget-user-image -->\n' +
                    '                            <h5 class="widget-user-username"> ' + event.summary + '  </h5>\n' +
                    '                            <h5 class="widget-user-desc">Début : ' + when + '</h5>\n' +
                    (("undefined" !== typeof event.location && "" !== event.location) ?
                        '                            <h5 class="widget-user-desc">Lieu : ' + event.location + '</h5>\n' : "") +
                    (("undefined" !== typeof event.end && "" !== event.end) ?
                        '                            <h5 class="widget-user-desc">Fin : ' + end + '</h5>\n' : "") +
                    '\n' +
                    '                        </div>\n' +
                    '                        <div class="box-footer no-padding">\n' +
                    '                            <div class="panel-body">\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                    <!-- /.widget-user -->\n' +
                    '                </div>');
            }
            appendPre(html);
        } else {
            appendPre('<div class="callout callout-info col-md-12">Aucun évènement</div>');
        }

    });
}
