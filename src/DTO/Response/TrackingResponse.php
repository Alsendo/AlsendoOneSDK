<?php

declare(strict_types=1);

namespace AlsendoOne\SDK\DTO\Response;

class TrackingResponse
{
    private string $service;
    /** @var TrackingEvent[] */
    private array $events;

    /**
     * @param TrackingEvent[] $events
     */
    private function __construct(string $service, array $events)
    {
        $this->service = $service;
        $this->events = $events;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $events = [];

        $rawEvents = $data['data'] ?? [];
        if (is_array($rawEvents)) {
            foreach ($rawEvents as $event) {
                if (is_array($event)) {
                    $events[] = TrackingEvent::fromArray($event);
                }
            }
        }

        return new self(
            (string) ($data['service'] ?? ''),
            $events
        );
    }

    /**
     * Service identification as returned by the API (observed as the full
     * service name, e.g. "Pocztex Punkt Punkt-Punkt").
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Tracking events, in the order returned by the API (newest first).
     *
     * @return TrackingEvent[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
