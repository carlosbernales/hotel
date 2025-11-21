$('#calendar').fullCalendar({
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    defaultView: 'month',
    editable: false,
    events: {
        url: 'eventData.php?calendar=true',
        error: function() {
            alert('Error fetching events!');
        }
    },
    eventRender: function(event, element) {
        element.attr('title', event.description);
    }
}); 