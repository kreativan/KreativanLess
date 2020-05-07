<?php
/**
 *  KreativanLess Config
 *
 *  @author Ivan Milincic <kreativan@outlook.com>
 *  @copyright 2020 kraetivan.net
 *  @link http://www.kraetivan.net
 *
*/

class KreativanLessConfig extends ModuleConfig {

	public function getInputfields() {
		$inputfields = parent::getInputfields();
		$wrapper = new InputfieldWrapper();

		/* ----------------------------------------------------------------
			Options
		------------------------------------------------------------------- */

		$options = $this->wire('modules')->get("InputfieldFieldset");
		$options->label = "Options";
		//$options->collapsed = 1;
		$options->icon = "fa-toggle-on";
		$wrapper->add($options);

		$f = $this->wire('modules')->get("InputfieldText");
		$f->attr('name', 'css_prefix');
		$f->label = "CSS file prefix";
		$f->value = "less";
		$f->required = true;
		$f->columnWidth = "33%";
		$f->description = "Change the prefix to bust the browser cache and force parsing on next page load.";
		$options->add($f);

		// cache_buster
		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'auto_cache_buster');
		$f->label = 'Auto browser cache buster';
		$f->options = array(
			'1' => "Yes",
			'2' => "No"
		);
		$f->required = true;
		$f->defaultValue = "2";
		$f->optionColumns = 1;
		$f->columnWidth = "33%";
		$f->description = "Bust browser cache automatically. This will change css prefix every time less is compiled.";
		$options->add($f);

		// minify_css
		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'minify_css');
		$f->label = 'Minify CSS';
		$f->options = array(
			'1' => "Yes",
			'2' => "No"
		);
		$f->required = true;
		$f->defaultValue = "1";
		$f->optionColumns = 1;
		$f->columnWidth = "33%";
		$f->description = "Remove comments and whitespace to generate minimized CSS files.";
		$options->add($f);

		// dev_mode
		$f = $this->wire('modules')->get("InputfieldRadios");
		$f->attr('name', 'dev_mode');
		$f->label = 'Development Mode';
		$f->options = array(
			'1' => "Yes",
			'2' => "No"
		);
		$f->required = true;
		$f->defaultValue = "2";
		$f->optionColumns = 1;
		$f->columnWidth = "100%";
		$f->description = "If enabled, **less files will be parsed on every page load**. Module is watching for changes and runs parser automatically, but if for any reason you need to force parsing, turn this option on. Just `dont forget to turn it off in production`, can affect page load speed a lot...";
		$options->add($f);

		$f = $this->wire('modules')->get("InputfieldText");
		$f->attr('name', 'timestamp');
		$f->label = "Timestamp";
		$f->columnWidth = "100%";
		$f->collapsed = "8";
		$f->description = "Last parse timestamp, used to track files changes";
		$options->add($f);

		$inputfields->add($options);

		/* ----------------------------------------------------------------
			About
		------------------------------------------------------------------- */

		$about = $this->wire('modules')->get("InputfieldFieldset");
		$about->label = "About";
		//$options->collapsed = 1;
		$about->icon = "fa-info";
		$wrapper->add($about);

		$html_single = '<link rel="stylesheet" type="text/css" href="<?=  $modules->get("KreativanLess")->getCssFile($less_file); ?>">';
		$html_single = htmlspecialchars($html_single);

		$html_multi = '<link rel="stylesheet" type="text/css" href="<?=  $modules->get("KreativanLess")->getCssFile($array); ?>';
		$html_multi = htmlspecialchars($html_multi);

		$html_less = '<link rel="stylesheet" type="text/css" href="<?=  $modules->get("KreativanLess")->getCssFile($array, $less_string); ?>';
		$html_less = htmlspecialchars($html_less);

		$f = $this->wire('modules')->get("InputfieldMarkup");
		//$f->attr('name', 'about');
		$f->label = "How to use";
		$f->value = "
			<p>Use <code>\$this->getCssFile(\$less_files, \$less_string)</code> method to compile your less files and get css file url.<br />
			Can parse single or multiple files in array. You can also pass additional less code as a string to the method...</p>
			<p>
				<code>
					\$less_string = '@tm-primary-bg: blue;';<br />
					\$less_file = 'templates/less/less_file.less'; <br />
					\$array = [<br />
						'templates/less/file-1.less',<br />
						'templates/less/file-2.less',<br />
						'templates/less/file-3.less',<br />
					];
				</code>
			</p>
			<p>
				<span class='uk-form-label'>Single file:</span> <br />
				<code>$html_single</code><br />
				<span class='uk-form-label'>Array of files:</span> <br />
				<code>$html_multi</code><br />
				<span class='uk-form-label'>Files + Additional less code...</span> <br />
				<code>$html_less</code>
			</p>
		";
		$about->add($f);

		$inputfields->add($about);


		// Render
		// ========================================================================
		return $inputfields;

	}

}
