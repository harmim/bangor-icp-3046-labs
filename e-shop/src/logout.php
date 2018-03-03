<?php

/**
 * Logout script.
 *
 * @author Dominik Harmim <harmim6@gmail.com>
 */

declare(strict_types=1);

use Main\Configuration;
use Main\Renderable;


require_once __DIR__ . '/includes/configuration.php';


// logout and redirect user
Configuration::setLoggedUser(null);
Renderable\Messages::addMessage('You have been successfully logged out.');
Configuration::redirect('index.php');
