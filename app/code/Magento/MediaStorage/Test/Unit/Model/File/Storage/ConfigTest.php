<?php declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaStorage\Test\Unit\Model\File\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\Write;
use Magento\MediaStorage\Model\File\Storage;
use Magento\MediaStorage\Model\File\Storage\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Test for save method
     */
    public function testSave()
    {
        $config = [];
        $fileStorageMock = $this->createMock(Storage::class);
        $fileStorageMock->expects($this->once())->method('getScriptConfig')->will($this->returnValue($config));

        $file = $this->getMockBuilder(Write::class)
            ->setMethods(['lock', 'write', 'unlock', 'close'])
            ->disableOriginalConstructor()
            ->getMock();
        $file->expects($this->once())->method('lock');
        $file->expects($this->once())->method('write')->with(json_encode($config));
        $file->expects($this->once())->method('unlock');
        $file->expects($this->once())->method('close');
        $directory = $this->createPartialMock(
            \Magento\Framework\Filesystem\Directory\Write::class,
            ['openFile', 'getRelativePath']
        );
        $directory->expects($this->once())->method('getRelativePath')->will($this->returnArgument(0));
        $directory->expects($this->once())->method('openFile')->with('cacheFile')->will($this->returnValue($file));
        $filesystem = $this->createPartialMock(Filesystem::class, ['getDirectoryWrite']);
        $filesystem->expects(
            $this->once()
        )->method(
            'getDirectoryWrite'
        )->with(
            DirectoryList::ROOT
        )->will(
            $this->returnValue($directory)
        );
        $model = new Config($fileStorageMock, $filesystem, 'cacheFile');
        $model->save();
    }
}
