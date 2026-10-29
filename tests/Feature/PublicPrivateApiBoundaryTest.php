<?php

use Illuminate\Support\Facades\Route;

test('applicant invitation endpoints are public but token rate limited', function (string $routeName, string $limiter) {
    $route = Route::getRoutes()->getByName($routeName);

    expect($route)->not->toBeNull();
    $middleware = $route->gatherMiddleware();

    expect($middleware)->not->toContain('jao.key');
    expect($middleware)->not->toContain('jao.hmac');
    expect($middleware)->not->toContain('web');
    expect($middleware)->toContain('throttle:'.$limiter);
})->with([
    'form' => ['application-invitations.form.show', 'public-application-form'],
    'submission' => ['application-invitations.submissions.store', 'public-application-submission'],
]);

test('public job reads do not require internal credentials', function (string $routeName) {
    $route = Route::getRoutes()->getByName($routeName);

    expect($route)->not->toBeNull();
    expect($route->gatherMiddleware())->not->toContain('jao.key');
    expect($route->gatherMiddleware())->not->toContain('jao.hmac');
    expect($route->gatherMiddleware())->toContain('throttle:public-job-hirings');
})->with([
    'list' => 'public.job-hirings.index',
    'detail' => 'public.job-hirings.show',
]);

test('administrative routes require the internal API key and HMAC', function (string $routeName) {
    $route = Route::getRoutes()->getByName($routeName);

    expect($route)->not->toBeNull();
    expect($route->gatherMiddleware())->toContain('jao.key');
    expect($route->gatherMiddleware())->toContain('jao.hmac');
})->with([
    'job hiring list' => 'job-hirings.index',
    'job hiring detail' => 'job-hirings.show',
    'job hiring create' => 'job-hirings.store',
    'job hiring update' => 'job-hirings.update',
    'job hiring delete' => 'job-hirings.destroy',
    'form administration' => 'form-templates.index',
    'application review' => 'applications.index',
]);

test('invitation creation is rate limited behind the internal API key', function () {
    $route = Route::getRoutes()->getByName('job-hirings.invitations.store');

    expect($route)->not->toBeNull();
    expect($route->gatherMiddleware())->toContain('jao.key');
    expect($route->gatherMiddleware())->toContain('throttle:private-invitation-create');
});
