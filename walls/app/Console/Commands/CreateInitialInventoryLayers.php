<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FifoService;

class CreateInitialInventoryLayers extends Command
{
    protected $signature = 'inventory:initial';

    protected $description = 'Зафиксировать существующие складские остатки как начальные FIFO-слои';

    public function handle(FifoService $fifoService)
    {
        $this->info(
            'Начинаем фиксацию существующих остатков...'
        );

        $created = $fifoService->createInitialLayers();

        if ($created === 0) {
            $this->warn(
                'Новых начальных FIFO-слоёв не создано.'
            );

            $this->line(
                'Возможно, остатки уже были зафиксированы ранее.'
            );

            return self::SUCCESS;
        }

        $this->info(
            "Создано начальных FIFO-слоёв: {$created}"
        );

        return self::SUCCESS;
    }
}