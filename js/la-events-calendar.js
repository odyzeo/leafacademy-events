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
					// eventClick: function(event, jsEvent, view) {
					//
					// 	var currentView = view.name;
					//
					// 	if (currentView == 'listMonth') {
					//
					// 		var isEventAllDay = event.allDay;
					// 		var eventTitle = event.title;
					// 		var eventDate = getEventTime(event.start._i);
					//
					// 		var alertTitle = eventDate + ' ' + eventTitle;
					//
					// 		if (isEventAllDay) {
					// 			alertTitle = eventTitle;
					// 		}
					//
					// 		alert(alertTitle);
					//
					// 	}
					//
					// },
					eventRender: function(event, element, view) {

						var currentView = view.name;

						var isEventAllDay = event.allDay;
						var eventId = event.extra.id;
						var eventColor = event.backgroundColor;
						var eventHtml = '';

						var eventTimeFormatted = getEventTime(event.start);

						if (currentView == 'month') {


							eventHtml = '<a style="background-color:' + eventColor + ';border:' + eventColor + ';" class="fc-day-grid-event fc-h-event fc-event fc-start fc-end tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" data-id="' + eventId + '" data-tipp-trigger="mouseenter"><div class="fc-content"><span class="fc-time">' + eventTimeFormatted + '</span> <span class="fc-title">' + event.title + '</span></div></a>';

							if (isEventAllDay) {
								eventHtml = '<a style="background-color:' + eventColor + ';border:' + eventColor + ';" class="fc-day-grid-event fc-h-event fc-event fc-start fc-end tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" data-id="' + eventId + '" data-tipp-trigger="mouseenter"><div class="fc-content"><span class="fc-title">' + event.title + '</span></div></a>';
							}
						}

						else {


							eventHtml = '<tr class="fc-list-item tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" data-id="' + eventId + '" data-tipp-trigger="mouseenter"><td class="fc-list-item-time fc-widget-content"></td><td class="fc-list-item-marker fc-widget-content"><span class="fc-event-dot" style="background-color:' + eventColor + '"></span></td><td class="fc-list-item-title fc-widget-content"><a>' + event.title + '</a></td></tr>';

							if (isEventAllDay) {

								eventHtml = '<tr class="fc-list-item tippy" data-tippy-arrow="true" data-tippy-animation="shift-toward" data-tippy-duration="[600,300]" data-id="' + eventId + '" data-tipp-trigger="mouseenter"><td class="fc-list-item-time fc-widget-content">' + LA_Events.all_day + '</td><td class="fc-list-item-marker fc-widget-content"><span class="fc-event-dot" style="background-color:' + eventColor + '"></span></td><td class="fc-list-item-title fc-widget-content"><a>' + event.title + '</a></td></tr>';


							}


						}

						return $(eventHtml);

					}
				});

				onCalendarRender(calendar);
				onCategoryChange('select[name="event_category"]', calendar);
				onCategoryListSelect('ul#event_category li', calendar);

				$(window).on('resize orientationChange', function(event) {
					checkViewPort(calendar);
				});

			};

			var getEventTime = function(eventStart) {

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
				var page = form.find('input[name="page"]').val();
				var total = form.find('input[name="total"]').val();
				var perPage = form.find('input[name="per_page"]').val();

				return {
					'start_interval': startInterval,
					'end_interval': endInterval,
					'category': category,
					'page': page,
					'total': total,
					'per_page': perPage
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

						var renderedTemplates = [];

						for (var i = 0; i < data.events.length; i++) {

							var eventWpId = data.events[i].extra.id;

							if (jQuery.inArray(eventWpId, renderedTemplates) === -1) {

								renderedTemplates.push(eventWpId);

								tippy('a.tippy[data-id="' + eventWpId + '"], tr.tippy[data-id="' + eventWpId + '"]', {
									html: function(e) {

										return generateHtmlTemplateForTippy(eventWpId, data.events[i]);

									}
								});
							}
						}
					}
				});


			};

			var callMobileRestApiEndpoint = function(restUrl) {

				var helperFormData = getHelperFormData();

				var page = helperFormData.page;
				var total = helperFormData.total;
				var perPage = helperFormData.per_page;
				var category = helperFormData.category;

				$.ajax({
					url: restUrl + 'page=' + page + '&total=' + total + '&per_page=' + perPage + '&category=' + category,
					success: function(data) {

						console.log(data);
					}
				});

			};

			var generateHtmlTemplateForTippy = function(eventId, event) {

				var eventTitle = event.title;
				var eventAllDay = event.allDay;
				var eventStart = event.start;
				var eventColor = event.backgroundColor;
				var eventContent = event.extra.content;

				var html = '';

				html += '<div id="' + eventId + '">';
				html += '<h1 style="border-left:4px solid ' + eventColor + ';padding-left:12px;">' + eventTitle + '</h1>';

				if (!eventAllDay) {
					html += LA_Events.event_start + ' ' + getEventTime(eventStart);
				}

				html += '<h2>' + eventContent + '</h2>';
				html += '</div>';

				$('body').append(html);

				return $('#' + eventId)[0];

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

			var initMobileListing = function() {

				var eventsEndpoint = LA_Events.rest_url + 'la-events-calendar/v1/event-mobile/';

				callMobileRestApiEndpoint(eventsEndpoint);

			};


			initCalendar('#la-calendar');
			// initMobileListing();

		});
	});
})(jQuery);