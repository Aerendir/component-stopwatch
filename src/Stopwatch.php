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
 * Stopwatch provides a way to profile code.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Stopwatch
{
    /** @var Section[] $sections */
    private $sections;

    /** @var Section[] $activeSections */
    private $activeSections;

    /**
     * Initializes the Stopwatch.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Starts an event.
     *
     * @param string $name     The event name
     * @param string $category The event category
     *
     * @return Event
     */
    public function start(string $name, string $category = null): Event
    {
        return end($this->activeSections)->startEvent($name, $category);
    }

    /**
     * Stops an event.
     *
     * @param string $name The event name
     *
     * @return Event
     */
    public function stop(string $name): Event
    {
        return end($this->activeSections)->stopEvent($name);
    }

    /**
     * Stops then restarts an event.
     *
     * @param string $name The event name
     *
     * @return Event
     */
    public function lap(string $name): Event
    {
        return end($this->activeSections)->stopEvent($name)->start();
    }

    /**
     * Returns a specific event by name.
     *
     * @param string $name The event name
     *
     * @return Event
     */
    public function getEvent(string $name): Event
    {
        return end($this->activeSections)->getEvent($name);
    }

    /**
     * Checks if the event was started.
     *
     * @param string $name The event name
     *
     * @return bool
     */
    public function isStarted(string $name): bool
    {
        return end($this->activeSections)->isEventStarted($name);
    }

    /**
     * Gets all events for a given section.
     *
     * @param string $id A section identifier
     *
     * @return Event[]
     */
    public function getSectionEvents(string $id): array
    {
        return isset($this->sections[$id]) ? $this->sections[$id]->getEvents() : [];
    }

    /**
     * Creates a new section or re-opens an existing section.
     *
     * @param string|null $id The id of the session to re-open, null to create a new one
     *
     * @throws \LogicException When the section to re-open is not reachable
     */
    public function openSection(?string $id = null): void
    {
        /** @var Section $current */
        $current = end($this->activeSections);

        if (null !== $id && null === $current->get($id)) {
            throw new \LogicException(sprintf('The section "%s" has been started at an other level and can not be opened.', $id));
        }

        $this->start('__section__.child', 'section');
        $this->activeSections[] = $current->open($id);
        $this->start('__section__');
    }

    /**
     * Stops the last started section.
     *
     * The id parameter is used to retrieve the events from this section.
     *
     * @see getSectionEvents()
     *
     * @param string $id The identifier of the section
     *
     * @throws \LogicException When there's no started section to be stopped
     */
    public function stopSection(string $id): void
    {
        $this->stop('__section__');

        if (1 === count($this->activeSections)) {
            throw new \LogicException('There is no started section to stop.');
        }

        $this->sections[$id] = array_pop($this->activeSections)->setId($id);
        $this->stop('__section__.child');
    }

    /**
     * @param string $id
     *
     * @return Section
     */
    public function getSection(string $id): Section
    {
        if (false === $this->hasSection($id)) {
            throw new \InvalidArgumentException(sprintf('The section "%s" doesn\'t exist. Maybe you have not still closed it.', $id));
        }

        return $this->sections[$id];
    }

    /**
     * @return Section[]
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasSection(string $id): bool
    {
        return isset($this->sections[$id]);
    }

    /**
     * Resets the stopwatch to its original state.
     */
    public function reset(): void
    {
        $this->sections = $this->activeSections = ['__root__' => new Section(null)];
    }
}
