<?php
/**
 * This is an automatically generated baseline for Phan issues.
 * When Phan is invoked with --load-baseline=path/to/baseline.php,
 * The pre-existing issues listed in this file won't be emitted.
 *
 * This file can be updated by invoking Phan with --save-baseline=path/to/baseline.php
 * (can be combined with --load-baseline)
 */
return [
    // # Issue statistics:
    // PhanUnreferencedPublicMethod : 50+ occurrences
    // PhanAccessMethodInternal : 30+ occurrences
    // PhanUndeclaredStaticMethod : 30+ occurrences
    // PhanUndeclaredMethod : 5 occurrences
    // PhanUnreferencedClass : 4 occurrences
    // PhanRedefinedExtendedClass : 2 occurrences
    // PhanTypePossiblyInvalidDimOffset : 1 occurrence

    // Currently, file_suppressions and directory_suppressions are the only supported suppressions
    'file_suppressions' => [
        'src/Event.php' => ['PhanUnreferencedPublicMethod'],
        'src/Period.php' => ['PhanAccessMethodInternal', 'PhanUnreferencedPublicMethod'],
        'src/Properties/Origin.php' => ['PhanUnreferencedPublicMethod'],
        'src/Section.php' => ['PhanUnreferencedPublicMethod'],
        'src/Stopwatch.php' => ['PhanUnreferencedPublicMethod'],
        'src/Utils/Formatter.php' => ['PhanTypePossiblyInvalidDimOffset', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'src/Utils/MemoryCalc.php' => ['PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/EventTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
        'tests/StopwatchTest.php' => ['PhanAccessMethodInternal', 'PhanRedefinedExtendedClass', 'PhanUndeclaredMethod', 'PhanUndeclaredStaticMethod', 'PhanUnreferencedClass', 'PhanUnreferencedPublicMethod'],
    ],
    // 'directory_suppressions' => ['src/directory_name' => ['PhanIssueName1', 'PhanIssueName2']] can be manually added if needed.
    // (directory_suppressions will currently be ignored by subsequent calls to --save-baseline, but may be preserved in future Phan releases)
];
