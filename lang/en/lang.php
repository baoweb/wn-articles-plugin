<?php return [
    'plugin' => [
        'name' => 'Articles',
        'description' => '',
    ],
    'permissions' => [
        'accessArticles' => 'Access articles',
        'editCategories' => 'Can edit categories',
        'editAllCategories' => 'Has access all articles',
        'editOnlySelectedArticles' => 'Can edit articles in selected categories',
        'assign_rights' => 'Assign user access to categories',
        'edit_author' => 'Change article\'s author',
    ],
    'categories' => [
        'name' => 'Name',
        'slug' => 'Slug',
    ],
    'articles' => [
        'author' => 'Author',
        'is_published' => 'Published',
        'published_at' => 'Publish date',
    ],
    'menu' => [
        'categories' => 'Categories',
        'user_rights' => 'User rights'
    ],
];
