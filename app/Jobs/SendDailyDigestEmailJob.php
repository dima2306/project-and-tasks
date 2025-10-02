<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 02.10.25
 * Time: 14:34
 */

namespace App\Jobs;

use App\Actions\Emails\PrepareDigestDataAction;
use App\Mail\DailyDigestMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendDailyDigestEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly User $user,
    ) {
    }

    public function handle(PrepareDigestDataAction $action): void
    {
        $digestData = $action->execute($this->user);

        if ($this->hasRelevantContent($digestData)) {
            Mail::to($this->user->email)
                ->send(new DailyDigestMail($digestData));
        } else {
            info('No relevant content for user: '.$this->user->email);
        }
    }

    private function hasRelevantContent(array $data): bool
    {
        return $data['tasks_due_today']->isNotEmpty() ||
            $data['newly_assigned_tasks']->isNotEmpty() ||
            $data['project_updates']->isNotEmpty();
    }
}
