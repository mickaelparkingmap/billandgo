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

$(function() {
    var date2 = new Date();
    var firstDay = new Date(date2.getFullYear(), date2.getMonth(), 1);
    var lastDay = new Date(date2.getFullYear(), date2.getMonth() + 1, 0);
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month, listWeek, listMonth, listYear, listDay'
        },
        lang: 'fr',
        views: {
            month: {
                buttonText: 'Normal'
            },
            listDay: {
                buttonText: 'Jours'
            },
            listWeek: {
                buttonText: 'Semaines'
            },
            listMonth: {
                buttonText: 'Mois'
            },
            listYear: {
                buttonText: 'Année'
            }
        },
        height: 700,
        editable: false,
        selectable: true,
        visibleRange: {
            start: firstDay,
            end: lastDay
        },
        //When u select some space in the calendar do the following:
        select: function (start, end, allDay) {
            //do something when space selected
            //Show 'add event' modal
            console.log(start);
            $('#eName').val("");
            $('#eDescription').val("");
            $('#eLocation').val("");
            $('#eDueDate').val("");
            $("#eStartDate").datepicker( "setDate", new Date(start));

            $('#createEventModal').modal('show');
        },

        //When u drop an event in the calendar do the following:
        eventDrop: function (event, delta, revertFunc) {
            //do something when event is dropped at a new location
        },

        //When u resize an event in the calendar do the following:
        eventResize: function (event, delta, revertFunc) {
            //do something when event is resized
        },

        eventRender: function(event, element) {
            $(element).tooltip({title: event.title});
        },

        //Activating modal for 'when an event is clicked'
        eventClick: function (event) {
            $('#modalTitle').html(event.title);
            $('#modalBody').html(event.description);
            $('#fullCalModal').modal();
        },

        /*events: idg,*/

        eventRender: function (event, element) {
            element.attr('href', 'javascript:void(0);');
            element.click(function() {
                $("#eventTitle").html((event.title));
                $("#startTime").html(moment(event.start).format('DD/MM/Y'));
                $("#endTime").html(moment(event.end).format('DD/MM/Y'));
                $("#eventInfo").html(event.description);
                $("#eventLink").attr('href', event.url);
                $("#eventLocation").html(event.location);
                $("#eventContent").modal('show');
            });

            element.append( "<span class='closeon btn btn-danger text-center'><i class='fa fa-remove'></i></span>");
            element.find(".closeon").click(function() {
                if (confirm('Vous êtes sûr de vouloir supprimer l\'évènement ' + event.title + '?')) {
                    $('#calendar').fullCalendar('removeEvents', event._id);
                    var request = gapi.client.calendar.events.delete({
                        'calendarId': 'primary',
                        'eventId': event.id
                    });
                    request.execute(function(event) {
                    });
                }
            });
        }
    });
});


$('#submitButton').on('click', function(e){
    // We don't want this to act as a link so cancel the link action
    e.preventDefault();

    doSubmit();
});

function zeroPadded(val) {
    if (val >= 10)
        return val;
    else
        return '0' + val;
}


function doSubmit(){
    $("#createEventModal").modal('hide');
    console.log(new Date(moment($('#eDueDate').val()).format('Y/M/D')));
    var event = {
        title: $('#eName').val(),
        start: new Date($('#eStartDate').val()),
        end: new Date(moment($('#eDueDate').val()).format('Y/M/D')),
        description: $('#eDescription').val(),
        location: $('#eLocation').val(),
    };
    $("#calendar").fullCalendar('renderEvent', event, true);

    var d = new Date($('#eStartDate').val());
    var d1 = new Date($('#eDueDate').val());
    var event1 = {
        'summary': $('#eName').val(),
        'location':$('#eLocation').val(),
        'description': $('#eDescription').val(),
        'start': {
            'dateTime': d.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d.getDate())+"T00:00:00"
        },
        'end': {
            'dateTime':d1.getFullYear()+"-"+zeroPadded(d1.getMonth() + 1)+"-"+zeroPadded(d1.getDate())+"T00:00:00"
        }
    };

    var request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event1
    });
    request.execute(function(event) {
    });
}

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
        listUpcomingEvents();
        addEvent();
        $("#calendar").show();
    } else {
        $("#calendar").hide();
        $("#row_event_append, .row_pre_event").hide();
        gastatus = 0;
        authorizeButton.style.display = 'block';
        signoutButton.style.display = 'none';
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
                    ' <div class="col-md-3 onlick" onclick="location.href=\'' + event.htmlLink + '\'">\n' +
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
