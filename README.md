[![Latest Stable Version](https://poser.pugx.org/serendipity_hq/stopwatch/v/stable.png)](https://packagist.org/packages/serendipity_hq/stopwatch)
[![Build Status](https://travis-ci.org/Aerendir/Stopwatch.svg?branch=master)](https://travis-ci.org/Aerendir/Stopwatch)
[![Total Downloads](https://poser.pugx.org/serendipity_hq/stopwatch/downloads.svg)](https://packagist.org/packages/serendipity_hq/stopwatch)
[![License](https://poser.pugx.org/serendipity_hq/stopwatch/license.svg)](https://packagist.org/packages/serendipity_hq/stopwatch)

[![Maintainability](https://api.codeclimate.com/v1/badges/f201e5346bcaf8c041fa/maintainability)](https://codeclimate.com/github/Aerendir/Stopwatch/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/f201e5346bcaf8c041fa/test_coverage)](https://codeclimate.com/github/Aerendir/Stopwatch/test_coverage)
[![Issue Count](https://codeclimate.com/github/Aerendir/Stopwatch/badges/issue_count.svg)](https://codeclimate.com/github/Aerendir/Stopwatch)

The Stopwatch Component
=======================

    The Stopwatch component provides a way to profile code, measuring both timing and memory usage.

**ATTENTION**
*Memory usage measurement is a really complex topic in PHP: please, be careful and use this component
only for basic purposes. For more advanced measurements, use a more accurate tool like xDebug or Blackfire.*

*This component is a fork of the Symfony's Stopwatch component, so you can check
[its Documentation](https://symfony.com/doc/current/components/stopwatch.html) for further information.*

*Improvements introduced in this version are waiting for merge in the main Symfony's Stopwatch component.*

### Features:

- Timing measurement
- Memory measurement (consumed, allocated, peak consumed by `emalloc()`, peak allocated)
- Divide profiles in sections
- Divide sections in events
- Group events in categories
- Start and stop
- Lap functionality
- Precision measurement

### Requirements

- PHP: >= 7.2

Installation
------------

.. code-block:: terminal

    $ composer require serendipity_hq/stopwatch

Alternatively, you can clone the `<https://github.com/aerendir/stopwatch>`_ repository.

This library follows the http://semver.org/ versioning conventions.

How to use Stopwatch
--------------------

Stopwatch component allows to profile each part of your code as you like, being really precise.

The Stopwatch component provides an easy and consistent way to measure execution
time and memory usage info of certain parts of code so that you don't constantly have to parse
microtime by yourself.

Instead, use the simple `SerendipityHQ\Component\Stopwatch\Stopwatch` class:

    use Symfony\Component\Stopwatch\Stopwatch;

    // Initialize the class
    $stopwatch = new Stopwatch(true);


> By default, the stopwatch truncates any sub-millisecond time measure to ``0``,
so you can't measure microseconds or nanoseconds. If you need more precision,
pass ``true`` to the ``Stopwatch`` class constructor to enable full precision::

    $stopwatch = new Stopwatch(true);

Now you can start measurements:

    // ...
    
    // Starts event named 'eventName'
    $stopwatch->start('eventName');

    // ... some code goes here
    
    // Stop the event and get it
    $event = $stopwatch->stop('eventName');

`$event` is a `StopwatchEvent` object from which you can get the profiling information.

Basic concepts
--------------

Stopwatch component has three main concepts:

1. Periods: The time passed between the start (`$stopwatch->start('eventName)`) and the stop (`$stopwatch->stop('eventName`);
2. Events: Something that is happening in your code and that you like to measure. It contains the Periods;
3. Sections: A group of events logically connected.

Each one of those is represented by a class, but you have to only interact with the main `Stopwatch` class.

### Events and Periods

An `Event` is something that is happening in you application: routing, image processing, a cycle, ecc.

An `Event` measures the time that passes using `Periods`.

So an `Event` is basically a collection of `Period`s.

You can give an `Event` a category: this way you can logically group `Event`s in the same category.

Categories are used, for example, by the Symfony WebProfileBundle to show a timeline with color-coded events.

![Image](https://farm5.staticflickr.com/4265/35035732604_d0eaece2ff_o.png)

In the image, "default", "section", "event_listener", ecc. are all categories of `Event`s.


As you know from the real world, all stopwatches come with two buttons: one to start and stop the stopwatch, and another to measure the lap time.

This is exactly what the `Stopwatch::lap()` method does::

    // ...
    
    // starts event named 'foo'
    $stopwatch->start('process_elements');
    
    foreach ($lements as $element) {
        // Process the $element
        
        // At the end use lap() to stop the timer and start a new Period
        $stopwatch->lap('process_elements');
        
    }
    
    // ... some other code goes here
    $event = $stopwatch->stop('eventName');

Lap information is stored as "periods" within the event. To get lap information
call::

    $event->getPeriods();

In addition to periods, you can get other useful information from the event object.
For example::

    $event->getCategory();   // returns the category the event was started in
    $event->getOrigin();     // returns the event start time in milliseconds
    $event->ensureStopped(); // stops all periods not already stopped
    $event->getStartTime();  // returns the start time of the very first period
    $event->getEndTime();    // returns the end time of the very last period
    $event->getDuration();   // returns the event duration, including all periods
    $event->getMemory();     // returns the max memory usage of all periods




The :class:`SerendipityHQ\\Component\\Stopwatch\\StopwatchEvent` object can be retrieved
from the  :method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::start`,
:method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::stop`,
:method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::lap` and
:method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::getEvent` methods.
The latter should be used when you need to retrieve the duration of an event
while it is still running.

The stopwatch can be reset to its original state at any given time with the
:method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::reset` method, which deletes
all the data measured so far.

You can also provide a category name to an event::

    $stopwatch->start('eventName', 'categoryName');

You can consider categories as a way of tagging events. For example, the
Symfony Profiler tool uses categories to nicely color-code different events.


Sections
--------

Sections are a way to logically split the timeline into groups. You can see
how Symfony uses sections to nicely visualize the framework lifecycle in the
Symfony Profiler tool. Here is a basic usage example using sections::

    $stopwatch = new Stopwatch();

    $stopwatch->openSection();
    $stopwatch->start('parsing_config_file', 'filesystem_operations');
    $stopwatch->stopSection('routing');

    $events = $stopwatch->getSectionEvents('routing');

You can reopen a closed section by calling the :method:`SerendipityHQ\\Component\\Stopwatch\\Stopwatch::openSection`
method and specifying the id of the section to be reopened::

    $stopwatch->openSection('routing');
    $stopwatch->start('building_config_tree');
    $stopwatch->stopSection('routing');

.. _Packagist: https://packagist.org/packages/serendipity_hq/stopwatch

Resources
---------

  * [Documentation](https://symfony.com/doc/current/components/stopwatch.html)
  * [Contributing](https://symfony.com/doc/current/contributing/index.html)
  * [Report issues](https://github.com/symfony/symfony/issues) and
    [send Pull Requests](https://github.com/symfony/symfony/pulls)
    in the [main Symfony repository](https://github.com/symfony/symfony)
