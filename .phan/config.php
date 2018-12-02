<?php
use \Phan\Config;
use \Phan\Issue;

return [
    'quick_mode' => false,
    'exclude_analysis_directory_list' => [
        'vendor/'
    ],
    'processes' => 2,
    'analyze_signature_compatibility' => true,
    'dead_code_detection' => true,
    'minimum_severity' => Issue::SEVERITY_CRITICAL,
    // 'directory_list' => [
    //     'module',
    //     'public',
    //     'config'
    // ]
];
