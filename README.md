# Leaf Academy Events Calendar

## Intro

Plugin providing events calendar with categories.

## How to install
 
After downloading, run buildscript with command

```
npm install
```

Plugin have watching tasks to watch if scss, css or js files are changing. To run this task, run command

```
gulp default
```
 
## Shortcodes provided with plugin

**[la-events-calendar]**

Major shortcode to show events calendar.

## Packages 

- [FullCalendar](https://fullcalendar.io/)
- [Flexbox Grid](http://flexboxgrid.com/)
- [jQuery LoadingOverlay](https://github.com/gasparesganga/jquery-loading-overlay)
- [Moment](https://momentjs.com/)
- [tippy.js](https://atomiks.github.io/tippyjs/)

## Changelog

**1.0.4**

- small improvements in build

**1.0.3**

- used gulp pump instead of pipe in gulpfile

**1.0.2**

- added tippy.js to show popup under event
- added dynamic calendar style change based on viewport

**1.0.1**

- added support for all-day events

**1.0.0**

- Initialization on GIT