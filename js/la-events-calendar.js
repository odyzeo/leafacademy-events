jQuery.noConflict();
(function($) {
	$(function() {
		$(document).ready(function() {

			var initCalendar = function(selector) {

				var calendar = $(selector).fullCalendar();

				onCalendarRender(calendar);

			};

			var onCalendarRender = function(calendar) {

				var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event/';

				callRestApiEndpoint(eventsEndpoint, calendar);

			};

			var callRestApiEndpoint = function(restUrl, calendar) {

				var currentView = getCurrentDateRange(calendar);

				var startInterval = currentView.start;
				var endInterval = currentView.end;

				$.ajax({
					url: restUrl + 'intStart=' + startInterval + '&intEnd=' + endInterval,
					success: function(data) {

						updateEvents(calendar, data.events);
					}
				});

			};

			var updateEvents = function(calendar, events) {

				calendar.fullCalendar('renderEvents', events);
			};

			var getCurrentDateRange = function(calendar) {

				var calendarObject = calendar.fullCalendar('getView');

				return {
					'start': calendarObject.start._i / 1000,
					'end': calendarObject.end._i / 1000
				};

			};


			initCalendar('#la-calendar');

		});
	});
})(jQuery);