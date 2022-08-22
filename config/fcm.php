<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => "AAAAoRwPLHE:APA91bE4yKLh0CrwaAH4DhaYgvqxImi1c2zTKSWswvCsOsqsKK8Kwmdu4NCWDenAek8fDdUjcJ3WsvwIJ7oVNy4oW8wWu7Gt_3OBJhyJXmMyzIwp-mPKwvGUUZe7ND-EP3G0L5NFOxi-",
        'sender_id' => "691960491121",
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 10.0, // in second
    ],
];
