<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\ExposeSection;

class ExposeSectionTest extends BaseTestCase
{
    public function testExpose(): void
    {
        $expose = new ExposeSection([3000, '8000']);
        $this->assertEquals([3000, 8000], $expose->getData());
        $this->assertTrue($expose->isOn(3000));
        $this->assertTrue($expose->isOn(8000));
        $this->assertFalse($expose->isOn(18000));
        $expose->off(3000);
        $this->assertFalse($expose->isOn(3000));
        $this->assertTrue($expose->isOn(8000));
        $this->assertFalse($expose->isOn(18000));
        $this->assertSame([8000], $expose->getData());
        $expose->on(123);
        $this->assertEquals([8000, 123], $expose->getData());
        $expose->clear();
        $this->assertNull($expose->getData());
    }

    public function testKey(): void
    {
        $expose = new ExposeSection([123]);
        $expose->on(234, 'first');
        $expose->on(345, 'second');
        $this->assertSame(345, $expose->getByKey('second'));
        $this->assertNull($expose->getByKey('third'));
        $expose->keyOff('second');
        $expose->keyOff('third');
        $this->assertSame(345, $expose->getByKey('second'));
        $this->assertEquals([123, 234], $expose->getData());
        $expose->keyOn('second');
        $this->assertEquals([123, 234, 345], $expose->getData());
    }

    public function testGroup(): void
    {
        $expose = new ExposeSection([123]);
        $expose->on(234, null, 'second');
        $expose->on(235, null, 'second');
        $expose->on(334, null, 'third');
        $expose->on(335, null, 'third');
        $this->assertEquals([123, 234, 235, 334, 335], $expose->getData());
        $expose->groupOff('second');
        $this->assertEquals([123, 334, 335], $expose->getData());
        $expose->groupOff('third');
        $expose->groupOff('fourth');
        $this->assertEquals([123], $expose->getData());
        $expose->groupOn('second');
        $expose->groupOn('fourth');
        $this->assertEquals([123, 234, 235], $expose->getData());
    }
}
