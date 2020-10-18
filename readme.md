# Laravel 7 Full Calendar 5 Helper

This is a simple helper package to make generating [http://fullcalendar.io](http://fullcalendar.io) in Laravel apps easier.

Thanks to [@maddhatter](https://github.com/maddhatter) for the [initial repo](https://github.com/maddhatter/laravel-fullcalendar) for laravel < 7

## Installing

Require the package with composer using the following command:

    composer require acaronlex/laravel-calendar

The provider and `Calendar` alias will be registered automatically.

You will also need to include [fullcalendar.io](http://fullcalendar.io/)'s files in your HTML.

## Usage

### Creating Events

#### Using `event()`:

The simpliest way to create an event is to pass the event information to `Calendar::event()`:

```php
$event = \Calendar::event(
    "Valentine's Day", //event title
    true, //full day event?
    '2015-02-14', //start time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg)
    '2015-02-14', //end time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg),
	1, //optional event ID
	[
		'url' => 'http://full-calendar.io'
	]
);
```

#### Implementing `Event` Interface

Alternatively, you can use an existing class and have it implement `BezhanSalleh\LaravelCalendar\Event`. An example of an Eloquent model that implements the `Event` interface:

```php
class EventModel extends Eloquent implements \BezhanSalleh\LaravelCalendar\Event
{

    protected $dates = ['start', 'end'];

    /**
     * Get the event's id number
     *
     * @return int
     */
    public function getId() {
		return $this->id;
	}

    /**
     * Get the event's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Is it an all day event?
     *
     * @return bool
     */
    public function isAllDay()
    {
        return (bool)$this->all_day;
    }

    /**
     * Get the start time
     *
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get the end time
     *
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }
}
```

#### `IdentifiableEvent` Interface

If you wish for your existing class to have event IDs, implement `\BezhanSalleh\LaravelCalendar\IdentifiableEvent` instead. This interface extends `\BezhanSalleh\LaravelCalendar\Event` to add a `getId()` method:

```php
class EventModel extends Eloquent implements \BezhanSalleh\LaravelCalendar\IdentifiableEvent
{

	// Implement all Event methods ...

    /**
     * Get the event's ID
     *
     * @return int|string|null
     */
    public function getId();

}

```

### Additional Event Parameters

If you want to add [additional parameters](http://fullcalendar.io/docs/event_data/Event_Object) to your events, there are two options:

#### Using `Calendar::event()`

Pass an array of `'parameter' => 'value'` pairs as the 6th parameter to `Calendar::event()`:

```php
$event = \Calendar::event(
    "Valentine's Day", //event title
    true, //full day event?
    '2015-02-14', //start time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg)
    '2015-02-14', //end time, must be a DateTime object or valid DateTime format (http://bit.ly/1z7QWbg),
	1, //optional event ID
	[
		'url' => 'http://full-calendar.io',
		//any other full-calendar supported parameters
	]
);

```

#### Add an `getEventOptions` method to your event class

```php
<?php
class CalendarEvent extends \Illuminate\Database\Eloquent\Model implements \BezhanSalleh\LaravelCalendar\Event
{
	//...

	/**
     * Optional FullCalendar.io settings for this event
     *
     * @return array
     */
    public function getEventOptions()
    {
        return [
            'color' => $this->background_color,
			//etc
        ];
    }

	//...
}

```

### Create a Calendar

To create a calendar, in your route or controller, create your event(s), then pass them to `Calendar::addEvent()` or `Calendar::addEvents()` (to add an array of events). `addEvent()` and `addEvents()` can be used fluently (chained together). Their second parameter accepts an array of valid [FullCalendar Event Object parameters](http://fullcalendar.io/docs/event_data/Event_Object/).

#### Sample Controller code:

```php
$events = [];

$events[] = \Calendar::event(
    'Event One', //event title
    false, //full day event?
    '2015-02-11T0800', //start time (you can also use Carbon instead of DateTime)
    '2015-02-12T0800', //end time (you can also use Carbon instead of DateTime)
	0 //optionally, you can specify an event ID
);

$events[] = \Calendar::event(
    "Valentine's Day", //event title
    true, //full day event?
    new \DateTime('2015-02-14'), //start time (you can also use Carbon instead of DateTime)
    new \DateTime('2015-02-14'), //end time (you can also use Carbon instead of DateTime)
	'stringEventId' //optionally, you can specify an event ID
);

$eloquentEvent = EventModel::first(); //EventModel implements BezhanSalleh\LaravelCalendar\Event

$calendar = new Calendar();
        $calendar->addEvents($events)
        ->setOptions([
            'locale' => 'fr',
            'firstDay' => 0,
            'displayEventTime' => true,
            'selectable' => true,
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'end' => 'today prev,next dayGridMonth timeGridWeek timeGridDay'
            ]
        ]);
        $calendar->setId('1');
        $calendar->setCallbacks([
            'select' => 'function(selectionInfo){}',
            'eventClick' => 'function(event){}'
        ]);

return view('hello', compact('calendar'));
```

#### Sample View

Then to display, add the following code to your View:

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.0.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.0.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@5.0.0/main.min.js"></script>

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.0.0/main.min.css"
    />

    <style>
      /* ... */
    </style>
  </head>
  <body>
    {!! $calendar->calendar() !!} {!! $calendar->script() !!}
  </body>
</html>
```

**Note:** The output from `calendar()` and `script()` must be non-escaped, so use `{!!` and `!!}` (or whatever you've configured your Blade compiler's raw tag directives as).

The `script()` can be placed anywhere after `calendar()`, and must be after fullcalendar was included.
