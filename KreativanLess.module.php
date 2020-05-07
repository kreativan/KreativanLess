<?php
/**
 *  KreativanLess Module
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *
 *  @see https://github.com/wikimedia/less.php
 *
*/

class KreativanLess extends WireData implements Module {

  public static function getModuleInfo() {
    return array(
      'title' => 'Kreativan Less',
      'version' => 100,
      'summary' => 'Less php compiler...',
      'icon' => 'css3',
      'autoload' => false
    );
  }

  public function __construct() {
    $this->folder     = $this->config->paths->assets . "kreativan-less/";
    $this->folderUrl  = $this->config->urls->assets . "kreativan-less/";
  }

  public function init() {
    // Do something on init
  }

  /**
   *  Use this method to get the compiled css file
   *  @param array|string $less_files - single or array of less files (full path)
   *  @param string $less - additional less code to compile as string
   *  @return string will return file url
   *  @example $this->getCssFile($my_less_file, "@primary-bg: blue;")
   */
  public function getCssFile($less_files, $less = "") {
    // existing files
    $file_path  = "{$this->folder}{$this->css_prefix}_main.css";
    $file_url = "{$this->folderUrl}{$this->css_prefix}_main.css";
    // if something changed compile and return new file, if not just return old one
    if(!file_exists($file_path) || $this->dev_mode == "1" || $this->fileIsChanged($less_files)) {
      return $this->compile($less_files);
    } else {
      return $file_url;
    }
  }

  /**
   *  Compile less file(s)
   *  get the css data from @see $this->getCssData()
   *  and put it in to a file
   *  also clear cache, set timestamp and change css_prefix if auto_cache_buster is on
   */
  public function compile($less_files, $less = "") {

    // clear cache
    $this->clearCache();

    // generate random css prefix and se the timestamp
    $rand = rand(100, 1000);
    $prefix = $this->auto_cache_buster == "1" ? "less_$rand" : $this->css_prefix;

    $settings = [
      "css_prefix" => $prefix,
      "timestamp" => time(),
    ];
    $old_data = $this->modules->getModuleConfigData($this->className());
    $data = array_merge($old_data, $settings);
    $this->modules->saveConfig($this->className(), $data);

    // put tthe compiled data to the css file
    $file_path  = $this->folder . "{$prefix}_main.css";
    $css = $this->getCssData($less_files);
    if(!is_dir($this->folder)) $this->files->mkdir($this->folder);
    $this->files->filePutContents($file_path, $css);

    // d("compiled");

    // return new compiled
    return $this->folderUrl . "{$prefix}_main.css";

  }

  /**
   *  Parse Less from files and get CSS data as string.
   *  @param array|string $less_files
   *  @param string $less - add additional less code as string
   *  @return string
   */
  public function getCssData($less_files, $less = "") {

    // load less.php if it is not already loaded
    // a simple require_once does not work properly
    if(!class_exists('Less_Parser')) require_once("less.php/lib/Less/Autoloader.php");
    Less_Autoloader::register();

    try {

      $root_url = "http://" . $this->config->httpHost . $this->config->urls->root;

      $parser_options = [
        'compress'=> $this->minify_css == "1" ? true : false,
      ];

      $parser = new Less_Parser($parser_options);

      // If it's arrray parse all the files
      if(is_array($less_files)) {
        foreach($less_files as $file) $parser->parseFile($file, $root_url);
      } else {
        $parser->parseFile($less_files, $root_url);
      }

      // parse custom string if exists
      if($less != "") $parser->parse($less);

      // Get the css
      $css = $parser->getCss();

      return $css;

    } catch(Exception $e) {

      $error_message = $e->getMessage();
      throw new WireException($error_message);

    }

  }

  // Clear cache
  // just delete target folder...
  public function clearCache() {
    $this->files->rmdir($this->folder, true);
  }

  // Check if file is chnaged
  // If file timestamp is bigger then saved one, it's changed...
  public function fileIsChanged($files) {
    $isChnaged = false;
    if(is_array($files)) {
      foreach($files as $f) if (filemtime($f) > $this->timestamp) $isChnaged = true;
    } else {
      if (filemtime($file) > $this->timestamp) $isChnaged = true;
    }
    return $isChnaged;
  }

}
