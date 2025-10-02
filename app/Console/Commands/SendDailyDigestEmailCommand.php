<?php
/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 15:30
 */

namespace App\Console\Commands;

use App\Jobs\SendDailyDigestEmailJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendDailyDigestEmailCommand extends Command
{
    protected $signature = 'send:daily-digest-email';

    protected $description = 'Send daily digest emails to all users';

    public function handle(): void
    {
        User::chunk(100, static function ($users) {
            foreach ($users as $user) {
                dispatch(new SendDailyDigestEmailJob($user));
            }
        });

        $this->info('Daily digest email jobs have been dispatched.');
    }
}
