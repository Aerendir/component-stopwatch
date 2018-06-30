<?php

/*
 *
 * This file is part of the Serendipity HQ Stopwatch Component.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Adamo Crespi <hello@aerendir.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the Symfony Framework.
 */

namespace SerendipityHQ\Component\Stopwatch;

/**
 * Stopwatch section.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Section
{
    /** @var Event[] $events */
    private $events = [];

    /** @var float|null $origin */
    private $origin;

    /** @var string $id */
    private $id;

    /** @var Section[] $children */
    private $children = [];

    /**
     * @param float|null $origin Set the origin of the events in this section, use null to set their origin to their start time
     */
    public function __construct(float $origin = null)
    {
        $this->origin = $origin;
    }

    /**
     * Returns the child section.
     *
     * @param string $id The child section identifier
     *
     * @return Section|null The child section or null when none found
     */
    public function get(string $id): ?Section
    {
        foreach ($this->children as $child) {
            if ($id === $child->getId()) {
                return $child;
            }
        }

        return null;
    }

    /**
     * Creates or re-opens a child section.
     *
     * @param string|null $id Null to create a new section, the identifier to re-open an existing one
     *
     * @return Section
     */
    public function open(?string $id = null): Section
    {
        // If no id is passed or if the Section is not already created, create a new Section
        if (null === $id) {
            return $section = $this->children[] = new self(microtime(true));
        }

        if (null === $this->get($id)) {
            return $section = $this->children[] = new self(microtime(true));
        }

        return $this->get($id);
    }

    /**
     * @return string The identifier of the section
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the session identifier.
     *
     * @param string $id The session identifier
     *
     * @return Section
     */
    public function setId(string $id): Section
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Starts an event.
     *
     * @param string      $name     The event name
     * @param string|null $category The event category
     *
     * @return Event The event
     */
    public function startEvent(string $name, ?string $category = null): Event
    {
        if ( ! isset($this->events[$name])) {
            $this->events[$name] = new Event($this->origin ?: microtime(true), $category);
        }

        return $this->events[$name]->start();
    }

    /**
     * Checks if the event was started.
     *
     * @param string $name The event name
     *
     * @return bool
     */
    public function isEventStarted(string $name): bool
    {
        return isset($this->events[$name]) && $this->events[$name]->isStarted();
    }

    /**
     * Stops an event.
     *
     * @param string $name The event name
     *
     * @throws \LogicException When the event has not been started
     *
     * @return Event The event
     */
    public function stopEvent(string $name): Event
    {
        if ( ! isset($this->events[$name])) {
            throw new \LogicException(sprintf('Event "%s" is not started.', $name));
        }

        return $this->events[$name]->stop();
    }

    /**
     * Stops then restarts an event.
     *
     * @param string $name The event name
     *
     * @throws \LogicException When the event has not been started
     *
     * @return Event The event
     */
    public function lap(string $name): Event
    {
        return $this->stopEvent($name)->start();
    }

    /**
     * Returns a specific event by name.
     *
     * @param string $name The event name
     *
     * @throws \LogicException When the event is not known
     *
     * @return Event The event
     */
    public function getEvent(string $name): Event
    {
        if ( ! isset($this->events[$name])) {
            throw new \LogicException(sprintf('Event "%s" is not known.', $name));
        }

        return $this->events[$name];
    }

    /**
     * Returns the events from this section.
     *
     * @return Event[] An array of Event instances
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
