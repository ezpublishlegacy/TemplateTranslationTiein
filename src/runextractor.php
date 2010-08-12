<?php
/**
 * File running the ezcTemplateTranslationExtractor class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package TemplateTranslationTiein
 * @version //autogentag//
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

/**
 * Require the base class
 */
// Silenced warning here, will be handeled below, if second include fails.
if ( ( @include dirname( __FILE__ ) . '/../../Base/src/base.php' ) === false )
{
    // Silenced warning here, will be handeled below, if second include fails.
    if ( ( @include dirname( __FILE__ ) . '/../Base/base.php' ) === false )
    {
        echo <<<EOT
eZ components environment not setup correctly. Could neither include eZ Base
component from 'Base/src/base.php', nor from 'Base/base.php'. Please check your
include path!

EOT;
        exit( -1 );
    }
}

function __autoload( $className )
{
    ezcBase::autoload( $className );
}

$extractor = new ezcTemplateTranslationExtractor();
$extractor->run();

?>
