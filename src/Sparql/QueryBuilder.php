<?php

namespace StardogPhp\Sparql;

class QueryBuilder
{

    const INDENT_LVL_1 = '   ';
    const INDENT_LVL_2 = '      ';
    const DEFAULT_PREFIXES = array(
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#'
    );

    private $prefixes;
    private $deleteTriples;
    private $select;
    private $insertTriples;
    private $whereTriples;
    private $optionalWhereTriples;
    private $isDelete;
    private $isInsert;
    private $isWhere;

    public static function create()
    {
        return new QueryBuilder();
    }

    public function __construct()
    {
        $this->prefixes = static::DEFAULT_PREFIXES;
        $this->deleteTriples = array();
        $this->insertTriples = array();
        $this->whereTriples = array();
        $this->optionalWhereTriples = array();
    }

    public function addPrefix($prefix, $namespace)
    {
        $this->prefixes[ $prefix ] = $namespace;
        return $this;
    }

    public function delete()
    {
        $this->deleteTriples = array();
        $this->isDelete = true;
        return $this;
    }

    public function addDelete($subject, $predicate, $value)
    {
        $this->deleteTriples[] = array($subject, $predicate, $value);
        $this->isDelete = true;
        return $this;
    }

    public function addInsert($subject, $predicate, $value)
    {
        $this->insertTriples[] = array($subject, $predicate, $value);
        $this->isInsert = true;
        return $this;
    }

    public function addSelect(array $items)
    {
        $this->select = $items;
        return $this;
    }

    public function addWhere($subject, $predicate, $value)
    {
        $this->whereTriples[] = array($subject, $predicate, $value);
        $this->isWhere = true;
        return $this;
    }

    public function addOptionalWhere($subject, $predicate, $value)
    {
        $this->optionalWhereTriples[] = array($subject, $predicate, $value);
        $this->isWhere = true;
        return $this;
    }

    public function buildSparqlQuery()
    {
        $sparql = '';
        if ( count( $this->prefixes ) > 0 ) {
            foreach ( $this->prefixes as $prefix => $namespace ) {
                $namespace = $value = $this->normalizeUri( $namespace );
                $sparql .= "PREFIX $prefix: $namespace" . PHP_EOL;
            }
        }
        if ( empty(!$this->select) ) {
            $sparql .= "SELECT " . implode( ' ', $this->select ) . PHP_EOL;
        } else {
            if ( $this->isDelete ) {
                if ( count( $this->deleteTriples ) > 0 ) {
                    $sparql .= (count( $this->whereTriples ) > 0 ? 'DELETE {' : 'DELETE DATA {') . PHP_EOL;
                    foreach ( $this->deleteTriples as $triple ) {
                        $subject = $this->normalizeSubject( $triple[ 0 ] );
                        $predicate = $triple[ 1 ];
                        $value = $this->normalizeValue( $triple[ 2 ] );
                        $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
                    }
                    $sparql .= '}' . PHP_EOL;
                } else {
                    $sparql .= 'DELETE ';
                }
            }
            if ( $this->isInsert ) {
                if ( count( $this->insertTriples ) > 0 ) {
                    $sparql .= (count( $this->whereTriples ) > 0 ? 'INSERT {' : 'INSERT DATA {') . PHP_EOL;
                    foreach ( $this->insertTriples as $triple ) {
                        $subject = $this->normalizeSubject( $triple[ 0 ] );
                        $predicate = $triple[ 1 ];
                        $value = $this->normalizeValue( $triple[ 2 ] );
                        $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
                    }
                    $sparql .= '}' . PHP_EOL;
                }
            }
        }
        if ( $this->isWhere ) {
            $sparql .= 'WHERE {' . PHP_EOL;
            if ( count( $this->whereTriples ) > 0 ) {
                foreach ( $this->whereTriples as $triple ) {
                    $subject = $this->normalizeSubject( $triple[ 0 ] );
                    $predicate = $triple[ 1 ];
                    $value = $this->normalizeValue( $triple[ 2 ] );
                    $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
                }
            }
            if ( count( $this->optionalWhereTriples ) > 0 ) {
                $sparql .= static::INDENT_LVL_1 . "OPTIONAL {" . PHP_EOL;
                foreach ( $this->optionalWhereTriples as $triple ) {
                    $subject = $this->normalizeSubject( $triple[ 0 ] );
                    $predicate = $triple[ 1 ];
                    $value = $this->normalizeValue( $triple[ 2 ] );
                    $sparql .= static::INDENT_LVL_2 . "$subject $predicate $value ." . PHP_EOL;
                }
                $sparql .= static::INDENT_LVL_1 . "}" . PHP_EOL;
            }
            $sparql .= '}' . PHP_EOL;
        }
        return $sparql;
    }

    private function normalizeSubject($value)
    {
        if ( strpos( $value, 'http://' ) === false ) {
            return $value;
        } else {
            return $this->normalizeUri( $value );
        }
    }

    private function normalizeValue($value)
    {
        if ( strpos( $value, 'http://' ) !== false ) {
            return $this->normalizeUri( $value );
        } else if ( strpos( $value, '?' ) === 0 ) {
            return $value;
        } else {
            return '"' . $value . '"';
        }
    }

    private function normalizeUri($uri)
    {
        return "<" . $uri . ">";
    }

}