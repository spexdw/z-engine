<?php

namespace ZEngine\Core\Services;

class EventService
{
    protected array $listeners = [];

    public function listen(string $event, callable|string|null $listener = null, int $priority = 0): void
    {
        if ($listener === null) {
            $this->listeners[$event] ??= [];
            return;
        }

        $this->listeners[$event][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];

        usort($this->listeners[$event], fn ($a, $b) => $b['priority'] <=> $a['priority']);
    }

    public function dispatch(string $event, mixed $payload = null): void
    {
        if (is_callable($payload)) {
            $payload();
            return;
        }

        foreach ($this->getListenersForEvent($event) as $item) {
            $listener = $item['listener'];

            $result = is_string($listener)
                ? app($listener)->handle($payload)
                : $listener($payload);

            if ($result === false) {
                break;
            }
        }
    }


    protected function getListenersForEvent(string $event): array
    {
        $listeners = $this->listeners[$event] ?? [];

        // wildcard support
        foreach ($this->listeners as $name => $items) {
            if (str_contains($name, '*')) {
                $pattern = '#^' . str_replace('*', '.*', $name) . '$#';
                if (preg_match($pattern, $event)) {
                    $listeners = array_merge($listeners, $items);
                }
            }
        }

        usort($listeners, fn ($a, $b) => $b['priority'] <=> $a['priority']);

        return $listeners;
    }

    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }
}
