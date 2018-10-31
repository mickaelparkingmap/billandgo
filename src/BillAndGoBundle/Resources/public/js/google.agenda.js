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





$.datetimepicker.setDateFormatter('moment');
jQuery.datetimepicker.setLocale('fr');
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

                var s = new Date((typeof event.start.date != "undefined") ? event.start.date : event.start.dateTime);

                var e = new Date((typeof event.end.date != "undefined") ? event.end.date : event.end.dateTime);

                var myEvent = {
                    id : event.id,
                    title: event.summary,
                    allDay: allday,
                    start: s,
                    end: e,
                    url: event.htmlLink,
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
            if ("1" == $('#allday').val()) {
                $('#fancy-checkbox-default').click();
            }

            $('#eName').val("");
            $('#eDescription').val("");
            $('#eLocation').val("");
            $('#eDueDate').val("");
            $("#eStartDate").val(start.format('YYYY/MM/DD 00:00'));
            $('#eDueDate').datetimepicker({step: 1});
            $("#eStartDate").datetimepicker({step: 1, setdate: start.format('YYYY/MM/DD HH:mm')});


            $('#createEventModal').modal('show');
        },

        //When u drop an event in the calendar do the following:
        eventDrop: function (event, delta, revertFunc) {
            //do something when event is dropped at a new location
        },

        //When u resize an event in the calendar do the following:
        eventResize: function (event, delta, revertFunc) {

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
                if (event.allDay) {
                    $(".notallday").hide();
                    $(".alldayok").show();
                    $("#startTimeADK").html(moment(event.start).format('DD/MM/Y')+ " <small class='text-info'>(Toute la journée)</small>");
                }
                else {
                    $(".alldayok").hide();
                    $(".notallday").show();
                    $("#startTime").html(moment(event.start).format('DD/MM/Y à HH:mm'));
                    $("#endTime").html(moment(event.end).format('DD/MM/Y à HH:mm'));
                }

                $("#eventContent").find(".modal-footer .editevent").attr("event-id", event.id);
                $(".closeon").attr("event-id", event.id);
                $("#eventInfo").html(event.description);
                $("#eventLink").attr('href', event.url);
                $("#eventLocation").html(event.location);
                $("#eventContent").modal('show');
            });

        }
    });
});


$('#submitButton').on('click', function(e){
    // We don't want this to act as a link so cancel the link action
    e.preventDefault();

    doSubmit();
});


$('#submitEditButton').on('click', function(e){
    // We don't want this to act as a link so cancel the link action
    e.preventDefault();

    doEditSubmit();
});

$(".closeon").click(function(e) {
    e.preventDefault();
    var id = $(this).attr("event-id");
    var evento = $("#calendar").fullCalendar('clientEvents', id)[0];
    $(".modal").modal('hide');
    $("#eventDelete").find(".modal-title").html("Suppression de l'évènement "+ evento.title )
    $("#eventDelete").find(".modal-body strong").html(evento.title);
    $("#eventDelete").modal('show');
    $("#eventDeleteConfirm").attr("event-id", id);
});


$(".editevent").click(function(e) {
    e.preventDefault();
    var id = $(this).attr("event-id");

    var evento = $("#calendar").fullCalendar('clientEvents', id)[0];
    $(".modal").modal('hide');

    $("#eNameE").val((evento.title));
    $("#eStartDateE").val(moment(evento.start).format('YYYY/MM/DD HH:mm'));
    $("#eStartDateE").datetimepicker({step: 1, format: 'YYYY/MM/DD HH:mm'});
    $("#eDueDateE").val(moment(evento.end).format('YYYY/MM/DD HH:mm'));
    $("#eDueDateE").datetimepicker({step: 1, format: 'YYYY/MM/DD HH:mm'});
    if (1 == $("#alldayE").val()) {
        $("#fancy-checkbox-defaultE").click();
    }
    if (evento.allDay) {
        $("#fancy-checkbox-defaultE").click();
        $("#eDueDateE").val(moment(evento.start).format('YYYY/MM/DD HH:mm'));
    }

    $("#eDescriptionE").val((evento.description == "Non renseigné")? "" : evento.description);
    $("#eLocationE").val((evento.location == "Non renseigné")? "" : evento.location);

    $("#editEventModal").find(".modal-title").html("Edition de l'évènement "+ evento.title )
    //$("#eventDelete").find(".modal-body strong").html(evento.title);
    $("#editEventModal").modal('show');
    $("#submitEditButton").attr("event-id", id);
});


$("#eventDeleteConfirm").click(function(e) {
    e.preventDefault();
    var id = $(this).attr("event-id");
    var evento = $("#calendar").fullCalendar('clientEvents', id)[0];
    $(".modal").modal('hide');
    $("#eventConfirmation").find(".modal-title").html("Confirmation de suppression de l'évènement "+ evento.title )
    $("#eventConfirmation").find(".modal-body").html("L'évènement <strong>"+evento.title+"</strong> a bien été supprimé.");
    $("#eventConfirmation").modal('show');

        $('#calendar').fullCalendar('removeEvents', id);
        var request = gapi.client.calendar.events.delete({
            'calendarId': 'primary',
            'eventId': id
        });
        request.execute(function(event) {
            $("#eventConfirmation").modal('show');
            setTimeout(function () {
                $("#eventConfirmation").modal('hide');
            }, 3000)
        });



});

function zeroPadded(val) {
    if (val >= 10)
        return val;
    else
        return '0' + val;
}


