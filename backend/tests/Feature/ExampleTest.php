<?php

test('example', function () {
    $response = $this->get('/');

    // Root route redirects to admin panel, so expect 302
    $response->assertStatus(302);
});
