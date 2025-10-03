<?php

use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new ProjectPolicy();

    // Create roles and permissions
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);
    Permission::create(['name' => 'view projects']);
});

describe('ProjectPolicy', function () {
    describe('viewAny', function () {
        it('allows users with view projects permission', function () {
            $user = User::factory()->create();
            $user->givePermissionTo('view projects');

            expect($this->policy->viewAny($user))->toBeTrue();
        });

        it('denies users without view projects permission', function () {
            $user = User::factory()->create();

            expect($this->policy->viewAny($user))->toBeFalse();
        });
    });

    describe('view', function () {
        it('allows project owner to view their project', function () {
            $user = User::factory()->create();
            $project = Project::factory()->for($user)->create();

            expect($this->policy->view($user, $project))->toBeTrue();
        });

        it('allows admin to view any project', function () {
            $admin = User::factory()->create();
            $admin->assignRole('admin');
            $user = User::factory()->create();
            $project = Project::factory()->for($user)->create();

            expect($this->policy->view($admin, $project))->toBeTrue();
        });

        it('denies non-owner non-admin from viewing project', function () {
            $owner = User::factory()->create();
            $otherUser = User::factory()->create();
            $project = Project::factory()->for($owner)->create();

            expect($this->policy->view($otherUser, $project))->toBeFalse();
        });
    });

    describe('create', function () {
        it('allows admin to create projects', function () {
            $admin = User::factory()->create();
            $admin->assignRole('admin');

            expect($this->policy->create($admin))->toBeTrue();
        });

        it('denies non-admin from creating projects', function () {
            $user = User::factory()->create();

            expect($this->policy->create($user))->toBeFalse();
        });
    });

    describe('update', function () {
        it('allows project owner to update their project', function () {
            $user = User::factory()->create();
            $project = Project::factory()->for($user)->create();

            expect($this->policy->update($user, $project))->toBeTrue();
        });

        it('denies non-owner from updating project', function () {
            $owner = User::factory()->create();
            $otherUser = User::factory()->create();
            $project = Project::factory()->for($owner)->create();

            expect($this->policy->update($otherUser, $project))->toBeFalse();
        });
    });

    describe('delete', function () {
        it('allows project owner to delete their project', function () {
            $user = User::factory()->create();
            $project = Project::factory()->for($user)->create();

            expect($this->policy->delete($user, $project))->toBeTrue();
        });

        it('denies non-owner from deleting project', function () {
            $owner = User::factory()->create();
            $otherUser = User::factory()->create();
            $project = Project::factory()->for($owner)->create();

            expect($this->policy->delete($otherUser, $project))->toBeFalse();
        });
    });
});
