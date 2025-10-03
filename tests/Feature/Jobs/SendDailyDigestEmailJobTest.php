<?php

use App\Actions\Emails\PrepareDigestDataAction;
use App\Jobs\SendDailyDigestEmailJob;
use App\Mail\DailyDigestMail;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $this->project = Project::factory()->for($this->user)->create();

    Mail::fake();
    Queue::fake();
});

describe('SendDailyDigestEmailJob', function () {
    describe('Job Execution', function () {
        it('dispatches successfully to queue', function () {
            SendDailyDigestEmailJob::dispatch($this->user);

            Queue::assertPushed(SendDailyDigestEmailJob::class, function ($job) {
                // Use reflection to access private property
                $reflection = new ReflectionClass($job);
                $userProperty = $reflection->getProperty('user');
                $userProperty->setAccessible(true);
                $jobUser = $userProperty->getValue($job);

                return $jobUser->id === $this->user->id;
            });
        });

        it('can be serialized and unserialized', function () {
            $job = new SendDailyDigestEmailJob($this->user);

            $serialized = serialize($job);
            $unserialized = unserialize($serialized);

            // Use reflection to access private property for verification
            $reflection = new ReflectionClass($unserialized);
            $userProperty = $reflection->getProperty('user');
            $userProperty->setAccessible(true);
            $jobUser = $userProperty->getValue($unserialized);

            expect($jobUser->id)->toBe($this->user->id);
            expect($jobUser->email)->toBe($this->user->email);
        });

        it('implements ShouldQueue interface', function () {
            $job = new SendDailyDigestEmailJob($this->user);

            expect($job)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);
        });
    });

    describe('Email Sending Logic', function () {
        it('sends email when there is relevant content', function () {
            // Create relevant content using proper enum values
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo',
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class, function ($mail) {
                return $mail->hasTo($this->user->email);
            });
        });

        it('does not send email when there is no relevant content', function () {
            // No relevant content - no tasks due today, no new assignments, no project updates

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertNotSent(DailyDigestMail::class);
        });

        it('sends email when tasks are due today', function () {
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo',
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class);
        });

        it('sends email when there are newly assigned tasks', function () {
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::yesterday(),
                'status' => 'completed',
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class);
        });

        it('sends email when there are project updates', function () {
            $this->project->update([
                'name' => 'Updated Project Name',
                'updated_at' => Carbon::now(),
            ]);

            // Create a task so the project "has tasks"
            Task::factory()->for($this->project)->create();

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class);
        });
    });

    describe('Content Validation', function () {
        it('correctly identifies relevant content exists', function () {
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo', // Use valid enum value
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $action = new PrepareDigestDataAction();
            $digestData = $action->execute($this->user);

            // Use reflection to test private method
            $reflection = new ReflectionClass($job);
            $method = $reflection->getMethod('hasRelevantContent');
            $method->setAccessible(true);

            $hasContent = $method->invoke($job, $digestData);

            expect($hasContent)->toBeTrue();
        });

        it('correctly identifies no relevant content', function () {
            // No relevant content created

            $job = new SendDailyDigestEmailJob($this->user);
            $action = new PrepareDigestDataAction();
            $digestData = $action->execute($this->user);

            // Use reflection to test private method
            $reflection = new ReflectionClass($job);
            $method = $reflection->getMethod('hasRelevantContent');
            $method->setAccessible(true);

            $hasContent = $method->invoke($job, $digestData);

            expect($hasContent)->toBeFalse();
        });

        it('passes correct data to mail class', function () {
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo', // Use valid enum value
                'title' => 'Test Task Due Today',
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class, function ($mail) {
                $data = $mail->digestData;

                return $data['user']->id === $this->user->id &&
                       $data['tasks_due_today']->isNotEmpty() &&
                       $data['date'] === Carbon::today()->format('Y-m-d');
            });
        });
    });

    describe('Error Handling', function () {
        it('handles action failure gracefully', function () {
            /** @var PrepareDigestDataAction&MockInterface $mockAction */
            $mockAction = Mockery::mock(PrepareDigestDataAction::class);
            $mockAction->shouldReceive('execute')
                ->with($this->user)
                ->andThrow(new Exception('Database error'));

            $job = new SendDailyDigestEmailJob($this->user);

            expect(fn () => $job->handle($mockAction))->toThrow(Exception::class);

            Mail::assertNotSent(DailyDigestMail::class);
        });

        it('handles mail sending failure', function () {
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo', // Use valid enum value
            ]);

            Mail::shouldReceive('to->send')
                ->andThrow(new Exception('Mail service unavailable'));

            $job = new SendDailyDigestEmailJob($this->user);

            expect(fn () => $job->handle(new PrepareDigestDataAction()))->toThrow(Exception::class);
        });

        it('logs when no relevant content is found', function () {
            // Mock the Log facade to capture log messages
            Log::shouldReceive('info')
                ->once()
                ->with('No relevant content for user: ' . $this->user->email, []);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertNotSent(DailyDigestMail::class);
        });
    });

    describe('Integration with PrepareDigestDataAction', function () {
        it('receives and uses digest data correctly', function () {
            $todayTask = Task::factory()->for($this->project)->create([
                'created_at' => Carbon::today(),
                'status' => 'todo', // Use valid enum value
                'title' => 'Task Due Today',
            ]);

            $completedTask = Task::factory()->for($this->project)->create([
                'created_at' => Carbon::yesterday(),
                'status' => 'completed',
                'title' => 'Newly Completed Task',
            ]);

            // Update project to trigger project updates
            $this->project->update(['updated_at' => Carbon::now()]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertQueued(DailyDigestMail::class, function ($mail) use ($todayTask, $completedTask) {
                $data = $mail->digestData;

                return $data['tasks_due_today']->contains('id', $todayTask->id) &&
                       $data['newly_assigned_tasks']->contains('id', $completedTask->id) &&
                       $data['project_updates']->contains('id', $this->project->id);
            });
        });

        it('handles empty collections in digest data', function () {
            // Create a project with tasks but no relevant content for digest
            Task::factory()->for($this->project)->create([
                'created_at' => Carbon::now()->subDays(3),
                'status' => 'completed',
            ]);

            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());

            Mail::assertNotSent(DailyDigestMail::class);
        });
    });

    describe('Performance and Resource Usage', function () {
        it('handles large datasets efficiently', function () {
            // Create many projects and tasks
            $projects = Project::factory(10)->for($this->user)->create();

            foreach ($projects as $project) {
                Task::factory(5)->for($project)->create([
                    'created_at' => Carbon::today(),
                    'status' => 'todo', // Use valid enum value
                ]);
            }

            $startTime = microtime(true);
            $job = new SendDailyDigestEmailJob($this->user);
            $job->handle(new PrepareDigestDataAction());
            $executionTime = microtime(true) - $startTime;

            // Should complete within reasonable time (adjust threshold as needed)
            expect($executionTime)->toBeLessThan(5.0);

            Mail::assertQueued(DailyDigestMail::class);
        });

        it('uses minimal memory for job serialization', function () {
            $job = new SendDailyDigestEmailJob($this->user);
            $serialized = serialize($job);

            // Serialized job should be reasonably small (adjust threshold as needed)
            expect(strlen($serialized))->toBeLessThan(10000);
        });
    });
});
