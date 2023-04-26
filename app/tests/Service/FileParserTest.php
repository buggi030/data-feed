<?php

namespace Tests\Service;

use App\Entity\Coffee;
use App\Exception\AppException;
use App\Exception\FileNotFoundException;
use App\Exception\UnsupportedFileTypeException;
use App\Serializer\XMLArrayEncoder;
use App\Service\FileParser;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FileParserTest extends TestCase
{
    public function testSuccessfullParse()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], __DIR__.'/resources/data.xml');

        $result = $parser->parse();
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $item = $result[0];
        $this->assertTrue($item instanceof Coffee);

        self::assertSame(370, $item->getId(), 'check id');
        self::assertSame('Green Mountain Ground Coffee', $item->getCategoryName(), 'check categoryName');
        self::assertSame('20', $item->getSku(), 'check sku');
        self::assertSame('Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag', $item->getName(), 'check name');
        self::assertSame('', $item->getDescription(), 'check description');
        self::assertSame('Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag steeps cup after cup of smoky-sweet, complex dark roast coffee from Green Mountain Ground Coffee.', $item->getShortDesc(), 'check shortDesc');
        self::assertSame(41.6, $item->getPrice(), 'check price');
        self::assertSame('http://www.coffeeforless.com/green-mountain-coffee-french-roast-ground-coffee-24-2-2oz-bag.html', $item->getLink(), 'check link');
        self::assertSame('http://mcdn.coffeeforless.com/media/catalog/product/images/uploads/intro/frac_box.jpg', $item->getImage(), 'check image');
        self::assertSame('Green Mountain Coffee', $item->getBrand(), 'check brand');
        self::assertSame(0, $item->getRating(), 'check rating');
        self::assertSame('Caffeinated', $item->getCaffeineType(), 'check caffeineType');
        self::assertSame(24, $item->getCount(), 'check count');
        self::assertSame('No', $item->getFlavored(), 'check flavored');
        self::assertSame('No', $item->getSeasonal(), 'check seasonal');
        self::assertSame('Yes', $item->getInStock(), 'check inStock');
        self::assertSame(true, $item->isFacebook(), 'check facebook');
        self::assertSame(false, $item->isKCup(), 'check isKCup');
    }

    public function testParseOneRecord()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], __DIR__.'/resources/one.xml');
        $result = $parser->parse();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame(Coffee::class, get_class($result[0]));
        $this->assertSame(342, $result[0]->getId(), 'check id');
    }

    public function testParseEmptyXML()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], __DIR__.'/resources/empty.xml');

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Invalid XML data, it cannot be empty.');

        $parser->parse();
    }

    public function testParseUnsupportedFileType()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], __DIR__.'/resources/feed.json');

        $this->expectException(UnsupportedFileTypeException::class);
        $this->expectExceptionMessage('File type is not supported');

        $parser->parse();
    }

    public function testFileNotExist(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], 'not_exist.xml');

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found');

        $parser->parse();
    }

    public function testParseDifferentStructureXML()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $encoder = new XMLArrayEncoder();
        $parser = new FileParser($logger, [$encoder], __DIR__.'/resources/brokenData.xml');

        $result = $parser->parse();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
