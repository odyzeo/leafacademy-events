jQuery.noConflict();
(function($) {
	$(function() {
		$(document).ready(function() {

			var initCalendar = function(selector) {

				var calendar = $(selector).fullCalendar({
					views: {
						month: {
							timeFormat: 'H:mm'
						}
					},
					viewRender: function(view, element) {

						if (typeof calendar !== 'undefined') {
							updateViewInformations(calendar);

							var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event/';

							callRestApiEndpoint(eventsEndpoint, calendar);

						}
					},
					eventClick: function(event, jsEvent, view) {

						var currentView = view.name;

						if (currentView == 'listMonth') {

							var isEventAllDay = event.allDay;
							var eventTitle = event.title;
							var eventDate = getEventDate(event.start._i);

							var alertTitle = eventDate + ' ' + eventTitle;

							if (isEventAllDay) {
								alertTitle = eventTitle;
							}

							alert(alertTitle);

						}

					},
					eventRender: function(event, element, view) {

						var currentView = view.name;

						var isEventAllDay = event.allDay;

						if (currentView == 'month') {

							var eventTimeFormatted = getEventDate(event.start._i);

							var eventHtml = '<a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" title="' + eventTimeFormatted + ' ' + event.title + '"><div class="fc-content"><span class="fc-time">' + eventTimeFormatted + '</span> <span class="fc-title">' + event.title + '</span></div></a>';

							if (isEventAllDay) {
								eventHtml = '<a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" title="' + event.title + '"><div class="fc-content"><span class="fc-title">' + event.title + '</span></div></a>';
							}

							return $(eventHtml)
						}

					}
				});

				onCalendarRender(calendar);
				onCategoryChange('select[name="event_category"]', calendar);
				onCategoryListSelect('ul#event_category li', calendar);

				$(window).on('resize orientationChange', function(event) {
					checkViewPort(calendar);
				});

			};

			var getEventDate = function(eventStart) {

				var eventTimeOffset = moment(eventStart).utcOffset();
				var eventTime = moment(eventStart).subtract(eventTimeOffset, 'm');
				var eventTimeFormatted = eventTime.format('HH:mm');

				return eventTimeFormatted;

			};

			var onCategoryListSelect = function(selector, calendar) {

				var selectedItem = $(selector);

				selectedItem.click(function() {

					var categoryId = $(this).data('id');

					$('ul#event_category li').removeClass('active');
					$('ul#event_category li[data-id="' + categoryId + '"]').addClass('active');

					updateHelperForm('category', categoryId);
					updateViewInformations(calendar);

					var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event/';

					callRestApiEndpoint(eventsEndpoint, calendar);

				});

			};

			var onCategoryChange = function(selector, calendar) {

				$(selector).change(function() {

					var categoryField = $('select[name="event_category"]');

					updateHelperForm('category', categoryField.find(":selected").val())
					updateViewInformations(calendar);

					var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event/';

					callRestApiEndpoint(eventsEndpoint, calendar);

				});

			};

			var updateViewInformations = function(calendar) {

				var currentView = getCurrentDateRange(calendar);

				var startInterval = currentView.start;
				var endInterval = currentView.end;

				updateHelperForm('start_interval', startInterval);
				updateHelperForm('end_interval', endInterval);

			};

			var switchCalendarView = function(calendar, view) {

				calendar.fullCalendar('changeView', view);

			};

			var checkViewPort = function(calendar) {
				var viewportWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

				if (viewportWidth <= 768) {

					switchCalendarView(calendar, 'listMonth');
				}

				else {
					switchCalendarView(calendar, 'month');
				}
			};

			var onCalendarRender = function(calendar) {

				checkViewPort(calendar);

				updateViewInformations(calendar);

				var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event/';

				callRestApiEndpoint(eventsEndpoint, calendar);

			};

			var updateHelperForm = function(key, value) {

				$('form#calendar-helper input[name="' + key + '"]').val(value);

			};

			var getHelperFormData = function() {

				var form = $('form#calendar-helper');
				var startInterval = form.find('input[name="start_interval"]').val();
				var endInterval = form.find('input[name="end_interval"]').val();
				var category = form.find('input[name="category"]').val();

				return {
					'start_interval': startInterval,
					'end_interval': endInterval,
					'category': category
				};

			};

			var callRestApiEndpoint = function(restUrl, calendar) {

				var helperFormData = getHelperFormData();

				var startInterval = helperFormData.start_interval;
				var endInterval = helperFormData.end_interval;
				var category = helperFormData.category;

				calendar.LoadingOverlay('show');

				$.ajax({
					url: restUrl + 'intStart=' + startInterval + '&intEnd=' + endInterval + '&category=' + category,
					success: function(data) {

						updateEvents(calendar, data.events);
						tippy('a.tippy');
					}
				});


			};

			var updateEvents = function(calendar, events) {

				calendar.fullCalendar('removeEvents');
				calendar.fullCalendar('renderEvents', events);

				calendar.LoadingOverlay('hide');
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