<p align="center">
    <a href="http://www.serendipityhq.com" target="_blank">
        <img style="max-width: 350px" src="http://www.serendipityhq.com/assets/open-source-projects/Logo-SerendipityHQ-Icon-Text-Purple.png">
    </a>
</p>

<h1 align="center">Serendipity HQ Stopwatch</h1>
<p align="center">Profile your code, measuring both timing and memory usage.</p>
<p align="center">
    <a href="https://github.com/Aerendir/component-stopwatch/releases"><img src="https://img.shields.io/packagist/v/serendipity_hq/component-stopwatch.svg?style=flat-square"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square"></a>
    <a href="https://github.com/Aerendir/component-stopwatch/releases"><img src="https://img.shields.io/packagist/php-v/serendipity_hq/component-stopwatch?color=%238892BF&style=flat-square&logo=php" /></a>
    <a title="Tested with Symfony ^3.4" href="https://github.com/Aerendir/component-stopwatch/actions"><img title="Tested with Symfony ^3.4" src="https://img.shields.io/badge/Symfony-%5E3.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^4.4" href="https://github.com/Aerendir/component-stopwatch/actions"><img title="Tested with Symfony ^4.4" src="https://img.shields.io/badge/Symfony-%5E4.4-333?style=flat-square&logo=symfony" /></a>
    <a title="Tested with Symfony ^5.0" href="https://github.com/Aerendir/component-stopwatch/actions"><img title="Tested with Symfony ^5.0" src="https://img.shields.io/badge/Symfony-%5E5.0-333?style=flat-square&logo=symfony" /></a>
</p>

## Current Status
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=coverage)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=alert_status)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=security_rating)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=sqale_index)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=Aerendir_component-stopwatch&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=Aerendir_component-stopwatch)

