<?php
/**
*	© newQuery - 2017
*	@author Thomas Wilmshorst
*	@link: http://blyat.eu/
*	@version 1.0.0
*	----------------------------------------------------------------------------------------------
*	Description:
	TLC - Twig Loading Class
*	----------------------------------------------------------------------------------------------
*/


class TwigLoader
{
	/*
	*	Array: Made of the options that if not NULL will be rendered
	*/
	public $options = [];

	/*
	*	String: Template's name
	*	Without the prefix and extension
	*/
	public $tpl;

	/*
	*	String: The name of a class::method to be call during the instanciation of the class
	*/
	public $action;


	# TWIG properties

	/*
	*	Array: Made of the options that if not NULL will be rendered
	*/
	public $renders;

	/*
	*	Array: Twig template
	*/
	public $template;


	/**
	*	@param String: The template's name
	*	@param Array: Made of the options you want to be in your TemplatingArray
	*	@param String: The name of a class::method() you want to be executed right away after instanciating the class
	*/
	public function __construct(String $tpl = '', Array $renders = NULL, String $action = '')
	{
		self::loadTwig();
		if($tpl != '') self::loadTemplate($tpl);
		if($renders != NULL) self::loadRenders($renders);
		if($action != '') self::$action();
	}

	/**
	*	@param String: The title you want to be displayed between the <title></title> tags
	*	@return $this
	*/
	public function _setTitle(String $title)
	{
		$this -> options = array_merge($this -> options, array('title' => $title));
		return $this;
	}

	/**
	*	@param Array: Made of the options you want to be using in your template
	*	@return $this
	*/
	public function _setRenders(Array $renders)
	{
		if(is_array($renders) && $renders != "") $this -> options = array_merge($this -> options, $renders);
		return $this;
	}

	/**
	*	@param String: Template's name - Will be displayed in you Twig templating array and usable
	*	@return $this
	*/
	public function _setTemplateName(String $name)
	{
		if(is_string($name) && $name != "") $this -> tpl = strtolower(trim($name));
		$this -> options = array_merge($this -> options, array('tplName' => $name));
		return $this;
	}

	/**
	*	Loading Twig Environments, Templates directories, extensions
	*	@return $this
	*/
	public function loadTwig()
	{
		$loader = new Twig_Loader_Filesystem(TPL_PATHS);
		$this -> twig = new Twig_Environment($loader, array(
			'debug' => true,
		    #'cache' => '/path/to/compilation_cache',
		));
		$this -> twig -> addExtension(new Twig_Extension_Debug());
		#$this -> twig -> addExtension(new Project_Twig_Extension());

		return $this;
	}

	/**
	*	@param String: The template's name to load
	*	@return $this
	*/
	public function loadTemplate(String $tpl = '')
	{
		$template = strtolower($tpl);
		if(isset($this -> tpl) && $this -> tpl != '' && $tpl == '')
		{
			if(self::checkIfTemplateExist($tpl))
			{
				$this -> template = $this -> twig -> load('tpl_'.$this -> tpl.'.twig');
				self::_setTemplateName($this -> tpl);
			}
			else throw new Exception("Template does not exist - view Exception in Twig.class.php, method loadTemplate");
		}
		elseif($tpl != '')
		{
			if(self::checkIfTemplateExist($tpl))
			{
				$this -> template = $this -> twig -> load('tpl_'.$template.'.twig');
				self::_setTemplateName($template);
			}
			else throw new Exception("Template does not exist - view Exception in Twig.class.php, method loadTemplate");
		}

		return $this;
	}

	/**
	*	@param Array: made of the options you want to be able to use in the TWIG template (Kinda the same as _setRenders, but it also loads them)
	*	@return $this
	*/
	public function loadRenders(Array $options = NULL)
	{
		if($options != NULL && is_array($options))
		{
			$this -> options = array_merge($this -> options, $options);
			$this -> renders = $this -> options;
		}
		elseif($options == NULL && isset($this -> options) && $this -> options != NULL) $this -> renders = $this -> options;

		if($this -> renders !== NULL) $this -> renderedTemplate = true;

		return $this;
	}

	/**
	*	This method should always be the last one to be called
	*	It is meant to load and echo out the template
	*/
	public function loadFinal()
	{
		// In case you didn't use loadTemplate but _setTemplateName during routing
		if(!isset($this -> template) && isset($this -> tpl)) self::loadTemplate($this -> tpl);

		// Load les renders si ils ont été ajouté depuis _setRenders et non pas par le constructeur
		if(isset($this -> options) && is_array($this -> options) && $this -> options != NULL) self::loadRenders();

		// Rendering it
		if(isset($this -> renderedTemplate)) echo $this -> template -> render($this -> renders);
		else echo $this -> template -> render();
	}

	/**
	*	@param String: Template's name - without the prefix & extension
	*/
	private function checkIfTemplateExist($tpl)
	{
		$paths = TPL_PATHS;
		foreach ($paths as $path)
		{
			if(file_exists($path.'/tpl_'.$tpl.'.twig'))
			{
				return true;
				break;
			}
		}
		return false;
	}
}
