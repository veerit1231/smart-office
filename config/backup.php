<?php

return [

    'backup' => [

        /*
         * Name of the application
         */
        'name' => env('APP_NAME', 'smart-office'),

        'source' => [

            /*
             * ❌ ไม่ backup files
             */
            'files' => [
                'include' => [],
                'exclude' => [],
                'follow_links' => false,
                'ignore_unreadable_directories' => true,
                'relative_path' => null,
            ],

            /*
             * ✅ Backup เฉพาะ database
             */
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        /*
         * ไม่บีบอัด dump
         */
        'database_dump_compressor' => null,

        'database_dump_file_timestamp_format' => null,

        'database_dump_filename_base' => 'database',

        'database_dump_file_extension' => '',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 9,
            'filename_prefix' => '',

            /*
             * ✅ เก็บลงเครื่องอย่างเดียว
             */
            'disks' => [
                'local',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => null,
        'encryption' => false,

        'tries' => 1,
        'retry_delay' => 0,
    ],

    /*
     * ❌ ปิด notification ทั้งหมด (ไม่จำเป็น)
     */
    'notifications' => [],

    /*
     * ❌ ปิด monitoring (ไม่จำเป็น)
     */
    'monitor_backups' => [],

];
