<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('system.index'))->assertRedirect(route('login'));
});

test('system page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('system.index'))->assertOk();
});

test('system page links to the catalog management pages', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('system.index'))
        ->assertSeeHtml(route('exercises.index'))
        ->assertSeeHtml(route('exercise-categories.index'))
        ->assertSeeHtml(route('exercise-types.index'))
        ->assertSeeHtml(route('fields.index'));
});
