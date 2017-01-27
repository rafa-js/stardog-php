<?php

namespace Tests\StardohPhp\Sparql;

use DataSourceBundle\Components\Normalize\LanguageNormalizer;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use StardogPhp\Sparql\QueryBuilder;
use StardogPhp\Stardog\Stardog;
use StardogPhp\Stardog\StardogBuilder;

class UpdateBuilderTest extends \PHPUnit_Framework_TestCase
{


    public function testInsert()
    {
        $expected =
            "@prefix rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "@prefix rdfs:<http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "@prefix foaf:<http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "INSERT {" . PHP_EOL .
            "\t<http://www.w3.org/People/Berners-Lee/> foaf:name \"Tim Berners Lee\"" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addInsert( "http://www.w3.org/People/Berners-Lee/", "foaf:name", "Tim Berners Lee" )
            ->buildSparqlUpdate();

        $this->assertEquals( $expected, $query );
    }

    public function testDelete()
    {
        $expected =
            "@prefix rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "@prefix rdfs:<http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "@prefix foaf:<http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE {" . PHP_EOL .
            "\t<http://www.w3.org/People/Berners-Lee/> ?p ?v" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "http://www.w3.org/People/Berners-Lee/", "?p", "?v" )
            ->buildSparqlUpdate();

        $this->assertEquals( $expected, $query );
    }

    public function testInsertWhere()
    {
        $expected =
            "@prefix rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "@prefix rdfs:<http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "@prefix foaf:<http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "INSERT {" . PHP_EOL .
            "\t?s foaf:friend <http://www.w3.org/People/Berners-Lee/>" . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "\t?s foaf:name \"John Lennon\"" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addInsert( "?s", "foaf:friend", "http://www.w3.org/People/Berners-Lee/" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlUpdate();

        $this->assertEquals( $expected, $query );
    }

    public function testDeleteWhere()
    {
        $expected =
            "@prefix rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "@prefix rdfs:<http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "@prefix foaf:<http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE {" . PHP_EOL .
            "\t?s ?p ?v" . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "\t?s foaf:name \"John Lennon\"" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "?s", "?p", "?v" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlUpdate();

        $this->assertEquals( $expected, $query );
    }

    public function testDeleteInsertWhere()
    {
        $expected =
            "@prefix rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "@prefix rdfs:<http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "@prefix foaf:<http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE {" . PHP_EOL .
            "\t?s foaf:name ?name" . PHP_EOL .
            "}" . PHP_EOL .
            "INSERT {" . PHP_EOL .
            "\t?s foaf:name \"J. Lennon\"" . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "\t?s foaf:name \"John Lennon\"" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "?s", "foaf:name", "?name" )
            ->addInsert( "?s", "foaf:name", "J. Lennon" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlUpdate();

        $this->assertEquals( $expected, $query );
    }

}
