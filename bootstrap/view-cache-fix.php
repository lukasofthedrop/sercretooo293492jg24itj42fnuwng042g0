<?php

// Force the VIEW_COMPILED_PATH to /app/storage/framework/views for Railway
$_ENV['VIEW_COMPILED_PATH'] = '/app/storage/framework/views';
putenv('VIEW_COMPILED_PATH=/app/storage/framework/views');

// Ensure the directory exists
if (!is_dir('/app/storage/framework/views')) {
    @mkdir('/app/storage/framework/views', 0755, true);
}
