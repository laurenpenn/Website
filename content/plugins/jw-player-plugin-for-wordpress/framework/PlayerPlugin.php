<?php

/**
 * A convenience object for storing information about a plugin.
 * @file Class definition for Plugin
 */
class Plugin
{
  private $title;
  private $version;
  private $repository;
  private $description;
  private $flash_vars;
  private $file_name;
  private $enabled;
  private $page;

  /**
   * Constructor.
   * @param string $name The natural language name of the plugin
   * @param string $vers The version number of the plugin
   * @param string $repo The LongTail repository name for the plugin
   * @param string $file The filename of the plugin
   * @param boolean $enable Whether a plugin is enabled or not.  Used in conjunction with a Player configuration.
   * @param string $desc The description of the plugin's functionality
   * @param array $fVars The array of flashvars associated with this plugin
   * @param string $link The link to the plugin's homepage
   */
  function __construct($name = "", $vers = "", $repo = "", $file = "", $enable = false, $desc = "", $f_vars = array(), $link) {
    $this->title = $name;
    $this->version = $vers;
    $this->repository = $repo;
    $this->file_name = $file;
    $this->description = $desc;
    $this->flash_vars = $f_vars;
    $this->enabled = $enable;
    $this->page = $link;
  }

  /**
   * Get the title of the plugin.  This is the natural language name.
   * @return string The title of plugin
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Set the title of the plugin.  This is the natural language name.
   * @param string $newTitle The new title of the plugin
   */
  public function setTitle($new_title) {
    $this->title = $new_title;
  }

  /**
   * Get the version of the plugin.
   * @return string The version of the plugin
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * Set the version of the plugin.
   * @param string $newVersion The new version of the plugin
   */
  public function setVersion($new_version) {
    $this->version = $new_version;
  }

  /**
   * Get the description of the plugin.
   * @return string The description of the plugin
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set the description of the plugin.
   * @param string $newDescription The new description of the plugin
   */
  public function setDescription($new_description) {
    $this->description = $new_description;
  }

  /**
   * Get the list of flashvars for the plugin.
   * @return array An array of FlashVar objects associated with the plugin
   */
  public function getFlashVars() {
    return $this->flash_vars;
  }

  /**
   * Set the list of flashvars for the plugin.
   * @param array $newFlashVars The new array of FlashVar objects to be associated with the plugin
   */
  public function setFlashVars($new_flash_vars) {
    $this->flash_vars = $new_flash_vars;
  }

  /**
   * Get the LongTail repository name of the plugin.
   * @return string The LongTail repository name of the plugin
   */
  public function getRepository() {
    return $this->repository;
  }

  /**
   * Set the LongTail repository name of the plugin.
   * @param string $newRepository The new LongTail repository name of the plugin
   */
  public function setRepository($new_repository) {
    $this->repository = $new_repository;
  }

  /**
   * Get the file name of the plugin.
   * @return string The file name of the plugin
   */
  public function getFileName() {
    return $this->file_name;
  }

  /**
   * Set the file name of the plugin.
   * @param string $newFileName The new file name of the plugin
   */
  public function setFileName($new_file_name) {
    $this->file_name = $new_file_name;
  }

  /**
   * Check if the plugin is enabled.
   * @return string The enabled state of the plugin
   */
  public function isEnabled() {
    return $this->enabled;
  }

  /**
   * Set the enabled state of the plugin.
   * @param string $enable The new enabled state of the plugin
   */
  public function setEnabled($enable) {
    $this->enabled = $enable;
  }

  /**
   * Get the plugin prefix for flashvars
   * @return string The plugin prefix
   */
  public function getPluginPrefix() {
    return str_replace(".swf", "", $this->getFileName());
  }

  /**
   * Get the home page for the plugin.
   * @return string The home page for the plugin
   */
  public function getPage() {
    return $this->page;
  }

  /**
   * SEt the home page for the plugin.
   * @param string $link The new home page for the plugin
   */
  public function setPage($link) {
    $this->page = $link;
  }
}

?>
