<?php

use Illuminate\Support\Facades\Schedule;

// Run every day at 8:00 AM
Schedule::command('reminders:dispatch')->dailyAt('08:00');