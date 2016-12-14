<?php
/**
 * (c) phpio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpio\App;

/**
 * Compare file names from the $path1 with ones from the $path2 and creates an array with full file paths from
 * the $path1 as keys and file paths from the $path2 (if exists) as values
 *
 * example:
 *
 * path1/a.php
 * path1/b.php
 * path2/a.php
 * path2/c.php
 *
 * ['path1/a.php' => 'path2/a.php', 'path1/b.php' => null]
 */
class FilesIntersection implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $path1;

    /**
     * @var string
     */
    private $path2;

    /**
     * @var string
     */
    private $extension;

    /**
     * @param string $path1
     * @param string $path2
     * @param string $extension
     */
    public function __construct($path1, $path2, $extension = null)
    {
        $this->path1     = $path1;
        $this->path2     = $path2;
        $this->extension = $extension;
    }

    /**
     * @return string[]|\ArrayIterator
     */
    public function getIterator()
    {
        $files2       = $this->getFiles($this->path2);
        $intersection = [];
        foreach ($this->getFiles($this->path1) as $fileName => $filePath) {
            $intersection[$filePath] = isset($files2[$fileName]) ? $files2[$fileName] : null;
        }
        return new \ArrayIterator($intersection);
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function getFiles($path)
    {
        $files = [];
        if (!file_exists($path) || !is_dir($path)) {
            return [];
        }
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }
            if ($this->extension && $fileInfo->getExtension() !== $this->extension) {
                continue;
            }
            $files[$fileInfo->getFilename()] = $fileInfo->getRealPath();
        }
        return $files;
    }
}