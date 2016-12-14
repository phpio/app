<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

/**
 * @covers FilesIntersection
 */
class FilesIntersectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array[]
     */
    public function providerGetIterator()
    {
        /**
         * @param array  $expected
         * @param string $path1
         * @param string $path2
         * @param string $extension
         *
         * @return array
         */
        $test = function (array $expected, $path1, $path2, $extension = null) {
            return [
                'expected'  => $expected,
                'path1'     => $path1,
                'path2'     => $path2,
                'extension' => $extension,
            ];
        };

        /**
         * @param string $file
         *
         * @return string
         */
        $f1 = function ($file) {
            return __DIR__ . '/Fixtures/FilesIntersectionTest/' . $file;
        };

        /**
         * @param string $file
         *
         * @return string
         */
        $f2 = function ($file) {
            return __DIR__ . '/Fixtures/FilesIntersectionTest/dir.txt/' . $file;
        };

        $path1 = __DIR__ . '/Fixtures/FilesIntersectionTest';
        $path2 = 'Fixtures/FilesIntersectionTest/dir.txt';

        return [
            'ignore non-existing'    => $test([], null, $path1),

            'ignore non-directories' => $test([], __FILE__, $path2),

            'without extension'      => $test([
                $f1('A.TXT') => $f2('A.TXT'),
                $f1('A.txt') => null,
                $f1('B.txt') => $f2('B.txt'),
                $f1('b.txt') => $f2('b.txt'),
                $f1('a.TXT') => $f2('a.TXT'),
                $f1('a.txt') => null,
            ], $path1, $path2),

            'with extension'         => $test([
                $f1('A.txt') => null,
                $f1('B.txt') => $f2('B.txt'),
                $f1('b.txt') => $f2('b.txt'),
                $f1('a.txt') => null,
            ], $path1, $path2, 'txt'),
        ];
    }

    /**
     * @covers       FilesIntersection::getIterator
     * @dataProvider providerGetIterator
     *
     * @param array  $expected
     * @param string $path1
     * @param string $path2
     * @param string $extension
     */
    public function testGetIterator(array $expected, $path1, $path2, $extension)
    {
        $current = [];
        foreach (new FilesIntersection($path1, $path2, $extension) as $file1 => $file2) {
            $current[$file1] = $file2;
        }
        ksort($current);
        $this->assertEquals($expected, $current);
    }
}
