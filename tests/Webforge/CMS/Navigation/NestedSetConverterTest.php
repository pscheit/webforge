<?php

namespace Webforge\CMS\Navigation;

/**
 */
class NestedSetConverterTest extends \Webforge\Code\Test\Base {
  
  protected $nestedSetConverter, $food;
  
  public function setUp() {
    parent::setUp();
    $this->nestedSetConverter = new NestedSetConverter();
  }
  
  public static function getFixtures() {
    return Array(
      array(new \Webforge\TestData\NestedSet\FoodCategories()),
      array(new \Webforge\TestData\NestedSet\Consumables()),
      array(new \Webforge\TestData\NestedSet\Hgdrn()),
    );
  }

  public static function getStructureFixtures() {
    return Array(
      array(new \Webforge\TestData\NestedSet\Hgdrn()),
    );
  }
  
  /**
   * @dataProvider getFixtures
   */
  public function testConversionFromParentPointerToNestedSetFlatArray($fixture) {
    $this->assertEquals(
      $fixture->toArray(),
      $this->unwrap($this->nestedSetConverter->fromParentPointer($this->wrap($fixture->toParentPointerArray())))
    );
  }
  
  public function testConversionFromParentPointerWithSingleRoot() {
    $this->assertEquals(
      Array ( array('title'=>'root', 'lft'=>1, 'rgt' =>2, 'depth'=>0)),
      $this->unwrap($this->nestedSetConverter->fromParentPointer($this->wrap(Array( array('title'=>'root', 'parent'=>NULL, 'depth'=>0)))))
    );
  }

  
  /**
   * @dataProvider getFixtures
   */
  public function testHTMLStructureEqualsConvertedHTMLList($fixture) {
    //$this->assertXmlStringEqualsXmlString(
    $this->assertEquals( // our function is whitespace-safe, so we can use assertEquals instead of XmlStringEquals
      $h1 = $fixture->toHTMLList(),
      $h2 = $this->nestedSetConverter->toHTMLList($this->wrap($fixture->toArray())),
      sprintf("\n<<<expected\n%s\n\n<<<actual\n%s\n", $h1, $h2)
    );
  }

  /**
   * @dataProvider getFixtures
   */
  public function testTextConversionFromFlatArrayToString($fixture) {
    $this->assertEquals(
      $fixture->toString(),
      $this->nestedSetConverter->toString($this->wrap($fixture->toArray()))
    );
  }

  /**
   * @dataProvider getFixtures
   */
  public function testConversionToParentPointerFromDepthFlatArray($fixture) {
    $this->assertEquals(
      $fixture->toParentPointerArray(),
      $this->unwrap(
        $this->nestedSetConverter->toParentPointer($this->wrap($fixture->toArray())), 
        'parentPointer'
      )
    );
  }

  /**
   * @dataProvider getStructureFixtures
   */
  public function testConversionToStructureFromDepthFlatArray($fixture) {
    $this->assertEquals(
      $fixture->toStructureArray(),
      $this->unwrap(
        $this->nestedSetConverter->toStructure($this->wrap($fixture->toArray())), 
        'structure'
      )
    );
  }

  public function testEmptyConversionToHTML() {
    $this->assertEquals(
      '<ul></ul>',
      $this->nestedSetConverter->toHTMLList(array())
    );
  }

  public function testForIndentFailureInToHTML() {
    $nodes = Array(
      array(
        'title' => 'Apercu',
        'parent'=> NULL,
        'depth' => 0
      ),
      array(
        'title' => 'Maroc',
        'parent'=> 'Apercu',
        'depth' => 1
      ),
      array(
        'title' => 'Forum Regional',
        'parent' => NULL,
        'depth' => 0
      ),
      array(
        'title' => 'Themes',
        'parent' => 'Forum Regional',
        'depth' => 1,
      ),
      array(
        'title' => 'Demokratie',
        'parent' => 'Themes',
        'depth' => 2,
      ),
      array(
        'title' => 'Baladiya',
        'parent' => 'Demokratie',
        'depth' => 3
      ),
      array(
        'title' => 'CEFEB',
        'parent' => 'Demokratie',
        'depth' => 3
      ),

      array(
        'title' => 'Prochains evenements',
        'parent' => NULL,
        'depth' => 0
      ),
      array(
        'title' => 'Calendrier',
        'parent' => 'Prochains evenements',
        'depth' => 1
      )
    );

    $sets = $this->nestedSetConverter->fromParentPointer($this->wrap($nodes));

    $this->nestedSetConverter->toHTMLList($sets);
  }
  
  /**
   * Converts the array nodes from the fixture into a node of the interface
   */
  protected function wrap(Array $arrayNodes) {
    $nodes = array();
    $nodesByTitle = array();
    foreach ($arrayNodes as $arrayNode) {
      if (isset($arrayNode['parent'])) {
        $arrayNode['parent'] = $nodesByTitle[ $arrayNode['parent'] ];
      }
      
      $nodes[] = $node = new SimpleNode($arrayNode);
      $nodesByTitle[$node->getTitle()] = $node;
    }
    
    return $nodes;
  }
  
  /**
   * Converts the node of the interface into an array node
   */
  protected function unwrap(Array $objectNodes, $type = NULL) {
    return array_map(function (SimpleNode $node) use ($type) {
      $node = $node->unwrap($type);

      return $node;
    }, $objectNodes);
  }
}
