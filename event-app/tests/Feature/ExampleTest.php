<?php

it('redirects to event page', function () {
    $response = $this->get('/');

    $response->assertRedirect('/event');
});
