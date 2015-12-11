<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\Test\Unit\Model;

use Magento\Elasticsearch\Model\Config;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Elasticsearch\Model\Adapter\ElasticsearchFactory;

/**
 * Class ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $encryptor;

    /**
     * @var ElasticsearchFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterFactory;

    /**
     * Setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->encryptor = $this->getMockBuilder('Magento\Framework\Encryption\EncryptorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->adapterFactory = $this->getMockBuilder('Magento\Elasticsearch\Model\Adapter\ElasticsearchFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $objectManager = new ObjectManagerHelper($this);
        $this->model = $objectManager->getObject(
            '\Magento\Elasticsearch\Model\Config',
            [
                'scopeConfig' => $this->scopeConfig,
                'encryptor' => $this->encryptor,
                'adapterFactory' => $this->adapterFactory
            ]
        );
    }

    /**
     * Test prepareClientOptions() method
     */
    public function testPrepareClientOptions()
    {
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->willReturn('');
        $options = [
            'hostname' => 'localhost',
            'port' => '9200',
            'index' => 'magento2',
            'enableAuth' => '1',
            'username' => 'user',
            'password' => 'pass',
            'timeout' => 1,
        ];
        $this->assertEquals($options, $this->model->prepareClientOptions($options));
    }

    /**
     * Test getIndexerPrefix() method
     */
    public function testGetIndexerPrefix()
    {
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->willReturn('indexerPrefix');
        $this->assertEquals('indexerPrefix', $this->model->getIndexerPrefix());
    }

    /**
     * Test getEntityType() method
     */
    public function testGetEntityType()
    {
        $this->assertInternalType('string', $this->model->getEntityType());
    }

    /**
     * Test getEntityType() method
     */
    public function testIsElasticsearchEnabled()
    {
        $this->assertFalse($this->model->isElasticsearchEnabled());
    }
}
