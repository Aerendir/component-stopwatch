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
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Section
{
    /** @var Event[] $events */
    private $events = [];

    /** @var string|null $id */
    private $id;

    /** @var Section[] $children */
    private $children = [];

    /**
     * @return string The identifier of the section
     */
    public function getId(): string
    {
        if (null === $this->id) {
            throw new \LogicException('This Section is not yet closed. You cannot get its ID until you close it for the first time.');
        }

        return $this->id;
    }

    /**
     * Sets the Section identifier.
     *
     * @param string $id The Section identifier
     *
     * @return Section
     * @internal
     */
    public function stopSection(string $id): Section
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Creates a new Section if conditions are met or returns an existing child section if it exists.
     *
     * The resulting section is returned so Stopwatch can add it to the list of currently active Sections.
     *
     * @param string|null $id Null to create a new section, the identifier to re-open an existing one
     *
     * @return Section
     * @internal
     */
    public function openChildSection(?string $id = null): Section
    {
        // If no $id is passed or if the Section is not already created, create a new Section
        if (null === $id || null === $this->getChildSection($id)) {
            // Return the created section so Stopwatch can add it to the list of currently active Sections
            return $section = $this->children[] = new self();
        }

        // Return the found child section so Stopwatch can add it to the list of currently active Sections
        return $this->getChildSection($id);
    }

    /**
     * Returns the child section.
     *
     * @param string $id The child section identifier
     *
     * @return Section|null The child section or null when none found
     * @internal
     */
    public function getChildSection(string $id): ?Section
    {
        foreach ($this->children as $child) {
            if ($id === $child->getId()) {
                return $child;
            }
        }

        return null;
    }

    /**
     * Starts an event.
     *
     * @param string      $name     The event name
     * @param string|null $category The event category
     *
     * @return Event The event
     * @internal
     */
    public function startEvent(string $name, ?string $category = null): Event
    {
        if ( ! isset($this->events[$name])) {
            $this->events[$name] = new Event($category);
        }

        return $this->events[$name]->start();
    }

    /**
     * Checks if the event was started.
     *
     * @param string $name The event name
     *
     * @return bool
     * @internal
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
     * @internal
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
     * @internal
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
     * @internal
     */
    public function getEvent(string $name): Event
    {
        if ( ! isset($this->events[$name])) {
            throw new \LogicException(sprintf('Event "%s" is not known.', $name));
        }

        return $this->events[$name];
    }

    /**
     * Returns the events recorded by this Section.
     *
     * @return Event[] All the Events recored by this Section
     * @internal
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
