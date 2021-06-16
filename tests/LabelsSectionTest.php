<?php

declare(strict_types=1);

namespace axy\docker\compose\config\tests;

use axy\docker\compose\config\LabelsSection;

class LabelsSectionTest extends BaseTestCase
{
    public function testEmpty(): void
    {
        $labels = new LabelsSection(null);
        $this->assertNull($labels->getData());
        $labels['x'] = 'y';
        $this->assertEquals([
            'x' => 'y',
        ], $labels->getData());
    }

    public function testDict(): void
    {
        $labels = new LabelsSection([
            'a' => 'x',
            'b' => 'y',
        ]);
        $this->assertEquals([
            'a' => 'x',
            'b' => 'y',
        ], $labels->getData());
    }

    public function testList(): void
    {
        $labels = new LabelsSection([
            'com.example.description=Accounting webapp',
            'com.example.department=Finance=Finance',
            'com.example.label-with-empty-value',
        ]);
        $this->assertEquals([
            'com.example.description' => 'Accounting webapp',
            'com.example.department' => 'Finance=Finance',
            'com.example.label-with-empty-value' => '',
        ], $labels->getData());
    }

    public function testString(): void
    {
        $labels = new LabelsSection('com.example.description=Accounting webapp');
        $this->assertEquals([
            'com.example.description' => 'Accounting webapp',
        ], $labels->getData());
    }
}
