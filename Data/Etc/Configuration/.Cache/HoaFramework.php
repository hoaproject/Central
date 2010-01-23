<?php

/**
 * Generated the 2010-01-23T20:02:27.000000Z
 */

return array (
  'keywords' => 
  array (
    'root.ofFrameworkDirectory' => NULL,
  ),
  'parameters' => 
  array (
    'root' => '(:root.ofFrameworkDirectory:)',
    'root.framework' => '(:%root:)',
    'root.data' => '(:%root:h:)/Data',
    'root.application' => '(:%root:h:)/Application',
    'framework.core' => '(:%root.framework:)/Core',
    'framework.library' => '(:%root.framework:)/Library',
    'framework.module' => '(:%root.framework:)/Module',
    'framework.optional' => '(:%root.framework:)/Optional',
    'data.module' => '(:%root.data:)/Module',
    'data.optional' => '(:%root.data:)/Optional',
    'protocol.Application' => '(:%root.application:)/',
    'protocol.Data' => '(:%root.data:)/',
    'protocol.Data/Etc' => '(:%protocol.Data:)Etc/',
    'protocol.Data/Etc/Configuration' => '(:%protocol.Data/Etc:)Configuration/',
    'protocol.Data/Etc/Locale' => '(:%protocol.Data/Etc:)Locale/',
    'protocol.Data/Lost+found' => '(:%protocol.Data:)Lost+found/',
    'protocol.Data/Module' => '(:%data.module:)/',
    'protocol.Data/Optional' => '(:%data.module:)/',
    'protocol.Data/Variable' => '(:%protocol.Data:)Variable/',
    'protocol.Data/Variable/Cache' => '(:%protocol.Data/Variable:)Cache/',
    'protocol.Data/Variable/Database' => '(:%protocol.Data/Variable:)Database/',
    'protocol.Data/Variable/Log' => '(:%protocol.Data/Variable:)Log/',
    'protocol.Data/Variable/Private' => '(:%protocol.Data/Variable:)Private/',
    'protocol.Data/Variable/Test' => '(:%protocol.Data/Variable:)Test/',
    'protocol.Framework' => '(:%root.framework:)/',
  ),
);