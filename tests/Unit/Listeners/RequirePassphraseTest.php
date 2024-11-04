<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners;

use BaseCodeOy\Passwordless\Listeners\RequirePassphrase;
use BaseCodeOy\Passwordless\Notifications\SendPassphrase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\Fixtures\User;

beforeEach(fn () => $this->subject = new RequirePassphrase());

it('should send a notification', function (): void {
    Notification::fake();

    $this->subject->handle((object) [
        'user' => $user = new User(),
    ]);

    Notification::assertSentTo($user, SendPassphrase::class);
});

it('should not send a notification if the user is already authenticated', function (): void {
    Notification::fake();

    Auth::shouldReceive('viaRemember')->once()->andReturn(true);

    $this->subject->handle((object) [
        'user' => new User(),
    ]);

    Notification::assertNothingSent();
});
