<?php

namespace Webforge\Setup;

use Psc\System\File;

class ApplicationStorageTest extends \Webforge\Code\Test\Base {
  
  protected $storage;
  
  public function setUp() {
    $this->storage = new ApplicationStorage('webforge-test');
  }
  
  public function testApplicationStorageMaintainsADirectoryToReadAndWriteTo() {
    $dir = $this->storage->getDirectory();
    
    $this->assertInstanceOf('Psc\System\Dir', $dir);
    $this->assertTrue($dir->exists(), $dir.' from application storage does not exist');
    $this->assertTrue($dir->isReadable(), $dir.' from application storage cannot be read');
    $this->assertTrue($dir->isWriteable(), $dir.' from application storage cannot be written');
  }
  
  /**
   * @dataProvider badNames
   * @expectedException InvalidArgumentException
   */
  public function testApplicationStorageMustHaveANiceNameForCons($name) {
    new ApplicationStorage($name);
  }
  
  public static function badNames() {
    return Array (
      array('with spaces is not okay'),
      array('with.dots.is.not.okay'),
      array('with-Otherä-letters'),
      array('with-/-letters')
    );
  }
}
?>