[![Phan](https://github.com/Aerendir/component-stopwatch/workflows/Phan/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![PHPStan](https://github.com/Aerendir/component-stopwatch/workflows/PHPStan/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![PSalm](https://github.com/Aerendir/component-stopwatch/workflows/PSalm/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![PHPUnit](https://github.com/Aerendir/component-stopwatch/workflows/PHPunit/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![Composer](https://github.com/Aerendir/component-stopwatch/workflows/Composer/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![PHP CS Fixer](https://github.com/Aerendir/component-stopwatch/workflows/PHP%20CS%20Fixer/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)
[![Rector](https://github.com/Aerendir/component-stopwatch/workflows/Rector/badge.svg)](https://github.com/Aerendir/component-stopwatch/actions?query=branch%3Adev)

## Why

*This component is a fork of the Symfony's Stopwatch component, so you can check
[its Documentation](https://symfony.com/doc/current/components/stopwatch.html) for further information.*

We decided to fork it as the proposed changes about memory measurement were [rejected](https://github.com/symfony/symfony/pull/27711).

**ATTENTION**
*Memory usage measurement is a really complex topic in PHP: please, be careful and use this component
only for basic purposes. For more advanced measurements, use a more accurate tool like xDebug or Blackfire.*

## Features:

- Timing measurement
- Memory measurement (consumed, allocated, peak consumed by `emalloc()`, peak allocated)
- Divide profiles in sections
- Divide sections in events
- Group events in categories
- Start and stop
- Lap functionality
- Precision measurement

## Install Serendipity HQ Stopwatch

.. code-block:: terminal

    $ composer require serendipity_hq/component-stopwatch

Alternatively, you can clone the `<https://github.com/aerendir/stopwatch>`_ repository.

This library follows the http://semver.org/ versioning conventions.

## How to use Stopwatch

Stopwatch component allows to profile each part of your code as you like, being really precise.

The Stopwatch component provides an easy and consistent way to measure execution
time and memory usage info of certain parts of code so that you don't constantly have to parse
microtime by yourself.

Instead, use the simple `SerendipityHQ\Component\Stopwatch\Stopwatch` class:

    use Symfony\Component\Stopwatch\Stopwatch;

    // Initialize the class
    $stopwatch = new Stopwatch();


> Unlike the Symfony's Stopwatch Component that accepts a `true` parameter (`$morePrecision`), SerendipityHQ's Stopwatch Component
always measures time with microsecond precision, so you don't need to pass any parameter to the constructor.

    // This is the Symfony's Stopwatch way
    $stopwatch = new Stopwatch(true);

    // This is the SerendipityHQ's Stopwatch way: always microseconds precision
    $stopwatch = new Stopwatch();

Now you can start measurements:

    // ...

    // Starts event named 'event_name'
    $stopwatch->start('event_name');

    // ... some code goes here

    // Stop the event and get it
    $event = $stopwatch->stop('event_name');

`$event` is a `Event` object from which you can get the profiling information.

## Basic concepts

### Measurements

Stopwatch component has three main concepts when it comes to measurements:

1. **Periods**: The time passed between the start (`$stopwatch->start('event_name)`) and the stop (`$stopwatch->stop('event_name`);
2. **Events**: Something that is happening in your code and that you like to measure. It contains the Periods;
3. **Sections**: A group of events logically connected.
4. **origins**: Available for `Event`s and `Section`s, are the start time and memory measurements taken on creation of an `Event` or of a `Section` and the time and memory measurements of the last
time `$stopwatch->stop('event_name')` was called.

Each one of those is represented by a class, but you have to only interact with the main `Stopwatch` class.

The `Stopwatch` class exposes those methods for measurements:

    // To manage Events
    $stopwatch->start('event_name', 'event_category'); // Starts an event starting a new Period
    $stopwatch->stop('event_name');                    // Stops the current Period
    $stopwatch->lap('event_name');                     // Stops the current Period and starts a new one.
                                                       // Equals to $stopwatch->stop('event_name')->start('event_name')

    // To manage Sections
    $stopwatch->openSection();
    $stopwatch->stopSection('section_name');

    // Other methods
    $stopwatch->reset();

#### Events and Periods

An `Event` is something that is happening in you application: routing, image processing, a cycle, ecc.

An `Event`, using `Periods` and origins, measures the time that passes.

Origins are the start time and memory measurements and the time and memory measurements of the last time `$stopwatch->stop('event_name')` was called.

So an `Event` is basically a collection of `Period`s.

You can optionally give a category to an `Event`: this way you can logically group `Event`s in the same category:

    // Starts event named 'event_name'
    $stopwatch->start('event_name', 'event_category');

Categories are used, for example, by the Symfony WebProfileBundle to show a timeline with color-coded events.

![Image](https://farm5.staticflickr.com/4265/35035732604_d0eaece2ff_o.png)

In the image, "default", "section", "event_listener", ecc. are all categories of `Event`s.

You can get the `Event` object calling:

    $stopwatch->start('event_name')
    $stopwatch->stop('event_name')
    $stopwatch->lap('event_name')
    $stopwatch->getEvent('event_name')

The latter should be used when you need to retrieve the duration of an event while it is still running.

The `Event` object stores basically two kind of information: memory consumption and timing.

You can get all this useful information from the event object:

    $event->getCategory();          // Returns the category the event was started in
    $event->getOrigin();            // Returns the event start time in milliseconds
    $event->ensureStopped();        // Stops all periods not already stopped
    $event->getStartTime();         // Returns the start time of the very first period
    $event->getEndTime();           // Returns the end time of the very last period
    $event->getDuration();          // Returns the event duration, including all periods
    $event->getMemory();            // Of all periods, gets the max memory amount assigned
                                    // to PHP (measured with memory_get_usage(true))
    $event->getMemoryCurrent();     // Of all periods, gets the max amount of memory used
                                    // by the script (measured with memory_get_usage())
    $event->getMemoryPeak();        // Of all periods, gets the max peak amount of memory
                                    // assigned to PHP (measured with memory_get_peak_usage(true))
    $event->getMemoryPeakEmalloc(); // Of all periods, gets the max amount of memory assigned
                                    // to PHP and used by emalloc() (measured with memory_get_peak_usage())

Additionally to this, the `Event` object stores also `Period`s.

As you know from the real world, all stopwatches come with two buttons: one to start and stop the stopwatch, and another to measure the lap time.

This is exactly what the `Stopwatch::lap()` method does::

```php
    // ...

    // starts event named 'process_elements'
    $stopwatch->start('process_elements');

    // Maybe here some other code

    // Start cycling the elements
    foreach ($lements as $element) {
        // Process the $element

        // At the end use lap() to stop the timer and start a new Period
        $stopwatch->lap('process_elements');

    }

    // ... Some other code goes here

    // Finally stop the Event and get it to get information about timing and memory
    $event = $stopwatch->stop('process_elements');
```

Lap information is stored as "periods" within the event.

To get detailed information about timing and memory for each lap, call:

    // Get all Periods measured in the Event
    $periods = $event->getPeriods();

### Formatting `Event`'s information

Stopwatch provides an Helper method useful to format the measurements:

```php
use SerendipityHQ\Component\Stopwatch\Utils\Formatter;

$event = $stopwatch->getSection('section_name')->getEvent('event_name');

dump(Formatter::formatTime($event->getDuration()), Formatter::formatMemory($event->getMemory());
```

### Sections

Sections are a way to logically split the timeline into groups.

You can see how Symfony uses sections to nicely visualize the framework lifecycle in the Symfony Profiler tool:

![Image](https://farm5.staticflickr.com/4265/35035732604_d0eaece2ff_o.png)

In the image, "kernel_request" is a `Section`.

Exapanding on the previous example, try implment somthing to use the `Section`s:

```php
    // ...

    // Open a section
    $stopwatch->openSection();

    // Start the event assigning the category "numbers"
    $stopwatch->start('fibonacci_event', 'numbers');

    // Execute the code
    dump('fibonacci_event result', '-------------', '');
    $prev = 0;
    $next = 1;
    while($prev < 10000000000000) {
        $num = $prev + $next;

        dump($num);

        $prev = $next;
        $next = $num;

        // Get a lap (returns the current event to be used if you like!)
        $stopwatch->lap('fibonacci_event');
    }

    // Stop the event
    $stopwatch->stop('fibonacci_event');

    // Start a new event assigning the category "geometry"
    $stopwatch->start('square_numbers_event', 'geometry');

    // Execute the code
    dump('square_numbers_event result', '-------------', '');
    $root = 0;
    while ($root < 50) {
        dump($root * $root); // or pow($root, 2);

        $root++;

        // Get a lap (returns the current event to be used if you like!)
        $stopwatch->lap('square_numbers_event');
    }

    // Stop the event
    $stopwatch->stop('square_numbers_event');

    // Stop the section assigning it a name (yes, when closing, not when opening!)
    $stopwatch->stopSection('fibonacci_and_squares');

    // Open a new section
    $stopwatch->openSection();

    // Start a new event assigning the category "geometry"
    $stopwatch->start('triangle_numbers_event', 'geometry');

    // Execute some code
    dump('triangle_numbers_event result', '-------------', '');
    for($i = 1; $i <= 10; $i++) {
        $triangle = [];

        for($j = 1; $j <= $i; $j++) {
            $triangle[] = $j;
        }

        dump(implode(' ', $triangle));

        // Get a lap (returns the current event to be used if you like!)
        $stopwatch->lap('triangle_numbers_event');
    }

    // Stop the event
    $stopwatch->stop('triangle_numbers_event');

    // Start a new event assigning the category "numbers"
    $stopwatch->start('magic_square', 'numbers');

    // Execute some code
    dump('magic_square result', '-------------', '');
    $order = 5;

    for ($row = 0; $row < $order; $row++) {
        $rows = [];
        for ($col = 0; $col < $order; $col++) {
            $rowMatrix = ((($order + 1) / 2 + $row + $col) % $order);
            $colMatrix = ((($order + 1) / 2 + $row + $order - $col - 1) % $order) + 1;

            $rows[] = $rowMatrix * $order + $colMatrix;
        }

        dump(implode(' ', $rows));

        // Get a lap (returns the current event to be used if you like!)
        $stopwatch->lap('magic_square');
    }

    // Stop the event
    $stopwatch->stop('magic_square');

    // Stop the section assigning it a name (yes, when closing, not when opening!)
    $stopwatch->stopSection('triangle_numbersand_magic_square');


    dd($stopwatch);
```

You can reopen a closed section by calling `$stopwatch::openSection('section_name')`.

So, for example, if we would like to add to the section `fibonacci_and_squares` another `Event`, we do:

    $stopwatch->openSection('fibonacci_and_squares');

    // Start another event, execute other code...

    // Stop the event and then stop the section again

#### Get measurement of a Section

As told, when you call `Stopwatch::openSection()`, Stopwatch creates an event that measures the section itself, other than collecting the other events you create manually.

This is useful to measure the entire section, without having to sum all the events in it.

You can get the `Section`'s `Event` with this simple code:

```php
use SerendipityHQ\Component\Stopwatch\Utils\Formatter;

$sectionEvent = $stopwatch->getSection('section_name')->getEvent(Stopwatch::SECTION);

dump(Formatter::formatTime($sectionEvent->getDuration()), Formatter::formatMemory($sectionEvent->getMemory());
```

You can also use the shortcut `Section::getSectionEvent()` to get the `Section`s `Event`:

```php
$sectionEvent = $stopwatch->getSection('section_name')->getSectionEvent();
```

### Memory

As told, measuring memory in PHP is task not so simple and also not so precise.

Using the `Stopwatch` component itself, you consume memory (a really small amount, but anyway an amount!), so when measuring the memory consumption you get cumulative results.

This means, for example, that if you run two scripts on your server and measure memory only from one, the memory measurements you get are anyway influenced by the other not measured script.

Take this into account when reading the results of the `Stopwatch`.

If you want to get more accurate measurements, you should consider using a more advanced tool for profiling like Blackfire that can be used also in production.

There are other caveats, too, but this is the most important one.

In long running processes, where you need to profile a lot of code in a long period of time, the ´Stopwatch` component may become very "fat" as it stores a lot of `Event`s, `Period`s and maybe of `Section`s.

In such situation maybe useful to optimize the amount of memory used by PHP ánd so, by `Stopwatch` too).

So, if you like, you can call `$stopwatch->reset()` method to erease from the `Stopwatch` object all the information collected, freeing up memory.

Obviously, once called, the information collected until that moment will not be available anymore, so it is a good idea to "save" them somewhere (in the database, in the logs or anywhere else).

## Resources

  * [Documentation](https://symfony.com/doc/current/components/stopwatch.html)
  * [Contributing](https://symfony.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/symfony/symfony/issues) and
    [send Pull Requests](https://github.com/symfony/symfony/pulls)
    in the [main Symfony repository](https://github.com/symfony/symfony)
