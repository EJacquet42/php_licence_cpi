<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('logs:import-from-files')->everyMinute()->withoutOverlapping();
