<?php

// Force the VIEW_COMPILED_PATH to /tmp/views to fix Railway deployment issues
if (!isset($_ENV['VIEW_COMPILED_PATH'])) {
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';
    putenv('VIEW_COMPILED_PATH=/tmp/views');
}

// Ensure the directory exists
if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}