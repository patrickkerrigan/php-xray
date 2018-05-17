<?php

namespace Pkerrigan\Xray;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 17/05/2018
 */
class SqlSegmentTest extends TestCase
{
    public function testSerialisesCorrectly()
    {
        $segment = new SqlSegment();
        $segment->setQuery('SELECT *')
            ->setDatabaseType('PostgreSQL')
            ->setDatabaseVersion('10.4')
            ->setDriverVersion('10')
            ->setPreparation('prepared')
            ->setUser('test')
            ->setUrl('pgsql://test@localhost');

        $serialised = $segment->jsonSerialize();

        $this->assertEquals('remote', $serialised['namespace']);
        $this->assertEquals('SELECT *', $serialised['sql']['sanitized_query']);
        $this->assertEquals('PostgreSQL', $serialised['sql']['database_type']);
        $this->assertEquals('10.4', $serialised['sql']['database_version']);
        $this->assertEquals('10', $serialised['sql']['driver_version']);
        $this->assertEquals('test', $serialised['sql']['user']);
        $this->assertEquals('prepared', $serialised['sql']['preparation']);
        $this->assertEquals('pgsql://test@localhost', $serialised['sql']['url']);
    }
}
