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
    private $deletes;
    private $select;
    private $inserts;
    private $wheres;
    private $optionalWheres;

    public static function create()
    {
        return new QueryBuilder();
    }

    public function __construct()
    {
        $this->prefixes = static::DEFAULT_PREFIXES;
        $this->deletes = array();
        $this->inserts = array();
        $this->wheres = array();
        $this->optionalWheres = array();
    }

    public function addPrefix($prefix, $namespace)
    {
        $this->prefixes[ $prefix ] = $namespace;
        return $this;
    }

    public function addDelete($subject, $predicate, $value)
    {
        $this->deletes[] = array($subject, $predicate, $value);
        return $this;
    }

    public function addInsert($subject, $predicate, $value)
    {
        $this->inserts[] = array($subject, $predicate, $value);
        return $this;
    }

    public function addSelect(array $items)
    {
        $this->select = $items;
        return $this;
    }

    public function addWhere($subject, $predicate, $value)
    {
        $this->wheres[] = array($subject, $predicate, $value);
        return $this;
    }

    public function addOptionalWhere($subject, $predicate, $value)
    {
        $this->optionalWheres[] = array($subject, $predicate, $value);
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
            if ( count( $this->deletes ) > 0 ) {
                $sparql .= (count( $this->wheres ) > 0 ? 'DELETE {' : 'DELETE DATA {') . PHP_EOL;
                foreach ( $this->deletes as $triple ) {
                    $subject = $this->normalizeSubject( $triple[ 0 ] );
                    $predicate = $triple[ 1 ];
                    $value = $this->normalizeValue( $triple[ 2 ] );
                    $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
                }
                $sparql .= '}' . PHP_EOL;
            }
            if ( count( $this->inserts ) > 0 ) {
                $sparql .= (count( $this->wheres ) > 0 ? 'INSERT {' : 'INSERT DATA {') . PHP_EOL;
                foreach ( $this->inserts as $triple ) {
                    $subject = $this->normalizeSubject( $triple[ 0 ] );
                    $predicate = $triple[ 1 ];
                    $value = $this->normalizeValue( $triple[ 2 ] );
                    $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
                }
                $sparql .= '}' . PHP_EOL;
            }
        }
        if ( count( $this->wheres ) > 0 ) {
            $sparql .= 'WHERE {' . PHP_EOL;
            foreach ( $this->wheres as $triple ) {
                $subject = $this->normalizeSubject( $triple[ 0 ] );
                $predicate = $triple[ 1 ];
                $value = $this->normalizeValue( $triple[ 2 ] );
                $sparql .= static::INDENT_LVL_1 . "$subject $predicate $value ." . PHP_EOL;
            }
            if ( count( $this->optionalWheres ) > 0 ) {
                $sparql .= static::INDENT_LVL_1 . "OPTIONAL {" . PHP_EOL;
                foreach ( $this->optionalWheres as $triple ) {
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