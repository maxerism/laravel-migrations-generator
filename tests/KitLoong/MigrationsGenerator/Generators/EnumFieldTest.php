<?php
/**
 * Created by PhpStorm.
 * User: liow.kitloong
 * Date: 2020/03/31
 * Time: 12:22
 */

namespace Tests\KitLoong\MigrationsGenerator\Generators;

use Illuminate\Support\Facades\DB;
use KitLoong\MigrationsGenerator\MigrationGeneratorSetting;
use KitLoong\MigrationsGenerator\Generators\EnumField;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;

class EnumFieldTest extends TestCase
{
    public function testMakeField()
    {
        /** @var EnumField $enumField */
        $enumField = resolve(EnumField::class);

        $field = [
            'field' => 'enum_field',
            'args' => []
        ];

        $this->mock(MigrationGeneratorSetting::class, function (MockInterface $mock) {
            $mock->shouldReceive('getConnection');
        });

        DB::shouldReceive('connection->select')
            ->with("SHOW COLUMNS FROM `table` where Field = 'enum_field' AND Type LIKE 'enum(%'")
            ->andReturn([
                (object) ['Type' => "enum('value1', 'value2' , 'value3')"]
            ])
            ->once();

        $field = $enumField->makeField('table', $field);
        $this->assertSame(["['value1', 'value2' , 'value3']"], $field['args']);
    }
}
