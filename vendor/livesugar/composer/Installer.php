<?php
namespace livesugar\composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;

class Installer extends LibraryInstaller {

  public function install(InstalledRepositoryInterface $repo, PackageInterface $package) {
    parent::install($repo, $package);

    // LiveSugar Framework
    if($package->getName() == 'livesugar/framework'){
      $mainDir = $this->vendorDir.'/../';
      $packDir = $this->vendorDir.'/'.$package->getName(); 
      if(!is_dir($mainDir.'/apps')) rename($packDir.'/apps',$mainDir.'/apps');
      if(!is_dir($mainDir.'/http'))rename($packDir.'/http',$mainDir.'/http');
    }
  }

}
?>
