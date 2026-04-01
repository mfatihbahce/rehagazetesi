<?php

return [
    'enabled' => env('ARCHIVE_ENABLED', false),
    'site_url' => env('ARCHIVE_SITE_URL', 'https://arsiv.rehagazetesi.com'),
    'news_path_prefix' => env('ARCHIVE_NEWS_PATH_PREFIX', 'kose-yazilari'),

    'tables' => [
        'users' => env('ARCHIVE_USERS_TABLE', 'users'),
        'news' => env('ARCHIVE_NEWS_TABLE', 'news'),
        'postmeta' => env('ARCHIVE_POSTMETA_TABLE', 'wp_postmeta'),
    ],

    'columns' => [
        'news' => [
            'primary_key' => env('ARCHIVE_NEWS_PRIMARY_KEY', 'id'),
            'author_key' => env('ARCHIVE_NEWS_AUTHOR_KEY', 'user_id'),
            'title' => env('ARCHIVE_NEWS_TITLE_COLUMN', 'title'),
            'slug' => env('ARCHIVE_NEWS_SLUG_COLUMN', 'slug'),
            'status' => env('ARCHIVE_NEWS_STATUS_COLUMN', 'status'),
            'published_at' => env('ARCHIVE_NEWS_PUBLISHED_AT_COLUMN', 'created_at'),
            'excerpt' => env('ARCHIVE_NEWS_EXCERPT_COLUMN', 'post_excerpt'),
            'featured_image_meta_key' => env('ARCHIVE_NEWS_FEATURED_IMAGE_META_KEY', '_thumbnail_id'),
        ],
    ],
];
