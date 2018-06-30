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
    private const SECTION = '__section__';
    private const SECTION_ROOT = '__root__';
    private const SECTION_CHILD = '__section__.child';
    private const SECTION_CATEGORY = 'section';

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
        return $this->getCurrentSection()->startEvent($name, $category);
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
        return $this->getCurrentSection()->stopEvent($name);
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
        return $this->getCurrentSection()->stopEvent($name)->start();
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
        return $this->getCurrentSection()->getEvent($name);
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
        return $this->getCurrentSection()->isEventStarted($name);
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
        return $this->hasSection($id) ? $this->getSection($id)->getEvents() : [];
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
        if (null !== $id && null === $this->getCurrentSection()->getChildSection($id)) {
            throw new \LogicException(sprintf('The section "%s" has been started at an other level and can not be opened.', $id));
        }

        $this->start(self::SECTION_CHILD, self::SECTION_CATEGORY);
        $this->activeSections[] = $this->getCurrentSection()->openChildSection($id);
        $this->start(self::SECTION);
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
        $this->stop(self::SECTION);

        if (1 === count($this->activeSections)) {
            throw new \LogicException('There is no started section to stop.');
        }
        
        /** @var Section $section */
        $section = array_pop($this->activeSections);

        $this->sections[$id] = $section->closeSection($id);
        $this->stop(self::SECTION_CHILD);
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
        $this->sections = $this->activeSections = [self::SECTION_ROOT => new Section()];
    }

    /**
     * @return Section
     */
    private function getCurrentSection():Section
    {
        return end($this->activeSections);
    }
}
