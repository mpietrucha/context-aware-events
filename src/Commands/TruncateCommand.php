<?php

namespace Mpietrucha\Events\Commands;

use Mpietrucha\Events\Bootstrap;
use Illuminate\Console\Command;

class TruncateCommand extends Command
{
    protected $signature = 'aware-events:truncate';

    protected $description = 'Clears all registered aware events.';

    public function handle(): void
    {
        $this->components->task('Clearing events', function () {
            Bootstrap::create()->truncate();
        });
    }
}
