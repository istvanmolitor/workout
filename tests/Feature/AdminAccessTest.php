<?php

use App\Models\User;

test('non-admin users cannot access the system pages', function (string $route) {
    $this->actingAs(User::factory()->create());

    $this->get($route)->assertForbidden();
})->with([
    fn () => route('system.index'),
    fn () => route('exercises.index'),
    fn () => route('exercise-categories.index'),
    fn () => route('exercise-types.index'),
    fn () => route('fields.index'),
]);

test('admin users can access the system pages', function (string $route) {
    $this->actingAs(User::factory()->admin()->create());

    $this->get($route)->assertOk();
})->with([
    fn () => route('system.index'),
    fn () => route('exercises.index'),
    fn () => route('exercise-categories.index'),
    fn () => route('exercise-types.index'),
    fn () => route('fields.index'),
]);

test('the system menu item is hidden from non-admin users', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))->assertDontSee(route('system.index'));
});

test('the system menu item is visible to admin users', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('dashboard'))->assertSeeHtml(route('system.index'));
});
