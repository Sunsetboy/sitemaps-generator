<?php
declare(strict_types=1);

namespace SitemapGenerator;

class SitemapUrl
{
    private string $link;
    private string $date;
    private string $frequency;
    private string $priority;

    /**
     * @param string $link
     * @param string $date
     * @param string $frequency
     * @param string $priority
     */
    public function __construct(string $link, string $date, string $frequency, string $priority)
    {
        $this->link = $link;
        $this->date = $date;
        $this->frequency = $frequency;
        $this->priority = $priority;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }
}
