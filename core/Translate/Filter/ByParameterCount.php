<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik
 * @package Piwik
 */

namespace Piwik\Translate\Filter;

use Piwik\Translate\Filter\FilterAbstract;

/**
 * @package Piwik
 * @subpackage Piwik_Translate
 */
class ByParameterCount extends FilterAbstract
{
    /**
     * Filter the given translations
     *
     * @param array $translations
     *
     * @return array   filtered translations
     *
     */
    public function filter($translations)
    {
        $cleanedTranslations = array();

        foreach ($translations AS $pluginName => $pluginTranslations) {

            foreach ($pluginTranslations AS $key => $translation) {

                if (isset($this->_baseTranslations[$pluginName][$key])) {
                    $baseTranslation  = $this->_baseTranslations[$pluginName][$key];
                } else {
                    $baseTranslation = '';
                }

                // ensure that translated strings have the same number of %s as the english source strings
                $baseCount        = $this->_getParametersCountToReplace($baseTranslation);
                $translationCount = $this->_getParametersCountToReplace($translation);

                if ($baseCount != $translationCount)  {

                    $this->_filteredData[$pluginName][$key] = $translation;
                    continue;
                }

                $cleanedTranslations[$pluginName][$key] = $translation;
            }
        }

        return $cleanedTranslations;
    }

    /**
     * Counts the placeholder parameters n given string
     *
     * @param string $string
     * @return array
     */
    protected  function _getParametersCountToReplace($string)
    {
        $sprintfParameters = array('%s', '%1$s', '%2$s', '%3$s', '%4$s', '%5$s', '%6$s', '%7$s', '%8$s', '%9$s');
        $count = array();
        foreach ($sprintfParameters as $parameter) {

            $placeholderCount = substr_count($string, $parameter);
            if ($placeholderCount > 0) {

                $count[$parameter] = $placeholderCount;
            }
        }
        return $count;
    }
}