<?php
declare(strict_types = 1);

return [
    'resources' => [
        'enabled'                => true,
        'label'                  => 'Fila',
        'plural_label'           => 'Filas',
        'navigation_group'       => 'Sistema',
        'navigation_icon'        => 'heroicon-o-cpu-chip',
        'navigation_sort'        => null,
        'record_title_attribute' => 'jobs',
        'navigation_count_badge' => true,
        'resource'               => Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource::class,
        'cluster'                => null,
    ],
    'pruning' => [
        'enabled'        => true,
        'retention_days' => 7,
    ],
    'queues' => [
        'default',
    ],
];
