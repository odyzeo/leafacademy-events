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

					updateHelperForm('page', 0);
					updateHelperForm('category', categoryField.find(":selected").val());
					updateViewInformations(calendar);

					callMobileRestApiEndpoint(LA_Events.mobile_rest_url, true);

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
				var perPage = form.find('input[name="per_page"]').val();

				return {
					'start_interval': startInterval,
					'end_interval': endInterval,
					'category': category,
					'page': page,
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

			var callMobileRestApiEndpoint = function(restUrl, refreshView) {

				var helperFormData = getHelperFormData();

				var page = helperFormData.page;
				var total = helperFormData.total;
				var perPage = helperFormData.per_page;
				var category = helperFormData.category;

				var mobileCalendarLayout = $('.la-events-listing .listing');

				mobileCalendarLayout.LoadingOverlay('show');

				$.ajax({
					url: restUrl + 'page=' + page + '&per_page=' + perPage + '&category=' + category,
					success: function(data) {

						appendItemsToHandlebarsResults(data, '.la-events-listing .listing', 'event-item', refreshView);
						mobileCalendarLayout.LoadingOverlay('hide');

						var total = data.total;
						var currentPage = data.page;

						updateHelperForm('page', currentPage);
						updateHelperForm('total', total);

						if (currentPage < total) {

							if (currentPage == 1) {
								renderLoadMore('.la-events-listing');
							}
						} else {
							$('.load-more').hide();
						}
					}
				});

			};

			var renderLoadMore = function(selector) {

				var appendElement = $(selector);

				var buttonHtml = '<div class="row load-more-wrapper"><div class="col-xs-12 inner"><div class="load-more ninja-forms-field btn green nf-element">' + LA_Events.load_more + '</div></div></div>';

				appendElement.append(buttonHtml);

				loadMoreButtonClick('.load-more');

			};

			var loadMoreButtonClick = function(selector) {

				var element = $(selector);

				element.on('click', function() {

					console.log('clicked');
					callMobileRestApiEndpoint(LA_Events.mobile_rest_url, false);
				});

			};

			var generateHtmlTemplateForTippy = function(eventId, event) {

				var eventTitle = event.title;
				var eventAllDay = event.allDay;
				var eventStart = getEventTime(event.start);
				var eventColor = event.backgroundColor;
				var eventContent = event.extra.content;

				generateHtml(eventId, eventTitle, eventAllDay, eventStart, eventColor, eventContent);

				return $('#' + eventId)[0];

			};

			var generateMobileHtmlTemplateForTippy = function(eventId, event) {

				var eventTitle = event.title;
				var eventAllDay = event.all_day;
				var eventStart = event.date_object.start_time;
				var eventColor = event.category.color;
				var eventContent = event.content;

				generateHtml(eventId, eventTitle, eventAllDay, eventStart, eventColor, eventContent);

				return $('#' + eventId)[0];

			};

			var generateHtml = function(eventId, title, allday, start, backgroundColor, content) {

				var html = '';

				html += '<div id="' + eventId + '">';
				html += '<h1 style="border-left:4px solid ' + backgroundColor + ';padding-left:12px;">' + title + '</h1>';

				if (!allday) {
					html += LA_Events.event_start + ' ' + start;
				}

				html += '<h2>' + content + '</h2>';
				html += '</div>';

				$('body').append(html);

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

				callMobileRestApiEndpoint(LA_Events.mobile_rest_url);

			};

			var appendItemsToHandlebarsResults = function(items, resultsSelector, templateId, refreshView) {

				var resultsElement = $(resultsSelector);
				var source = document.getElementById(templateId).innerHTML;
				var template = Handlebars.compile(source);
				var html = template(items);


				if (refreshView) {
					resultsElement.empty();
				}

				resultsElement.append(html);

				for (var i = 0; i < items.items.length; i++) {

					var eventWpId = items.items[i].ID;

					tippy('.row.item.tippy[data-id="' + eventWpId + '"]', {
						html: function(e) {

							return generateMobileHtmlTemplateForTippy(eventWpId, items.items[i]);

						}
					});

				}

			};

			initCalendar('#la-calendar');
			initMobileListing();

		});
	});
})(jQuery);