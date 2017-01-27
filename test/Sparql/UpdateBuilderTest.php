<?php

namespace Tests\StardohPhp\Sparql;


use StardogPhp\Sparql\QueryBuilder;

class UpdateBuilderTest extends \PHPUnit_Framework_TestCase
{


    public function testInsert()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "INSERT DATA {" . PHP_EOL .
            "   <http://www.w3.org/People/Berners-Lee/> foaf:name \"Tim Berners Lee\" ." . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addInsert( "http://www.w3.org/People/Berners-Lee/", "foaf:name", "Tim Berners Lee" )
            ->buildSparqlQuery();

        $this->assertEquals( $expected, $query );
    }

    public function testDelete()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE DATA {" . PHP_EOL .
            "   <http://www.w3.org/People/Berners-Lee/> ?p ?v ." . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "http://www.w3.org/People/Berners-Lee/", "?p", "?v" )
            ->buildSparqlQuery();

        $this->assertEquals( $expected, $query );
    }

    public function testInsertWhere()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "INSERT {" . PHP_EOL .
            "   ?s foaf:friend <http://www.w3.org/People/Berners-Lee/> ." . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "   ?s foaf:name \"John Lennon\" ." . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addInsert( "?s", "foaf:friend", "http://www.w3.org/People/Berners-Lee/" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlQuery();

        $this->assertEquals( $expected, $query );
    }

    public function testDeleteWhere()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE {" . PHP_EOL .
            "   ?s ?p ?v ." . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "   ?s foaf:name \"John Lennon\" ." . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "?s", "?p", "?v" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlQuery();

        $this->assertEquals( $expected, $query );
    }

    public function testDeleteInsertWhere()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "DELETE {" . PHP_EOL .
            "   ?s foaf:name ?name ." . PHP_EOL .
            "}" . PHP_EOL .
            "INSERT {" . PHP_EOL .
            "   ?s foaf:name \"J. Lennon\" ." . PHP_EOL .
            "}" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "   ?s foaf:name \"John Lennon\" ." . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addDelete( "?s", "foaf:name", "?name" )
            ->addInsert( "?s", "foaf:name", "J. Lennon" )
            ->addWhere( "?s", "foaf:name", "John Lennon" )
            ->buildSparqlQuery();

        $this->assertEquals( $expected, $query );
    }

    public function testOptionalWhere()
    {
        $expected =
            "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" . PHP_EOL .
            "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>" . PHP_EOL .
            "PREFIX foaf: <http://http://xmlns.com/foaf/0.1/>" . PHP_EOL .
            "SELECT ?s ?p ?v" . PHP_EOL .
            "WHERE {" . PHP_EOL .
            "   ?s ?p ?v ." . PHP_EOL .
            "   OPTIONAL {" . PHP_EOL .
            "      ?s ?p \"Name\" ." . PHP_EOL .
            "   }" . PHP_EOL .
            "}" . PHP_EOL;
        $query = QueryBuilder::create()
            ->addPrefix( "foaf", "http://http://xmlns.com/foaf/0.1/" )
            ->addSelect( array('?s', '?p', '?v') )
            ->addWhere( '?s', '?p', '?v' )
            ->addOptionalWhere( '?s', '?p', 'Name' )
            ->buildSparqlQuery();
        $this->assertEquals( $expected, $query );
    }

}
