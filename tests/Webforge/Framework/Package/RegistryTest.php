<?php

namespace Webforge\Framework\Package;

class RegistryTest extends \Webforge\Code\Test\Base {
  
  protected $registry;
  
  public function setUp() {
    $this->registry = new Registry();
    
    $this->registerPackage('ACME');
  }
  
  public function testFindByFQNReturnsThePackageWithTheNamespace() {
    $acmePackage = $this->registry->findByFQN('ACME\IntranetApplication\Main');
    
    $this->assertInstanceOf('Webforge\Framework\Package\Package', $acmePackage);
    $this->assertEquals('acme/intranet-application', $acmePackage->getIdentifier());
  }

  public function testNonFindableIdentifierReturnsNULL() {
    $this->assertNull($this->registry->findByIdentifier('is-not-defined/package'));
  }

  public function testfindByFQNDoesNotMatchNamespacesThatJustBeginWithTheSearch() {
    $registry = new Registry();

    // namespace is just Webforge\ but this should not find WebforgeOtherNamespace\
    $this->registerPackage('Webforge');

    $this->expectPackageNotFound();
    $this->registry->findByFQN('WebforgeOtherNamespace\ShouldNotBeFound');
  }

  public function testNonFindablePrefixFQNCannotBeFound() {
    $this->expectPackageNotFound();
    $this->registry->findByFQN('IsNotDefinedPrefix');
  }
  
  public function testFindACMEWithConflictingPackagesCannotBeResolved() {
    $acmePackage = $this->registry->findByIdentifier('acme/intranet-application');

    $acmePackage2 = clone $acmePackage;
    $acmePackage2->setSlug('intranet-application-clone');

    $this->registry->addPackage($acmePackage2);
    
    $this->expectPackageNotResolved();
    $this->registry->findByFQN('ACME\IntranetApplication\Main');
  }

  public function testFindACMEWithConflictingPackagesByFQN_whichCanBeResolvedThroughSubNamespace() {
    $registry = new Registry();
    
    // this is a project from ACME which is for the root autoloading namespace ACME\*
    // this is a libray for common things
    $this->registerPackage('ACMELibrary', $registry);
    
    // the other Package (the naming is not very nice, i know..) is for the intranet-application and has
    // the autloading root namespace ACME\IntranetApplication
    $this->registerPackage('ACME', $registry);
    
    // the library is added here first, because it would devour the namespace from ACME\IntranetApplication if not sorted
    $acmeIntranetPackage = $registry->findByFQN('ACME\IntranetApplication\Main');
    $this->assertEquals('acme/intranet-application', $acmeIntranetPackage->getIdentifier());

    $acmeLibPackage = $registry->findByFQN('ACME\Common\Util');
    $this->assertEquals('acme/library', $acmeLibPackage->getIdentifier());
  }


  public function testFindACMEWithConflictingPackagesByFQN_whichCaneBeResolvedWithQueryingTheMainNamespace() {
    $registry = new Registry();
    
    // this is a project from ACME which is for the root autoloading namespace ACME\Common\*
    $this->registerPackage('ACMELibrary', $registry);
    
    // the other Package does have its own namespace (ACME\Conflicting) but it references the ACME\Common Namespace in autoload as well
    $this->registerPackage('ACMEConflicting', $registry);
    
    $this->assertEquals('acme/library', $registry->findByFQN('ACME\Common\Util')->getIdentifier());
    $this->assertEquals('acme/conflicting', $registry->findByFQN('ACME\Conflicting\Util')->getIdentifier());
  }
  
  public function testFindByIdentifier() {
    $this->registerPackage('Webforge');
    $this->registerPackage('ACMELibrary');
    
    $this->assertSame(
      $this->registry->findByFQN('ACME\Common\Util'),
      $this->registry->findByIdentifier('acme/library'),
      'acme library package is expected to be returned'
    );
  }
  
  public function testRegistryFindsProjectFromDirectoryWhichIsChildOfAprojectDirectory() {
    $this->registerPackage('ACMELibrary');
    
    $this->assertSame(
      $this->registry->findByDirectory($this->getPackageRoot('ACMELibrary')->sub('tests/ACME')),
      $this->registry->findByIdentifier('acme/library'),
      'acme library package is expected to be returned, when find by path'
    );
  }

  public function getPackageRoot($name) {
    return $this->getTestDirectory()->sub('packages/'.$name.'/');
  }

  protected function registerPackage($name, $registry = NULL) {
    $registry = $registry ?: $this->registry;
    $registry->addComposerPackageFromDirectory($this->getPackageRoot($name));
  }

  protected function expectPackageNotFound() {
    $this->setExpectedException('Webforge\Framework\Package\PackageNotFoundException');
  }

  protected function expectPackageNotResolved() {
    $this->setExpectedException('Webforge\Framework\Package\NotResolvedException');
  }
}
