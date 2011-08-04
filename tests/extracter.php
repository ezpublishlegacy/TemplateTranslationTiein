<?php
/**
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
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version //autogentag//
 * @filesource
 * @package TemplateTranslationTiein
 * @subpackage Tests
 */

/**
 * @package TemplateTranslationTiein
 * @subpackage Tests
 */
class ezcTemplateTranslationExtracterTest extends ezcTestCase
{
    function testExtracter()
    {
        $file = dirname( __FILE__ ) . '/test_files/test.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getTranslation();
        self::assertEquals( 
            array( 'een', 'twee', 'drie', 'vier', 'vijf', 'zes', 'zeven', 'acht', 'negen', 'tien', 'elf' ),
            array_keys( $this->readAttribute( $tr['test'], 'translationMap' ) ) 
        );
    }

    function testExtracterWithoutDefaultContext()
    {
        $file = dirname( __FILE__ ) . '/test_files/test_without_default_context.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getTranslation();
        self::assertEquals( 
            array( 'een', 'twee', 'drie', 'vier', 'vijf', 'zes', 'zeven', 'acht', 'negen', 'tien', 'elf' ),
            array_keys( $this->readAttribute( $tr['test'], 'translationMap' ) ) 
        );
    }

    function testExtracterWithoutContext()
    {
        $file = dirname( __FILE__ ) . '/test_files/test_without_context.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        try
        {
            $eted = $tst->accept( $et );
            self::fail( "Expected exception not thrown." );
        }
        catch ( ezcTemplateParserException $e )
        {
            self::assertEquals( "$file:3:11: Expecting a 'context' parameter, or a default context set with {tr_context}.\n\n{tr \"een\"}\n          ^\n", $e->getMessage() );
        }
    }

    function testExtracterWithQuotes()
    {
        $file = dirname( __FILE__ ) . '/test_files/test-quotes.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getStrings();

        self::assertEquals( 
            array( 'Test quotes: \'test\'.', 'Test quotes: "test".', 'Test quotes: \'test\' "test".', 'Test quotes: "test" \'test\'.' ),
            array_keys( $tr['un'] ) 
        );
    }

    function testExtracterWithComments()
    {
        $file = dirname( __FILE__ ) . '/test_files/test_with_comments.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getTranslation();

        self::assertEquals(
            array( 'foo' ),
            array_keys( $this->readAttribute( $tr['test'], 'translationMap' ) )
        );

        $translationMap = $this->readAttribute( $tr['test'], 'translationMap' );
        self::assertEquals( "some comment", $translationMap['foo']->comment );
    }

    function testUnifyFileName()
    {
        ob_start();
        $extractor = new ezcTemplateTranslationExtractor();
        ob_end_clean();
        $this->assertSame(
            'foo.txt',
            $extractor->unifyFilepath( '/path/to/foo.txt', '/path/to' )
        );
    }

    function testUnifyFileNameTrailingSlash()
    {
        ob_start();
        $extractor = new ezcTemplateTranslationExtractor();
        ob_end_clean();
        $this->assertSame(
            'foo.txt',
            $extractor->unifyFilepath( '/path/to/foo.txt', '/path/to/' )
        );
    }

    function testUnifyFileNameSubDir()
    {
        ob_start();
        $extractor = new ezcTemplateTranslationExtractor();
        ob_end_clean();
        $this->assertSame(
            'bar/foo.txt',
            $extractor->unifyFilepath( '/path/to/bar/foo.txt', '/path/to/' )
        );
    }

    function testUnifyFileNameWin()
    {
        ob_start();
        $extractor = new ezcTemplateTranslationExtractor();
        ob_end_clean();
        $this->assertSame(
            'foo.txt',
            $extractor->unifyFilepath( '\\path\\to\\foo.txt', '\\path\\to' )
        );
    }

    public function testRunNonEztTemplateExtension()
    {
        $tmpDir = sys_get_temp_dir();
        $testFileDir = "TemplateTranslationTiein/tests/test_files/extractor/templates/customtplextension";
        $tmpFile = "${tmpDir}/en.xml";

        $this->runExtractorCommand( $testFileDir, 'tpl', $tmpDir );

        $this->assertEquals(
            file_get_contents( "${testFileDir}/output.xml" ),
            file_get_contents( $tmpFile )
        );

        unlink( $tmpFile );
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( 'ezcTemplateTranslationExtracterTest' );
    }

    private function runExtractorCommand( $inputDir, $fileExt, $outputDir )
    {
        $origArgv = $_SERVER['argv'];

        ob_start();
        $extractor = new ezcTemplateTranslationExtractor();

        $_SERVER['argv'] = array (
            0 => 'TemplateTranslationTiein/src/runextractor.php',
            1 => '-t',
            2 => $inputDir,
            3 => '-e',
            4 => $fileExt,
            5 => $outputDir,
        );

        $extractor->run();
        ob_end_clean();

        $_SERVER['argv'] = $origArgv;
    }
}

?>
