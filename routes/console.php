<?php

use Illuminate\Support\Facades\Schedule;

// Automatically run the Moodle AI encouragement task daily
Schedule::command('moodle:send-encouragement')->daily();
