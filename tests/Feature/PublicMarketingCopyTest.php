<?php

use Inertia\Testing\AssertableInertia as Assert;

test('public marketing pages are reachable', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Welcome'));

    $this->get(route('features'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Features'));

    $this->get(route('pricing'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Pricing'));

    $this->get(route('faq'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('FAQ'));
});

test('public marketing copy does not sell a live Pro plan or unbuilt features', function () {
    $files = [
        resource_path('js/pages/Welcome.vue'),
        resource_path('js/pages/Features.vue'),
        resource_path('js/pages/Pricing.vue'),
        resource_path('js/pages/FAQ.vue'),
        resource_path('js/components/PublicHeader.vue'),
        resource_path('js/components/PublicFooter.vue'),
        resource_path('js/components/PricingTable.vue'),
    ];

    $copy = collect($files)
        ->map(fn (string $path) => file_get_contents($path))
        ->implode("\n");

    $forbidden = [
        '£6.99',
        '£49.99',
        'Squadra365 Pro',
        'Pro Tier',
        'Free Tier',
        'Session Planner',
        'Parent-to-Parent',
        'placehold.co',
        'App screenshot',
        'soccer',
    ];

    foreach ($forbidden as $claim) {
        expect($copy)->not->toContain($claim);
    }

    expect($copy)
        ->toContain('join code')
        ->toContain('Reminders')
        ->toContain('Matchday')
        ->toContain('Club Portal')
        ->toContain('Coming later, if we need it')
        ->toContain('SMS frost');
});