function doSubmit(){
    var alld = $('#allday').val();
    if ("" === $('#eName').val()) {
        $("#eName").effect("highlight", {}, 3000);
        return false;
    }
    if (("" === $('#eStartDate').val() )) {
        $("#eStartDate").effect("highlight", {}, 3000);
        return false;
    }

    if (0 == alld && ("" === $('#eDueDate').val() )) {
        $("#eDueDate").effect("highlight", {}, 3000);
        return false;
    }


    $("#createEventModal").modal('hide');


    var d = new Date($('#eStartDate').val());

    var st = null;
    var ed = null;
    if (1 == alld) {
         st = ed = {
            'date': d.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d.getDate())
        };

    }
    else {
        var d1 = new Date($('#eDueDate').val());
        st = {
            'dateTime': d.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d.getDate())+"T"+zeroPadded(d.getHours())+":"+zeroPadded(d.getMinutes())+":00",
            'timeZone': 'Europe/Paris'
        };

        ed = {
            'dateTime': d1.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d1.getDate())+"T"+zeroPadded(d1.getHours())+":"+zeroPadded(d1.getMinutes())+":00",
            'timeZone': 'Europe/Paris'
        };
    }
    var event1 = {
        'summary': $('#eName').val(),
        'location':$('#eLocation').val(),
        'description': $('#eDescription').val(),
        'start': st,
        'end': ed
    };

    if (1 == alld) {
        var event = {
            title: $('#eName').val(),
            start: new Date($('#eStartDate').val()),
            end: new Date(moment($('#eDueDate').val()).format('Y/M/D H:m')),
            description: $('#eDescription').val(),
            location: $('#eLocation').val(),
            allDay: true
        };
    }
    else {
        var event = {
            title: $('#eName').val(),
            start: new Date($('#eStartDate').val()),
            end: new Date(moment($('#eDueDate').val()).format('Y/M/D H:m')),
            description: $('#eDescription').val(),
            location: $('#eLocation').val()
        };
    }

    var request = gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event1
    });
    request.execute(function(event2) {

        event.id = event2.id;
        $("#calendar").fullCalendar('renderEvent', event, true);

    });
}



function doEditSubmit(){
    var id = $("#submitEditButton").attr("event-id");
    var evento = $("#calendar").fullCalendar('clientEvents', id)[0];
    var alld = $('#alldayE').val();
    if ("" === $('#eNameE').val()) {
        $("#eNameE").effect("highlight", {}, 3000);
        return false;
    }
    if (("" === $('#eStartDateE').val() )) {
        $("#eStartDateE").effect("highlight", {}, 3000);
        return false;
    }

    if (0 == alld && ("" === $('#eDueDateE').val() )) {
        $("#eDueDateE").effect("highlight", {}, 3000);
        return false;
    }


    $("#editEventModal").modal('hide');


    var d = new Date($('#eStartDateE').val());

    var st = null;
    var ed = null;
    if (1 == alld) {
        st = ed = {
            'date': d.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d.getDate())
        };

    }
    else {
        var d1 = new Date($('#eDueDateE').val());
        st = {
            'dateTime': d.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d.getDate())+"T"+zeroPadded(d.getHours())+":"+zeroPadded(d.getMinutes())+":00",
            'timeZone': 'Europe/Paris'
        };

        ed = {
            'dateTime': d1.getFullYear()+"-"+zeroPadded(d.getMonth() + 1)+"-"+zeroPadded(d1.getDate())+"T"+zeroPadded(d1.getHours())+":"+zeroPadded(d1.getMinutes())+":00",
            'timeZone': 'Europe/Paris'
        };
    }


    if (1 == alld) {

            evento.id = id;
                evento.title = $('#eNameE').val();
            evento.start = new Date($('#eStartDateE').val());
            evento.end =  null;
            evento.description =  $('#eDescriptionE').val();
            evento.location= $('#eLocationE').val();
            evento.allDay = true;

    }
    else {
        evento.id = id;
        evento.title = $('#eNameE').val();
        evento.start = new Date($('#eStartDateE').val());
        evento.end =  new Date(moment($('#eDueDateE').val()).format('Y/M/D H:m'));
        evento.description =  $('#eDescriptionE').val();
        evento.location= $('#eLocationE').val();
        evento.allDay = false;
    }


    var eventGoogle = gapi.client.calendar.events.get({"calendarId": 'primary', "eventId": id});


    eventGoogle.summary =  $('#eNameE').val();
    eventGoogle.location = $('#eLocationE').val();
    eventGoogle.description =  $('#eDescriptionE').val();
    eventGoogle.start = st;
    eventGoogle.end = ed;

    var request = gapi.client.calendar.events.update({
        'calendarId': 'primary',
        'resource': eventGoogle,
        'eventId': id
    });
    request.execute(function(event) {
        $("#calendar").fullCalendar('updateEvent', evento, true);
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
        //authorizeButton.onclick = handleAuthClick;
        //signoutButton.onclick = handleSignoutClick;
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
        //$("#row_event_append, .row_pre_event").show();
        listUpcomingEvents();
        addEvent();
        $("#calendar").show();
        //$(".row_pre_event").show();
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
    //pre.innerHTML = message;
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
                    '                            <h5 class="widget-user-desc">Début : ' + when +  (("undefined" !== typeof event.end && "" !== event.end) ?' <br> Fin : ' + end : '')+' </h5>\n' +
                    (("undefined" !== typeof event.location && "" !== event.location) ?
                        '                            <h5 class="widget-user-desc">Lieu : ' + event.location + '</h5>\n' : "") +
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
