<?php

/**
 * Twig Integration for the Contao OpenSource CMS
 *
 * @package ContaoTwig
 * @link    https://github.com/InfinitySoft/contao-twig SCM
 * @link    http://de.contaowiki.org/Twig Wiki
 * @author  Tristan Lins <tristan.lins@infinitysoft.de>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Class TwigHelper
 *
 * A helper class with some functions.
 *
 * @package ContaoTwig
 * @author  Tristan Lins <tristan.lins@infinitysoft.de>
 */
class TwigHelper extends Controller
{
    /**
     * Singleton instance.
     *
     * @var TwigHelper
     */
    protected static $objInstance = null;

    /**
     * Get the singleton instance.
     *
     * @static
     * @return TwigHelper
     */
    public static function getInstance() {
        if (self::$objInstance === null) {
            self::$objInstance = new TwigHelper();
        }
        return self::$objInstance;
    }

	/**
	 * Return all template files of a particular group as array
	 * @param string
	 * @param integer
	 * @return array
	 * @throws Exception
	 */
	public function getTemplateGroup($strPrefix, $intTheme=0)
	{
        $this->import('Database');

		$arrFolders = array();
		$arrTemplates = array();

		// Add the templates root directory
		$arrFolders[] = TL_ROOT . '/templates';

		// Add the theme templates folder
		if ($intTheme > 0)
		{
			$objTheme = $this->Database->prepare("SELECT templates FROM tl_theme WHERE id=?")
									   ->limit(1)
									   ->execute($intTheme);

			if ($objTheme->numRows > 0 && $objTheme->templates != '')
			{
				$arrFolders[] = TL_ROOT .'/'. $objTheme->templates;
			}
		}

		// Add the module templates folders if they exist
		foreach ($this->Config->getActiveModules() as $strModule)
		{
			$strFolder = TL_ROOT . '/system/modules/' . $strModule . '/templates';

			if (is_dir($strFolder))
			{
				$arrFolders[] = $strFolder;
			}
		}

		// Find all matching templates
		foreach ($arrFolders as $strFolder)
		{
			$arrFiles = preg_grep('/^' . preg_quote($strPrefix, '/') . '.*\.twig$/i',  scan($strFolder));

			foreach ($arrFiles as $strTemplate)
			{
				$strName = basename($strTemplate);
				$arrTemplates[] = preg_replace('#\.[^\.]+\.twig$#', '', $strName);
			}
		}

		natcasesort($arrTemplates);
		$arrTemplates = array_values(array_unique($arrTemplates));

		return $arrTemplates;
	}
}