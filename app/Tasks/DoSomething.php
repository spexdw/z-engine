<?php

namespace ZEngine\App\Tasks;

class DoSomething
{
    public function handle(): void
    {
        // cron logic

        echo "[" . date('Y-m-d H:i:s') . "] DoSomethinh worked.\n";
    }
}
